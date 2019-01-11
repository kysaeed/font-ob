<?php

namespace FontObscure\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

use FontObscure\TtfFile;
use FontObscure\TtfGlyph;
use FontObscure\TtfHorizontalMetrix;
use FontObscure\GlyphSvg;


class TestController extends Controller
{
	public function test(Request $request)
    {


		$rectangle1 = $this->getRectangle(
			['x' => 10, 'y' => 10],
			['x' => 700, 'y' => 500]
		);

		$rectangle2 = $this->getRectangle(
			['x' => 300, 'y' => 100],
			['x' => 400, 'y' => 1000]
		);

		$compoased = $this->compose($rectangle1, $rectangle2);

		$c = [];
		foreach ($compoased as $pos) {
			$c[] = [
				'x' => $pos['x'],
				'y' => $pos['y'],
				'flags' => 0x01,
			];
		}


		$glyph = new TtfGlyph([
			'glyph_index' => 0,
			'number_of_contours' => null,
			'x_min' => 0,
			'y_min' => 0,
			'x_max' => 1400,
			'y_max' => 1300,
			'coordinates' => [
				$c,
			],
			'instructions' => [],
		]);

		$hm = new TtfHorizontalMetrix([
			'advance_width' => 1200,
			'lsb' => 20,
		]);

        $glyfData = [
            'header' => [
                 "xMin" => $glyph->xMin,
                 "yMin" => $glyph->yMin,
                 "xMax" => $glyph->xMax,
                 "yMax" => $glyph->yMax,
            ],
            'coordinates' => $glyph->coordinates,
            'instructions' => $glyph->instructions,
        ];
		$gs = new GlyphSvg($glyfData, $hm);
		echo $gs->getSvg();

		$glyph = new TtfGlyph([
			'glyph_index' => 0,
			'number_of_contours' => null,
			'x_min' => 0,
			'y_min' => 0,
			'x_max' => 100,
			'y_max' => 100,
			'coordinates' => [
				[
					['x' => 500, 'y' => 1000, 'flags' => 0x00],
					['x' => 1000, 'y' => 500, 'flags' => 0x00],
					['x' => 500, 'y' => 0, 'flags' => 0x00],
					['x' => 0, 'y' => 500, 'flags' => 0x000],
				],
				[
					['x' => 500, 'y' => 700, 'flags' => 0x01],
					['x' => 300, 'y' => 500, 'flags' => 0x01],
					['x' => 500, 'y' => 300, 'flags' => 0x01],
					['x' => 700, 'y' => 500, 'flags' => 0x01],
				],
			],
			'instructions' => [],
		]);

		$hm = new TtfHorizontalMetrix([
			'advance_width' => 1200,
			'lsb' => 20,
		]);

		$glyfData = [
			'header' => [
				 'xMin' => $glyph->xMin,
				 'yMin' => $glyph->yMin,
				 'xMax' => $glyph->xMax,
				 'yMax' => $glyph->yMax,
			],
			'coordinates' => $glyph->coordinates,
			'instructions' => $glyph->instructions,
		];
		$gs = new GlyphSvg($glyfData, $hm);
		echo $gs->getSvg();


		// return '<hr />OK';


// TODO:
//
// <path d="M11,33 c16.332,-3.471 29,-9 46,-9 c20.198,0 30,8.569 30,22 c0,19.948 -15.424,32.624 -54,36" fill="none" stroke="#000000" stroke-width="2" />
//
		echo '<svg>';
		echo '<path d="M 11,33 C27.332,30.471 40,24 57,24 C77.198,24 87,32.569 87,46 C87,65.948 72.424,78.624 33,82" fill="none" stroke="#FF0000" stroke-width="1" />';
		echo '</svg>';

		echo '<svg>';
		echo '<path d="M 50,10 0,100 100,100 z" fill="none" stroke="#000000" stroke-width="1" />';
		echo '</svg>';

		$stroke = [
			[
				[ 11, 33, true],

				[ 27.332, 30.471, false],
				[ 40,24, false],
				[ 57,24, true],

				[ 77.198,24, false],
				[ 87,32.569, false],
				[ 87,46, true],

				[ 87,65.948, false],
				[ 72.424,78.624, false],
				[ 33,82, true],
			],
		];

		$s = '<svg>';
		$s .= '<path d="M 11,33 C27.332,30.471 40,24 57,24 C77.198,24 87,32.569 87,46 C87,65.948 72.424,78.624 33,82" fill="none" stroke="#FF0000" stroke-width="1" />';
		$s .= '<path d="m 11,33 C27.332,30.471 40,24 57,24 C77.198,24 87,32.569 87,46 C87,65.948 72.424,78.624 33,82" fill="none" stroke="#FF0000" stroke-width="1" />';
		$s .= '</svg>';
		$stroke = self::parseStrokeSvg($s);

		$outline = $this->getOutlineFromStroke($stroke);
		$s = '<svg>';
		foreach ($outline as $line) {
			$s .= '<path d="M ';
			foreach ($line as $l) {
				$s .= "{$l['x']},{$l['y']} ";
			}
			$s .= 'z" fill="#dddddd" stroke="#000000" stroke-width="1" />';
		}
		$s .= '</svg>';
		echo $s;
		dd($s);

		// font
		// mplus-1c-light

		// $file = Storage::disk('local')->get('strokes/font.ttf');
		// $file = Storage::disk('local')->get('strokes/Glamor-Light.ttf');
		// $file = Storage::disk('local')->get('strokes/fancyheart_regular.ttf');
		$file = Storage::disk('local')->get('strokes/mplus-1c-light.ttf');

		$ttf = TtfFile::createFromFile('test', $file);

		$charCodeList = [
			// ord('-'),
			ord('M'),
			ord('A'),
			ord('Y'),
			ord('A'),
			//
			ord('-'),

			0x307E,
			0x3084,

			ord('-'),

			ord('m'),
			ord('a'),
			ord('y'),
			ord('a'),

			ord('-'),
			//
			ord('a'),
			ord('b'),
			ord('c'),
			ord('d'),
			ord('e'),
			ord('f'),
			ord('g'),
			ord('h'),
			ord('s'),
			ord('i'),
			ord('m'),
			ord('w'),
			ord('o'),

			ord('x'),
			ord('y'),
			ord('z'),

			ord('A'),
			ord('W'),
			ord('S'),
			ord('O'),
			ord('Q'),
		];

		foreach ($charCodeList as $i => $charCode) {
			$gs = $ttf->createSvg($charCode);
			if ($gs) {
				$svg = $gs->getSvg();
				echo $svg;
			}
		}





		echo '<hr />';

		// dd($glyphIndex);

		return 'hello !';
    }

