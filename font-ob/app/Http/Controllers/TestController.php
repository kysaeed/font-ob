<?php

namespace FontObscure\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

use FontObscure\TffFile;
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

		$ttf = new TffFile($file);

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
// echo var_dump(); die;
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

	public function cross(Request $request)
	{
		$v1 = [
			['x'=> 10, 'y'=>10],
			['x'=> 20, 'y'=>20],
		];
		$v2 = [
			['x'=> 20, 'y'=>10],
			['x'=> 10, 'y'=>20],
		];

		$a = $this->crossProduct(
				$v2,
				[$v2[0], $v1[0]]
		) / 2;
		dump($a);

		$b = $this->crossProduct(
				$v2,
				[$v1[1], $v2[0]]
		) / 2;
		dump($b);

		$crossVectorLengthBase = $a / ($a + $b);

		$r = [
			[
				'x' => $v1[0]['x'],
				'y' => $v1[0]['y'],
			],
			[
				'x' => $v1[0]['x'] + (($v1[1]['x'] - $v1[0]['x']) * $crossVectorLengthBase),
				'y' => $v1[0]['y'] + (($v1[1]['y'] - $v1[0]['y']) * $crossVectorLengthBase),
			],
		];

		dump($crossVectorLengthBase);
		dump($r);

		return 'cross !';
	}

	public function crossProduct($v1, $v2)
	{
		return (
			($v1[1]['x'] - $v1[0]['x']) * ($v2[1]['y'] - $v2[0]['y']) -
			($v1[1]['y'] - $v1[0]['y']) * ($v2[1]['x'] - $v2[0]['x'])
		);

	}
}
