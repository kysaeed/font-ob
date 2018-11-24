<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfGlyph extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'header' => [
			'numberOfContours' => ['n', 1, true],
			'xMin' => ['n', 1, true],
			'yMin' => ['n', 1, true],
			'xMax' => ['n', 1, true],
			'yMax' => ['n', 1, true],
		],

		'description' => [
			'endPtsOfContours' => ['n', 'numberOfContours'],
			'instructionLength' => ['n', 1],
			'instructions' => ['C', 'instructionLength'],
			'flags' => ['C', 'instructionLength'],
			'xCoordinates' => [['C', 'n'], 'flagsLength', true],
			'yCoordinates' => [['C', 'n'], 'flagsLength', true],
		],
	];

	public function ttfFile()
	{
		return $this->belognsTo('FontObscure\TtfFile');
	}

	public static function createFromFile($binTtfFile, $offset, $glyphIndex)
	{
		$header = self::parseHeader($binTtfFile, $offset);
		$offset += 10;

		if ($header['numberOfContours'] < 0) {
			return null;
		}

		$description = self::parseDescription($binTtfFile, $offset, $header);

		return new TtfGlyph([
			'glyph_index' => $glyphIndex,
			'x_min' => $header['xMin'],
			'y_min' => $header['yMin'],
			'x_max' => $header['xMax'],
			'y_max' => $header['yMax'],

			'coordinates' => $description['coordinates'],
			'instructions' => $description['instructions'],

		]);
	}

	protected static function parseHeader($binGlyph, $offset)
	{
        $header = self::unpackBinData(self::FileFormat['header'], $binGlyph, $offset);
		return $header;
	}

    protected static function parseDescription($binTtfFile, $offset, $glyphHeader)
    {
        $format = self::FileFormat['description'];

		$endPtsOfContoursList = array_values(unpack("@{$offset}/{$format['endPtsOfContours'][0]}{$glyphHeader['numberOfContours']}", $binTtfFile));
        $offset += (2 * $glyphHeader['numberOfContours']);

		$instructionLength = unpack("@{$offset}/{$format['instructionLength'][0]}", $binTtfFile)[1];
        $offset += 2;

		$instructions = array_values(unpack("@{$offset}/{$format['instructions'][0]}{$instructionLength}", $binTtfFile));
        $offset += $instructionLength;

		// TODO: 定数を定義
		$ON_CURVE_POINT = (0x01 << 0);
		$X_SHORT_VECTOR = (0x01 << 1);
		$Y_SHORT_VECTOR = (0x01 << 2);
		$REPEAT_FLAG = (0x01 << 3);
		$X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR = (0x01 << 4);
		$Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR = (0x01 << 5);
		$OVERLAP_SIMPLE = (0x01 << 6);

		$pointCount = max($endPtsOfContoursList) + 1;
		$flagsList = [];

		$index = 0;
		while (count($flagsList) < $pointCount) {
			// TODO: repeatがあるのでなおす
			$flags = unpack("@{$offset}/C", $binTtfFile)[1];
            $offset++;
			$flagsList[] = $flags;
			if ($flags & $REPEAT_FLAG) {
				$repeatCount = unpack("@{$offset}/C", $binTtfFile)[1];
                $offset++;
				for ($j = 0; $j < $repeatCount; $j++) {
					$flagsList[] = $flags;
				}
			}
		}

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