	public static function parseStrokeSvg($svg)
	{
		$stroke = [];

		$pathSvg = [];
		if (preg_match_all('/<path\s+[^\/]+\/>/', $svg, $pathSvg)) {
			foreach ($pathSvg[0] as $p) {
				$stroke[] = self::parsePathSvg($p);
			}
		}
		return $stroke;
	}

	public static function parsePathSvg($svg)
	{
		$pathParams = [];
		echo '<hr />';
		$d = [];
		if (preg_match('/\s+d=["\|\'](.[^"]*)/', $svg, $d)) {
			$pathMatches = [];
			preg_match_all('/([MmCc]\s?)?([\d.]+),([\d.]+)/', $d[1], $pathMatches);

			$offCurveCount = 0;
			$x = null;
			$y = null;
			$prevX = 0;
			$prevY = 0;
			foreach ($pathMatches[0] as $index => $svg) {

				$isOnCurvePoint = true;
				if ($offCurveCount > 0) {
					$isOnCurvePoint = false;
					$offCurveCount--;
				} else {
					$prevX = $x;
					$prevY = $y;
				}
				$x = (float)$pathMatches[2][$index];
				$y = (float)$pathMatches[3][$index];

				if ($index > 0) {
					if ($pathMatches[1][$index] == 'c') {
						$offCurveCount = 2;

						$x = $prevX + $x;
						$y = $prevY + $y;
					}
				}
				if ($pathMatches[1][$index] == 'C') {
					$offCurveCount = 2;
				}

				$pathParams[] = [
					'x' => $x,
					'y' => $y,
					'isOnCurvePoint' => $isOnCurvePoint,
				];

			}

			$isClosed = preg_match('/z/', $d[1]);

			return [
				'path' => $pathParams,
				'isClosed' => $isClosed,
			];
		}

		return [];
	}

