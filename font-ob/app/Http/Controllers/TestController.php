<?php

namespace FontObscure\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

use FontObscure\TtfFile;
use FontObscure\TtfGlyph;
use FontObscure\GlyphSvg;


class TestController extends Controller
{
	public function test(Request $request)
    {
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
			$glyphIndex = $ttf->getGlyphIndex($charCode);
// dump($glyphIndex);
			if ($glyphIndex < 0) {
				continue;
			}

			$g = TtfGlyph::where('glyph_index', $glyphIndex)->first();

			if (!$g) continue;
// dd(json_encode($g->coordinates));

			$c = json_decode($g->coordinates, true);
			$i = json_decode($g->instructions, true);
			$glyfData = [
				'header' => [
					"numberOfContours" => null,
				     "xMin" => $g->xMin,
				     "yMin" => $g->yMin,
				     "xMax" => $g->xMax,
					 "yMax" => $g->yMax,
 				],
				'endPtsOfContours' => null,
				'coordinates' => $c,
				'instructions' => $i,
			];
			$hm = $ttf->ttf['hmtx'][$glyphIndex];
			$gs = new GlyphSvg($glyfData, $hm);
			//
			$svg = $gs->getSvg();
			echo $svg;

		}

		echo '<hr />';

		// dd($glyphIndex);

		return 'hello !';
    }

	protected function foo()
	{
		$t1 = [
			['x'=> 10, 'y' => 10],
			['x'=> 100, 'y' => 10],
		];
		$t2 = [
			['x'=> 30, 'y' =>  130],
			['x'=> 30, 'y' => 40],
		];

		$p = $this->getCrossPoint($t1, $t2);
		$svg = '<svg>';
		$svg .= $this->getSvgPolygon($t1);
		$svg .= $this->getSvgPolygon($t2);
		dump($p);
		$svg .= "<circle cx=\"{$p['x']}\" cy=\"{$p['y']}\" r=\"3\" stroke='red'/>";
		$svg .= '</svg>';
		echo $svg;
	}

	public function cross(Request $request)
	{
		// $this->foo();
		// die;

		// $a = $this->crossProduct(
		// 	[['x'=>0, 'y'=>0], ['x'=>123, 'y'=>456]],
		// 	[['x'=>0, 'y'=>0], ['x'=>100, 'y'=>77]]
		// );
		// dd($a);

		echo 'この２つの四角を足し算します<br />';

		$svg = '<svg>';

		$rectangle1 = $this->getRectangle(
			['x' => 10, 'y' => 10],
			['x' => 70, 'y' => 100]
		);
		$rectangle2 = $this->getRectangle(
			['x' => 30, 'y' => 50],
			['x' => 120, 'y' => 130]
		);

		$rectangle1[] = ['x' => 0, 'y' => 150];

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
		$current = [
			'coordinates' => $base,
			'coordinatesCount' => count($base),
			'debug' => 'BASE',
		];
		$other = [
			'coordinates' => $addition,
			'coordinatesCount' => count($addition),
			'debug' => 'ADDTION',
		];


		$composed = [];

		$prevCurrent = null;
		$otherIngnoreIndex = -1;
		for ($index = 0; $index <= $current['coordinatesCount']; $index++) {
			$c = $current['coordinates'][$index % $current['coordinatesCount']];
			$current['coordinates'];

// dump("index:{$index} ({$current['debug']}) (ingnore={$otherIngnoreIndex})");

			$isCrossed = false;
			if ($prevCurrent) {
				$prevOther = 0;
				for ($otherIndex = 0; $otherIndex <= $other['coordinatesCount'];  $otherIndex++) {
					$o = $other['coordinates'][$otherIndex % $other['coordinatesCount']];
					if ($prevOther) {
						if ($otherIndex != $otherIngnoreIndex) {
// dump("hit check to = {$otherIndex}");
							$cp = $this->getCrossPoint([$prevCurrent, $c], [$prevOther, $o]);
// dump(compact('cp'));
							if ($cp) {
// dump('cross!!!');
								$composed[] = $cp;
								$prevCurrent = $cp;

								$otherIngnoreIndex = $index;
								list($current, $other) = [$other, $current];
								$index = $otherIndex - 1;
								$isCrossed = true;
								break;
							}
						}
					}

					// dump($prevCurrent);
					// dump($cp);
					$prevOther = $o;
				}
			}

			if (!$isCrossed) {
				$composed[] = $c;
				$prevCurrent = $c;
				$otherIngnoreIndex = -1;
			}
		}


		return $composed;
	}
}
