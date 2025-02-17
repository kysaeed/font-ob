<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfGlyph extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'header' => [
			'number_of_contours' => ['n', 1, true],
			'x_min' => ['n', 1, true],
			'y_min' => ['n', 1, true],
			'x_max' => ['n', 1, true],
			'y_max' => ['n', 1, true],
		],

		'description' => [
			'end_pts_of_contours' => ['n', 'number_of_contours'],
			'instruction_length' => ['n', 1],
			'instructions' => ['C', 'instruction_length'],
			'flags' => ['C', 'instruction_length'],
			'xCoordinates' => [['C', 'n'], 'flags_length', true],
			'yCoordinates' => [['C', 'n'], 'flags_length', true],
		],
	];

	const ON_CURVE_POINT = (0x01 << 0);
	const X_SHORT_VECTOR = (0x01 << 1);
	const Y_SHORT_VECTOR = (0x01 << 2);
	const REPEAT_FLAG = (0x01 << 3);
	const X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR = (0x01 << 4);
	const Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR = (0x01 << 5);
	const OVERLAP_SIMPLE = (0x01 << 6);

	public function ttfFile()
	{
		return $this->belognsTo('FontObscure\TtfFile');
	}

	public static function createFromFile($binTtfFile, $offset, $glyphIndex)
	{
		$header = self::parseHeader($binTtfFile, $offset);
		$offset += 10;

		if ($header['number_of_contours'] < 0) {
			return null;
		}

		$description = self::parseDescription($binTtfFile, $offset, $header);

		return new TtfGlyph([
			'glyph_index' => $glyphIndex,
			'x_min' => $header['x_min'],
			'y_min' => $header['y_min'],
			'x_max' => $header['x_max'],
			'y_max' => $header['y_max'],

			'coordinates' => $description['coordinates'],
			'instructions' => $description['instructions'],

		]);
	}

	public function toBinary()
	{
		$bin = '';
		$bin .= $this->toBinaryHeader();
		$bin .= $this->toBinaryDescription();

		return $bin;
	}

	protected function toBinaryHeader()
	{
		$headerParams = [
			'number_of_contours' => count($this->coordinates),
			'x_min' => $this->x_min,
			'y_min' => $this->y_min,
			'x_max' => $this->x_max,
			'y_max' => $this->y_max,
		];

		return self::packAttributes(self::FileFormat['header'], $headerParams);
	}

	protected function toBinaryDescription()
	{
		$format = self::FileFormat['description'];

		// TODO: endPtsOfContours$


		$binInstractions = self::packInstructions($this->instructions);


		$flagsList = [];
		$binX = '';
		$binY = '';
		$prevX = null;
		$prevY = null;
		$endPtsOfContours = [];
		$contoursCoutn = 0;
		foreach ($this->coordinates as $contours) {
			foreach ($contours as $c) {
// dd($c);
				$flags = ($c['flags'] & self::ON_CURVE_POINT);

				if (is_null($prevX)) {
					$x = $c['x'];
				} else {
					$x = $c['x'] - $prevX;
				}

				if (($x == 0) && !is_null($prevX)) {
					$flags |= self::X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR;
				} else {
					$typeList = $format['xCoordinates'][0];
					if (abs($x) <= 0xFF) {
						if ($x >= 0) {
							$flags |= self::X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR;
						}
						$flags |= self::X_SHORT_VECTOR;
						$binX .= pack("{$typeList[0]}", abs($x));
					} else {
						$binX .= pack("{$typeList[1]}", $x);
					}
				}
				$prevX = $c['x'];


				if (is_null($prevY)) {
					$y = $c['y'];
				} else {
					$y = $c['y'] - $prevY;
				}
				if (($y == 0) && !is_null($prevY)) {
					$flags |= self::Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR;
				} else {
					$typeList = $format['yCoordinates'][0];
					if (abs($y) <= 0xFF) {
						if ($y >= 0) {
							$flags |= self::Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR;
						}
						$flags |= self::Y_SHORT_VECTOR;
						$binY .= pack("{$typeList[0]}", abs($y));
					} else {
						$binY .= pack("{$typeList[1]}", $y);
					}
				}
				$prevY = $c['y'];


				$flagsList[] = $flags;

				$contoursCoutn++;
			}
			$endPtsOfContours[] = ($contoursCoutn - 1);
		}

		$binEndPtsOfContours = '';
		foreach ($endPtsOfContours as $end) {
			$binEndPtsOfContours .= pack($format['end_pts_of_contours'][0], $end);
		}

		$binFlags = self::packFlags($flagsList);

// dd($binX.$binY.$binFlags);
// dd(unpack('C*', $binFlags));

		return $binEndPtsOfContours.$binInstractions.$binFlags.$binX.$binY;
	}

	protected static function packFlags($flagsList)
	{
		$format = self::FileFormat['description']['flags'];

		$binFlags = '';
		$isRepeat = false;
		$flags = null;
		$prevFlags = null;
		$repeatCount = 0;

		foreach ($flagsList as $flags) {
			$flags &= (~self::REPEAT_FLAG);
			if ($isRepeat) {
				if ($flags == $prevFlags) {
					$repeatCount++;
				} else {
					$binFlags .= pack("{$format[0]}", $repeatCount);
					$isRepeat = false;
				}
			} else {
				if (!is_null($prevFlags)) {
					if ($flags == $prevFlags) {
						$isRepeat = true;
						$repeatCount = 1;
						$prevFlags |= self::REPEAT_FLAG;
					}
					$binFlags .= pack("{$format[0]}", $prevFlags);
				}
			}
			$prevFlags = $flags;
		}
		if (!$isRepeat) {
			if (!is_null($flags)) {
				$binFlags .= pack("{$format[0]}", $flags);
			}
		} else {
			$binFlags .= pack("{$format[0]}", $repeatCount);
		}
		return $binFlags;
	}

	protected static function packInstructions($instructions)
	{
		$formatLength = self::FileFormat['description']['instruction_length'];
		$formatInstructions = self::FileFormat['description']['instructions'];

		$binInstractions = pack("{$formatLength[0]}", count($instructions));
		foreach ($instructions as $i) {
			$binInstractions = pack("{$formatInstructions[0]}", count($i));
		}

		return $binInstractions;
	}

	protected static function parseHeader($binGlyph, $offset)
	{
        $header = self::unpackBinData(self::FileFormat['header'], $binGlyph, $offset);
		return $header;
	}

    protected static function parseDescription($binTtfFile, $offset, $glyphHeader)
    {
		// TODO: 定数を定義
		$ON_CURVE_POINT = (0x01 << 0);
		$X_SHORT_VECTOR = (0x01 << 1);
		$Y_SHORT_VECTOR = (0x01 << 2);
		$REPEAT_FLAG = (0x01 << 3);
		$X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR = (0x01 << 4);
		$Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR = (0x01 << 5);
		$OVERLAP_SIMPLE = (0x01 << 6);


        $format = self::FileFormat['description'];

		$endPtsOfContoursList = array_values(unpack("@{$offset}/{$format['end_pts_of_contours'][0]}{$glyphHeader['number_of_contours']}", $binTtfFile));
        $offset += (2 * $glyphHeader['number_of_contours']);

		$instructionLength = unpack("@{$offset}/{$format['instruction_length'][0]}", $binTtfFile)[1];
        $offset += 2;

		$instructions = array_values(unpack("@{$offset}/{$format['instructions'][0]}{$instructionLength}", $binTtfFile));
        $offset += $instructionLength;

		$pointCount = max($endPtsOfContoursList) + 1;

		$flagsList = self::parseDescriptionFlags($binTtfFile, $offset, $pointCount);

		$xCoordinatesList = [];
		$x = 0;
		foreach ($flagsList as $index => $flags) {
			if ($flags & $X_SHORT_VECTOR) {
				$xCoordinate = unpack("@{$offset}/C", $binTtfFile)[1];
                $offset++;
				if (!($flags & $X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR)) {
					$xCoordinate = -$xCoordinate;
				}
			} else {
				if (!($flags & $X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR)) {
					$xCoordinate = unpack("@{$offset}/n", $binTtfFile)[1];
                    $offset += 2;
					if ($xCoordinate > 0x7fff) {
						$xCoordinate = -(0x8000 - ($xCoordinate & 0x7fff));
					}
				} else {
					$xCoordinate = 0;
				}
			}

			$x += $xCoordinate;
			$xCoordinatesList[] = $x;
		}

		$yCoordinatesList = [];
		$y = 0;
		foreach ($flagsList as $index => $flags) {
			if ($flags & $Y_SHORT_VECTOR) {
				$yCoordinate = unpack("@{$offset}/C", $binTtfFile)[1];
                $offset++;
				if (!($flags & $Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR)) {
					$yCoordinate = -$yCoordinate;
				}
			} else {
				if (!($flags & $Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR)) {
					$yCoordinate = unpack("@{$offset}/n", $binTtfFile)[1];
                    $offset += 2;
					if ($yCoordinate > 0x7fff) {
						$yCoordinate = -(0x8000 - ($yCoordinate & 0x7fff));
					}
				} else {
					$yCoordinate = 0;
				}
			}
			$y += $yCoordinate;
			$yCoordinatesList[] = $y;
		}


		// $endPtsOfContoursList   <= ソートする
		$glyphCoordinatesList = [];
		$contours = [];
		$endPoint = $endPtsOfContoursList[0];
		foreach ($flagsList as $index => $flags) {
			$contours[] = [
				'x' => $xCoordinatesList[$index],
				'y' => $yCoordinatesList[$index],
				'flags' => $flags,
			];

			if ($index >= $endPoint) {
				$glyphCoordinatesList[] = $contours;
				$contours = [];
				$endPointIndex = count($glyphCoordinatesList);
				if ($endPointIndex >= count($endPtsOfContoursList)) {
					break;
				}
				$endPoint = $endPtsOfContoursList[$endPointIndex];
			}
		}

        return  [
			'instructions' => $instructions,
			'coordinates' => $glyphCoordinatesList
		];
    }

	protected static function parseDescriptionFlags($binTtfFile, &$offset, $pointCount)
	{

		$flagsList = [];

		$formatFlags = self::FileFormat['description']['flags'];

		while (count($flagsList) < $pointCount) {
			$flags = unpack("@{$offset}/C", $binTtfFile)[1];
            $offset++;
			$isRepeat = false;
			if ($flags & self::REPEAT_FLAG) {
				$isRepeat = true;
			}
			$flagsList[] = $flags;
			if ($isRepeat) {
				$repeatCount = unpack("@{$offset}/$formatFlags[0]", $binTtfFile)[1];
                $offset++;
				for ($j = 0; $j < $repeatCount; $j++) {
					$flagsList[] = $flags;
				}
			}
		}

		return $flagsList;
	}

	protected $fillable = [
		'glyph_index',
		'number_of_contours',
		'x_min',
		'y_min',
		'x_max',
		'y_max',
		'coordinates',
		'instructions',
	];

	protected $casts = [
		'coordinates' => 'json',
		'instructions' => 'json',
	];
}