	public function getOutlineFromStroke($stroke)
	{
		$outline = [];
		$add = 5;
		foreach ($stroke as $index => $line) {
			$outlineUp = [];
			$outlineDown = [];
			$lineCount = count($line['path']);
			$maxLineIndex = $lineCount - 1;
			foreach ($line['path'] as $index => $l) {
				$prevIndex = ($index + ($lineCount - 1)) % $lineCount;
				if ($index == 0) {
					$lNext = $line['path'][($index + 1) % $lineCount];
					$n = self::getNormal($l, $lNext);

					$up = [
						'x' => $l['x'] + ($n['x'] * $add),
						'y' => $l['y'] + ($n['y'] * $add),
						'isOnCurvePoint' => $l['isOnCurvePoint'],
					];
					$donw = [
						'x' => $l['x'] + ($n['x'] * -$add),
						'y' => $l['y'] + ($n['y'] * -$add),
						'isOnCurvePoint' => $l['isOnCurvePoint'],
					];
				} else if ($index == $maxLineIndex) {
					$lPrev = $line['path'][$prevIndex];
					$n = self::getNormal($lPrev, $l);

					$up = [
						'x' => $l['x'] + ($n['x'] * $add),
						'y' => $l['y'] + ($n['y'] * $add),
						'isOnCurvePoint' => $l['isOnCurvePoint'],
					];
					$donw = [
						'x' => $l['x'] + ($n['x'] * -$add),
						'y' => $l['y'] + ($n['y'] * -$add),
						'isOnCurvePoint' => $l['isOnCurvePoint'],
					];
				} else {
					$lPrev = $line['path'][$prevIndex];
					$lNext = $line['path'][($index + 1) % $lineCount];
					$up = self::getOutlinePoint($lPrev, $l, $lNext, $add);
					$donw = self::getOutlinePoint($lPrev, $l, $lNext, -$add);

				}
				$outlineUp[] = [
					'x' => $up['x'],
					'y' => $up['y'],
					'isOnCurvePoint' => $up['isOnCurvePoint'],
				];
				array_unshift($outlineDown, [
					'x' => $donw['x'],
					'y' => $donw['y'],
					'isOnCurvePoint' => $donw['isOnCurvePoint'],
				]);
			}
			$outline[] = array_merge($outlineUp, $outlineDown);
		}

		return $outline;
	}

	protected static function getOutlinePoint($prevPoint, $currentPoint, $nextPoint, $add)
	{
// dump($currentPoint);
		$n = self::getNormal($prevPoint, $currentPoint);

		$prevO1 = [
			'x' => $prevPoint['x'] + ($n['x'] * $add),
			'y' => $prevPoint['y'] + ($n['y'] * $add),
			'flags' => $prevPoint['isOnCurvePoint'],
		];

		$prevO2 = [
			'x' => $currentPoint['x'] + ($n['x'] * $add),
			'y' => $currentPoint['y'] + ($n['y'] * $add),
			'flags' => $currentPoint['isOnCurvePoint'],
		];
		$n = self::getNormal($currentPoint, $nextPoint);


		$o1 = [
			'x' => $currentPoint['x'] + ($n['x'] * $add),
			'y' => $currentPoint['y'] + ($n['y'] * $add),
			'flags' => $currentPoint['isOnCurvePoint'],
		];
		$o2 = [
			'x' => $nextPoint['x'] + ($n['x'] * $add),
			'y' => $nextPoint['y'] + ($n['y'] * $add),
			'flags' => $nextPoint['isOnCurvePoint'],
		];
		$dd = self::getCrossPointToOutsideOfVector([$prevO1, $prevO2], [$o1, $o2]);

		return [
			'x' => $dd['x'],
			'y' => $dd['y'],
			'isOnCurvePoint' => $currentPoint['isOnCurvePoint'],
		];
	}

