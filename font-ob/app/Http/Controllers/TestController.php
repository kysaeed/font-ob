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


		// return '<hr />OK';




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

	public function cross(Request $request)
	{
		// $a = $this->crossProduct(
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

	public function crossProduct($v1, $v2)
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
		$a = $this->crossProduct(
			[$v2[0], $v1[0]],
			$v2
		) /* / 2 */;

// dump($a);
		$b = $this->crossProduct(
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
