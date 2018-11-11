<?php

namespace FontObscure\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

use FontObscure\TffFile;
use FontObscure\GlyphSvg;


class TestController extends Controller
{
	public function test(Request $request)
    {
		// font
		// mplus-1c-light

		$file = Storage::disk('local')->get('strokes/font.ttf');
		// $file = Storage::disk('local')->get('strokes/Glamor-Light.ttf');
		// $file = Storage::disk('local')->get('strokes/fancyheart_regular.ttf');
		// $file = Storage::disk('local')->get('strokes/mplus-1c-light.ttf');

		$ttf = new TffFile($file);

		$charCodeList = [
			// ord('-'),

			ord('M'),
			ord('A'),
			ord('Y'),
			ord('A'),
			//
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

		// for ($glyphIndex = 0; $glyphIndex < 10; $glyphIndex++) {
		// 	$glyfData = $ttf->ttf['glyphList'][$glyphIndex];
		// 	$hm = $ttf->ttf['hmtx'][$glyphIndex];
		// 	$gs = new GlyphSvg($glyfData, $hm);
		//
		// 	$svg = $gs->getSvg();
		// 	echo $svg;
		// }

		foreach ($charCodeList as $i => $charCode) {
			$glyphIndex = $ttf->getGlyphIndex($charCode);
// dump($glyphIndex);
			if ($glyphIndex < 0) {
				continue;
			}

			$glyfData = $ttf->ttf['glyphList'][$glyphIndex];
			$hm = $ttf->ttf['hmtx'][$glyphIndex];
			$gs = new GlyphSvg($glyfData, $hm);

			$svg = $gs->getSvg();
			echo $svg;

		}

		echo '<hr />';

// dd($glyphIndex);

		return 'hello !';
    }
}