	protected static function getNormal($start, $end)
	{
		$vector = [
			'x' => $end['x'] - $start['x'],
			'y' => $end['y'] - $start['y'],
		];

// TODO: 外積で大きさを取得する！！！
		$len = sqrt(($vector['x'] * $vector['x']) + ($vector['y'] * $vector['y']));

		return [
			'x' => ($vector['y'] / $len),
			'y' => -($vector['x'] / $len),
		];
	}

	public function cross(Request $request)
	{
		// $a = self::crossProduct(
		// 	[['x'=>0, 'y'=>0], ['x'=>123, 'y'=>456]],
		// 	[['x'=>0, 'y'=>0], ['x'=>100, 'y'=>77]]
		// );
		// dd($a);

		echo 'この２つの四角を足し算します<br />';

		$svg = '<svg>';

		$rectangle1 = $this->getRectangle(
			['x' => 30, 'y' => 10],
			['x' => 100, 'y' => 50]
		);
		$rectangle2 = $this->getRectangle(
			['x' => 50, 'y' => 45],
			['x' => 80, 'y' => 90]
		);

		// $rectangle1[] = ['x' => 1, 'y' => 130];

		$svg .= $this->getSvgPolygon($rectangle1);
		$svg .= $this->getSvgPolygon($rectangle2);

		$svg .= '</svg>';
		echo $svg;

		echo '<hr />';
		echo 'こうなるよ！<br />';

		$svg = '<svg>';
		$compoased = $this->compose($rectangle1, $rectangle2);
		$svg .= $this->getSvgPolygon($compoased);
		$svg .= '</svg>';
		echo $svg;


		return '<hr />OK !';
	}

	public static function crossProduct($v1, $v2)
	{
		return (
			($v1[1]['x'] - $v1[0]['x']) * ($v2[1]['y'] - $v2[0]['y']) -
			($v1[1]['y'] - $v1[0]['y']) * ($v2[1]['x'] - $v2[0]['x'])
		);
	}

	public function getRectangle($s, $e)
	{
		return [
			['x' => $s['x'], 'y' => $s['y']],
			['x' => $e['x'], 'y' => $s['y']],
			['x' => $e['x'], 'y' => $e['y']],
			['x' => $s['x'], 'y' => $e['y']],
		];
	}


	public function getCrossPoint($v1, $v2)
	{
// dump('getCrossPoint *********************');
// dump($v1);
// dump($v2);
		$a = self::crossProduct(
			[$v2[0], $v1[0]],
			$v2
		) /* / 2 */;

// dump($a);
		$b = self::crossProduct(
			$v2,
			[$v2[0], $v1[1]]
		) /* / 2 */;
// dump($b);

// dump("a={$a}, b={$b}");
		$ab = ($a + $b);
		if (!$ab) {
			return null;
		}

		$crossVectorLengthBase = ($a / $ab);
// echo('<br />v-len='.$crossVectorLengthBase.'<br />');
		if (($crossVectorLengthBase < 0) || ($crossVectorLengthBase > 1)) {
			return null;
		}

		$crossed = [
			'x' => $v1[0]['x'] + (($v1[1]['x'] - $v1[0]['x']) * $crossVectorLengthBase),
			'y' => $v1[0]['y'] + (($v1[1]['y'] - $v1[0]['y']) * $crossVectorLengthBase),
			'length' => $crossVectorLengthBase,
		];

		if (!$this->isInsideBox($v2, $crossed)) {
			return null;
		}

		return $crossed;
	}

	public static function getCrossPointToOutsideOfVector($v1, $v2)
	{
// dump('getCrossPoint *********************');
		$a = self::crossProduct(
			[$v2[0], $v1[0]],
			$v2
		);
// dump($a);
		$b = self::crossProduct(
			$v2,
			[$v2[0], $v1[1]]
		);
// dump($b);

		$ab = ($a + $b);
		if (!$ab) {
			return null;
		}

		$crossVectorLengthBase = ($a / $ab);
// echo('<br />v-len='.$crossVectorLengthBase.'<br />');
		// if (($crossVectorLengthBase < 0) || ($crossVectorLengthBase > 1)) {
		// 	return null;
		// }

		$crossed = [
			'x' => $v1[0]['x'] + (($v1[1]['x'] - $v1[0]['x']) * $crossVectorLengthBase),
			'y' => $v1[0]['y'] + (($v1[1]['y'] - $v1[0]['y']) * $crossVectorLengthBase),
			'length' => $crossVectorLengthBase,
		];

		// if (!$this->isInsideBox($v2, $crossed)) {
		// 	return null;
		// }

		return $crossed;
	}

	public function isInsideBox($box, $point)
	{
		$minX = $box[0]['x'];
		$maxX = $box[1]['x'];
		if ($minX > $maxX) {
			list($minX, $maxX) = [$maxX, $minX];
		}
		if (($minX > $point['x']) || ($maxX < $point['x'])) {
			return false;
		}

		$minY = $box[0]['y'];
		$maxY = $box[1]['y'];
		if ($minY > $maxY) {
			list($minY, $maxY) = [$maxY, $minY];
		}
		if (($minY > $point['y']) || ($maxY < $point['y'])) {
			return false;
		}

		return true;
	}

	public function getSvgPolygon($points)
	{
		$poly = '<polygon points="';
		foreach ($points as $p) {
			$poly .= "{$p['x']},{$p['y']} ";
		}
		$poly .= '" stroke="black" fill="none"/>';
		return $poly;
	}

	public function compose($base, $addition)
	{
		$composed = [];

		$count = count($base);
		for ($i = 0; $i < $count; $i++) {
			$this->addCoordinate($composed, $i, $base, $addition);
		}
		return $composed;
	}

	protected function addCoordinate(&$coordinateList, &$index, $base, $other)
	{
		$coordinateList[] = $base[($index)];

		$baseVector = [
			$base[$index],
			$base[($index + 1) % count($base)],
		];

		$crossInfo = $this->getCrossPointToShape($other, $baseVector, null);
		if (is_null($crossInfo['point'])) {
			return false;
		}
		$coordinateList[] = $crossInfo['point'];
		$addtionOffset = $crossInfo['index'] + 1;

		$count = count($other);
		$prevPoint = $crossInfo['point'];
		for ($i = 0; $i <= $count; $i++) {
			$s = $other[($i + $addtionOffset) % $count];
			$ignoreIndex = null;
			if ($i == 0) {
				$ignoreIndex = $index;
			}
			$crossPoint = $this->getCrossPointToShape($base, [$prevPoint, $s], $ignoreIndex);

			if (is_null($crossPoint['point'])) {
				$coordinateList[] = $s;
			} else {
				$coordinateList[] = $crossPoint['point'];
				$index = $crossPoint['index'];
				break;
			}

			$prevPoint = $s;
		}
		return true;
	}

	protected function getCrossPointToShape($shape, $vector, $ignoreIndex)
	{
		$count = count($shape);

		$crossPoint = null;
		$crossIndex = null;
		for ($i = 0; $i < $count; $i++) {
			$s = $shape[$i];
			$e = $shape[($i + 1) % $count];

			if ($i !== $ignoreIndex) {
				$cp = $this->getCrossPoint($vector, [$s, $e]);
				if (!is_null($cp)) {
					if (is_null($crossPoint)) {
						$crossPoint = $cp;
						$crossIndex = $i;
					} else {
						if ($cp['length'] < $crossPoint['length']) {
							$crossPoint = $cp;
							$crossIndex = $i;
						}
					}
				} else {

				}
			}
		}

		return [
			'point' => $crossPoint,
			'index' => $crossIndex,
		];
	}

}
