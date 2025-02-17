<?php

namespace FontObscure\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

use FontObscure\TtfFile;
use FontObscure\TtfGlyph;
use FontObscure\TtfHorizontalMetrix;
use FontObscure\GlyphSvg;
use FontObscure\Stroke;

use FontObscure\Libs\Outline;
use FontObscure\Libs\Shape;


class TestController extends Controller
{
	public function loadStroke(Request $request)
	{
		echo '<h1>loadStroke</h1>';

		$s = '<svg>';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="14" x2="86" y1="21" y2="21" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="38" x2="38" y1="6" y2="81" />';
		$s .= '<path d="M68,40 c-8.337,27.624 -28.133,45 -43,45 c-7.852,0 -13,-4.894 -13,-15 c0,-17.828 17.78,-32 43,-32 C76.039,38 88,48.227 88,63 C88,76.87 77.755,86.868 60,90" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';


//		$s = '<svg>';
//		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="14" x2="86" y1="21" y2="21" />';
//		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="38" x2="38" y1="6" y2="81" />';
//		$s .= '<path d="M14,35 84,35 84,70 14,70 14,35 z" fill="none" stroke="#000000" stroke-width="2" />';
//		$s .= '</svg>';


		echo $s;


		$stroke = Stroke::createFromSvg($s, 1);
		$stroke->save();

		dump($stroke->data);

		$s = '<svg>';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="84" x2="16" y1="40" y2="40" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="87" x2="13" y1="20" y2="20" />';
		$s .= '<path d="M57,6 v63.0 c0,15.188 -5.467,20 -18,20 c-11.587,0 -19,-5.989 -19,-15 c0,-8.021 6.047,-13 19,-13 c9.891,0 24.002,6.69 45,24" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';

		echo $s;

		$stroke = Stroke::createFromSvg($s, 2);
		$stroke->save();

		dump($stroke->data);

		$s = '<svg>';
		$s .= '<path d="M48,91 c-6.987,-26.081 -14.347,-51.367 -23,-78" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M7,46 c48.111,-18.469 57.416,-21 66,-21 c11.1,0 18,6.727 18,18 c0,11.315 -7.242,18 -19,18 c-4.578,0 -8.745,-0.591 -14,-2" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M63,44 c-2.557,-11.075 -5.676,-23.595 -9,-36" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';

		echo $s;

		$stroke = Stroke::createFromSvg($s, 3);
		$stroke->save();

		dump($stroke->data);



		$s = '<svg>';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="28" x2="28" y1="11.5" y2="56" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="11.5" x2="45.49" y1="41" y2="41" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="11.5" x2="45.49" y1="25.5" y2="25.5" />';
		$s .= '<path d="M47,10.5 h-36.5 v46.5 h36.0 c0,30.181 -2.043,34.5 -5,34.5 c-2.813,0 -6.941,-0.3 -12,-1" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '<path d="M40,78 c-1.045,-4.762 -2.381,-9.932 -4,-15.5" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M30,82.25 c-0.666,-5.847 -1.643,-12.114 -3,-19" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M20,86.5 c-0.287,-6.932 -0.904,-14.298 -2,-22.5" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M6,89.5 c2.229,-7.541 3.926,-16.133 5,-26" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '<path d="M57.5,42 h33.0 v-31.0 h-34.0 v31.0 c0,22.843 -2.312,38.206 -6.5,50" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M93.5,92 c-10.365,-13.469 -18.158,-31.894 -21,-50" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '</svg>';
		echo $s;
		$stroke = Stroke::createFromSvg($s, 4);
		$stroke->save();

		echo '<hr />';


		$s = '<svg xmlns="http://www.w3.org/2000/svg" height="100px" version="1.0" viewBox="0 0 100 100" width="100px" x="0px" y="0px">';
		$s .= '<polyline fill="none" points="90,44.5 90,27.5 10,27.5 10,45.5" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M12.5,13 c26.042,0 51.093,-1.89 75,-5.5" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M31,27.5 c-2.783,-5.236 -5.894,-10.218 -9,-14.5" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '<path d="M53,27.5 c-2.773,-5.55 -5.91,-10.955 -9,-15.5" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M69,27.5 c3.491,-5.151 6.621,-10.566 9,-15.5" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="29.5" x2="77" y1="60" y2="60" />';
		$s .= '<path d="M8,91.5 c29.968,-4.7 54.725,-16.6 68.268,-31.181" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M92,91.5 c-25.572,-4.097 -49.002,-14.464 -63,-27" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M31.5,33.5 v9.5 c0,7.665 0.177,7.867 6,8.2 c3.229,0.186 7.058,0.3 11,0.3 c3.87,0 7.555,-0.103 11,-0.3 c10.129,-0.58 10.674,-0.943 12,-11.2" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M10,54 c5.406,-4.688 9.783,-10.667 12.5,-17" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M88.5,54 c-3.571,-6.246 -8.054,-12.773 -13,-19" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M58.5,42.5 c-5.266,-3.313 -11.482,-6.611 -18,-9.5" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M8,74 c12.773,-6.165 23.109,-14.281 29.5,-22.5" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '</svg>';
		echo $s;
		$stroke = Stroke::createFromSvg($s, 5);
		$stroke->save();

		echo '<hr />';

		$s = '<svg xmlns="http://www.w3.org/2000/svg" height="100px" version="1.0" viewBox="0 0 100 100" width="100px" x="0px" y="0px">';

		$s .= '<polyline fill="none" points="20,25 81,25 81,10 19,10 19,26" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M8,92 c13.08,-3.282 24.369,-7.771 31,-12" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M92,93 c-7.982,-4.208 -19.018,-8.679 -31,-12.5" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="16" x2="84" y1="61.5" y2="61.5" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="16" x2="84" y1="47.5" y2="47.5" />';
		$s .= '<polyline fill="none" points="16,75.5 85,75.5 85,33.5 15,33.5 15,76.5" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';
		echo $s;
		$stroke = Stroke::createFromSvg($s, 6);
		$stroke->save();
		echo '<hr />';


		// 漢
		$s = '<svg xmlns="http://www.w3.org/2000/svg" height="100px" version="1.0" viewBox="0 0 100 100" width="100px" x="0px" y="0px">';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="30.5" x2="93" y1="14" y2="14" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="49.5" x2="49.5" y1="22" y2="4" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="75" x2="75" y1="22" y2="4" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="30" x2="93.5" y1="68" y2="68" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="32" x2="91.5" y1="54.5" y2="54.5" />';

		$s .= '<polyline fill="none" points="37,43.5 89,43.5 89,26 36,26 36,44.5" stroke="#000000" stroke-width="2" />';

		$s .= '<path d="M29.5,91 c20.092,-5.015 33,-16.851 33,-26.5 V27.0" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M92.5,91 c-18.154,-5.014 -30,-16.853 -30,-26.5" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '<path d="M9,91.5 c7.072,-9.315 12.963,-19.819 17,-30.5" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M26,22.5 c-4.551,-4.456 -9.955,-9.209 -16,-14" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M22.5,45.5 c-5.014,-4.892 -10.751,-9.858 -17.5,-15" fill="none" stroke="#000000" stroke-width="2" />';

		$s .= '</svg>';
		echo $s;
		$stroke = Stroke::createFromSvg($s, 7);
		$stroke->save();
		echo '<hr />';

		$s = '<svg xmlns="http://www.w3.org/2000/svg" height="100px" version="1.0" viewBox="0 0 100 100" width="100px" x="0px" y="0px">';
		$s .= '<polyline fill="none" points="10,12 50,12 50,88 90,88" stroke="#000000" stroke-width="2" />';
		$s .= '<polyline fill="none" points="88,10 88,50 12,50 12,90" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';
		echo $s;
		$stroke = Stroke::createFromSvg($s, 8);
		$stroke->save();
		echo '<hr />';



		return 'OK';
	}

	public function test(Request $request)
    {
		self::testStrokeToOutlineFromDatabase();
die;
		self::testCornerCross();


		self::testLineLine();

		self::testCurveOutlinePenetrate();


		self::testOutlineShi();

		self::testShapeClass();

die;





		self::testOutlineMa();

		self::testOutlineA();

//
//		self::testOutlineCurveSelfCross();

//		self::testBezierSlice();


//		self::testParallelLine();



		// self::testBezierCross();
		//
		// self::testBezierCtoQ();

		// self::testOutlineMaya();


//		self::testOutlineSelfCross();
//
//

		// self::testOutlinePenetrate();




		//
		// $glyph = new TtfGlyph([
		// 	'glyph_index' => 0,
		// 	'number_of_contours' => null,
		// 	'x_min' => 0,
		// 	'y_min' => 0,
		// 	'x_max' => 100,
		// 	'y_max' => 100,
		// 	'coordinates' => [
		// 		[
		// 			['x' => 500, 'y' => 1000, 'flags' => 0x00],
		// 			['x' => 1000, 'y' => 500, 'flags' => 0x00],
		// 			['x' => 500, 'y' => 0, 'flags' => 0x00],
		// 			['x' => 0, 'y' => 500, 'flags' => 0x000],
		// 		],
		// 		[
		// 			['x' => 500, 'y' => 700, 'flags' => 0x01],
		// 			['x' => 300, 'y' => 500, 'flags' => 0x01],
		// 			['x' => 500, 'y' => 300, 'flags' => 0x01],
		// 			['x' => 700, 'y' => 500, 'flags' => 0x01],
		// 		],
		// 	],
		// 	'instructions' => [],
		// ]);
		//
		// $hm = new TtfHorizontalMetrix([
		// 	'advance_width' => 1200,
		// 	'lsb' => 20,
		// ]);
		//
		// $glyfData = [
		// 	'header' => [
		// 		 'xMin' => $glyph->xMin,
		// 		 'yMin' => $glyph->yMin,
		// 		 'xMax' => $glyph->xMax,
		// 		 'yMax' => $glyph->yMax,
		// 	],
		// 	'coordinates' => $glyph->coordinates,
		// 	'instructions' => $glyph->instructions,
		// ];
		// $gs = new GlyphSvg($glyfData, $hm);
		// echo $gs->getSvg();


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

		$s = '<svg>';
		$s .= ' <path d="M 11,33 C27.332,30.471 40,24 57,24 C77.198,24 87,32.569 87,46 C87,65.948 72.424,78.624 33,82" fill="none" stroke="#FF0000" stroke-width="1" />';
		$s .= ' <circle cx="11" cy="33" r="3"/>';
		$s .= ' <circle cx="27.332" cy="30.471" r="3"/>';
		$s .= ' <circle cx="40" cy="24" r="3"/>';
		$s .= ' <circle cx="57" cy="24" r="3"/>';
		$s .= ' <circle cx="77.198" cy="24" r="3"/>';
		$s .= ' <circle cx="87" cy="32.569" r="3"/>';
		$s .= ' <circle cx="87" cy="46" r="3"/>';
		$s .= ' <circle cx="87" cy="65.948" r="3"/>';
		$s .= ' <circle cx="72.424" cy="78.624" r="3"/>';
		$s .= ' <circle cx="33" cy="82" r="3"/>';
		$s .= '</svg>';
		echo "<hr />{$s}<hr />";


		$s = '<svg>';
		$s .= ' <path d="M 11,33 C27.332,30.471 40,24 57,24 C77.198,24 87,32.569 87,46 C87,65.948 72.424,78.624 33,82" fill="none" stroke="#FF0000" stroke-width="1" />';
		$s .= '</svg>';
		$stroke = self::parseStrokeSvg($s);

		echo '<hr />svgテスト<br />';
		$s = '<svg>';
		$s .= ' <path d="M 11,60 C40,60 40,24 57,24 C60,24 87,40 87,60 " fill="none" stroke="#FF0000" stroke-width="1" />';
		$s .= '<circle cx="57" cy="24" r="3"/>';
		$s .= '</svg>';
		echo $s;

		echo '<hr />ストローク tsu<br />';
		echo self::testStrokeToSvg($stroke);
		echo '<br />';

		echo '<hr />アウトライン<br />';
		$outline = self::getOutlineFromStroke($stroke);
		$s = '<svg>';
		$sc = '';
		foreach ($outline as $line) {
			$s .= '<path d="M ';
			$cCount = 0;
			foreach ($line as $index => $l) {
				if (!$l['isOnCurvePoint']) {
					if ($cCount == 0) {
						$s .= "Q";
						$cCount = 2;
					}
				} else {
					if ($index != 0) {
						if ($cCount == 0) {
							$s .= ' L';
						}
					}
				}
				if ($cCount > 0) {
					$cCount--;
				} else{
					if (!$l['isOnCurvePoint']) {
						// dd('ばぐったー!');
					}
				}

				// $s .= "{$index},{$index} ";
				$s .= "{$l['x']},{$l['y']} ";
				$sc .= "<circle cx='{$l['x']}' cy='{$l['y']}' r='2' />";
			}
			$s .= 'z" fill="#dddddd" stroke="#000000" stroke-width="1" />';
		}
		$s .= $sc.'</svg>';
		echo $s;


		$sp = '<svg  width="100" height="100">';
		$sp .= ' <path d="M0,0 Q4,75 50,75 Q95,76 100,0" fill="none" stroke="black"/>';
		$sp .= '</svg>';
		echo $sp;

		echo '<hr /><br /><br />';

		echo '<hr />ストローク @shi <br />';
		$shi = '<svg>';
		$shi .= ' <path d="M86,57 c-10.481,21.489 -24.904,32 -39,32 c-13.079,0 -21,-8.539 -21,-28 c0,-16.818 2,-32.069 2,-50" fill="none" stroke="black"/>';
		$shi .= '</svg>';

		$stroke = self::parseStrokeSvg($shi);

		self::testStrokeToSvg($stroke);


		dump($stroke);

		echo '<hr />ストローク @shi test<br />';
		$shi = '<svg>';
		$shi .= ' <path d="M86,57 c-10.481,21.489 -24.904,32 -39,32 c-13.079,0 -21,-8.539 -21,-28 c0,-16.818 2,-32.069 2,-50" fill="none" stroke="black"/>';
		$shi .= '</svg>';

		$stroke = self::parseStrokeSvg($shi);

		$s = '<svg>';
		$sc = '';
// dd($stroke);
		foreach ($stroke as $line) {
			// $s .= '<path d="M ';
			$cCount = 0;
			foreach ($line['path'] as $index => $l) {
				if (!$l['isOnCurvePoint']) {
					if ($cCount == 0) {
						$cCount = 2;
						// $s .= "C";
					}
				}
				if ($cCount > 0) {
					$cCount--;
				}

				// $s .= "{$l['x']},{$l['y']} ";
				$s .= "<circle cx='{$l['x']}' cy='{$l['y']}' r='3' />";
			}
			$s .= '" fill="none" stroke="blue" stroke-width="1" />';
		}
		$s .= $sc.'</svg>';
		echo $s;

		dump($stroke);

		echo '<hr />アウトライン @shi test<br />';
		$s = '<svg>';
		$s .= ' <path d="M86,57 c-10.481,21.489 -24.904,32 -39,32 c-13.079,0 -21,-8.539 -21,-28 c0,-16.818 2,-32.069 2,-50" fill="none" stroke="black"/>';
		$s .= '</svg>';

// echo $s.'<br />';

		$stroke = self::parseStrokeSvg($s);
		$outline = self::getOutlineFromStroke($stroke);

		echo self::testOutlineToSvg($outline);


// $s = '<svg>';
// $s .= '<path d="M 10.08184643318,27.070666645589 C25.461132678355,24.689193775515 38.556281302713,18 57,18 C79.450877152123,18 93,29.844769810595 93,46 C93,68.681610662714 74.885104253786,84.435206881087 33.511925148314,87.978121163252 32.488074851686,76.021878836748 C69.962895746214,72.812793118913 81,63.214389337286 81,46 C81,35.293230189405 74.945122847877,30 57,30 C41.443718697287,30 29.202867321645,36.252806224485 11.91815356682,38.929333354411 z" />';
// $s .= '</svg>';
// echo $s;
// 		dd($s);


		echo '<hr />glyph test<br />';
		$o = [];
		foreach ($outline as $contour) {
			$c = [];
			foreach ($contour as $point) {
				$flags = 0;
				if ($point['isOnCurvePoint']) {
					$flags |= 0x01;
				}

				$c[] = [
					'x' => $point['x'] * 10,
					'y' => 200 - $point['y'] * 10,
					'flags' => $flags,
				];
			}
			if (count($c) > 0) {
				$o[] = $c;
			}
		}

		$g = new TtfGlyph([
			'glyph_index' => 1,
			'number_of_contours' => count($o),
			'x_min' => 0,
			'y_min' => 0,
			'x_max' => 300,
			'y_max' => 300,
			'coordinates' => $o,
			'instructions' => [],
		]);

//$s = new GlyphSvg($g, $hm);
//echo $s->getSvg();
//echo '<hr />';



// dd($s->getSvg());



		// font
		// mplus-1c-light

		// $file = Storage::disk('local')->get('strokes/font.ttf');
		// $file = Storage::disk('local')->get('strokes/Glamor-Light.ttf');
		// $file = Storage::disk('local')->get('strokes/fancyheart_regular.ttf');
		$file = Storage::disk('local')->get('strokes/mplus-1c-light.ttf');

		$ttf = TtfFile::createFromFile('test', $file);
		$charCodeList = [
			mb_ord('け'),
			mb_ord('し'),
			mb_ord('い'),
			mb_ord('う'),
			mb_ord('え'),
			mb_ord('-'),
			mb_ord('朧'),
			mb_ord('卍'),


			// ord('-'),
			// ord('M'),
			// ord('A'),
			// ord('Y'),
			// ord('A'),
			//
			ord('-'),

			0x307F,
			0x3084,

			ord('-'),

			// ord('m'),
			// ord('a'),
			// ord('y'),
			// ord('a'),

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

	public static function testLineLine()
	{
		echo '<h1>LINE to LINE hit</h1>';

		$stroke = [
			[
				'path' => [
					[
						'x' => 10,
						'y' => 50,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 110,
						'y' => 50,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => true,
			],

			[
				'path' => [
					[
						'x' => 10,
						'y' => 55,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 110,
						'y' => 55,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => true,
			],
		];

		$outline = new \FontObscure\Libs\Outline($stroke);


	}

	public static function testOutlineShi()
	{

		echo '<hr />ストローク @shi <br />';
		$shi = '<svg>';
		$shi .= '<path d="M18,90 c-3.537,-13.203 -5,-24.992 -5,-40 s1.463,-26.797 5,-40" fill="none" stroke="#000000" stroke-width="2" />';
		$shi .= '<line fill="none" stroke="#000000" stroke-width="2" x1="94" x2="36" y1="30" y2="30" />';
		$shi .= '<path d="M51,89 c19.62,-5.258 24,-10.913 24,-40 v-40.0" fill="none" stroke="#000000" stroke-width="2" />';
		$shi .= '</svg>';


//		$shi = '<svg>';
//		$shi .= ' <path d="M86,57 c-10.481,21.489 -24.904,32 -39,32 c-13.079,0 -21,-8.539 -21,-28 c0,-16.818 2,-32.069 2,-50" fill="none" stroke="black"/>';
//		$shi .= '</svg>';

		echo $shi;

		$stroke = Stroke::createFromSvg($shi);
		echo $stroke->toSvg();

		$outline = new \FontObscure\Libs\Outline($stroke->data);
		echo $outline->toSvg();

	}

	public static function testCornerCross()
	{
		echo '<h1>testCornerCross</h1>';

		$shapesData = [];

//		$shapesData[] = [
//			[
//				'x' => 1,
//				'y' => 130,
//				'isOnCurvePoint' => false,
//			], [
//				'x' => 180,
//				'y' => 100,
//				'isOnCurvePoint' => true,
//			], [
//				'x' => 180,
//				'y' => 120,
//				'isOnCurvePoint' => true,
//			], [
//				'x' => 10,
//				'y' => 120,
//				'isOnCurvePoint' => true,
//			], [
//				'x' => 10,
//				'y' => 10,
//				'isOnCurvePoint' => true,
//			], [
//				'x' => 40,
//				'y' => 10,
//				'isOnCurvePoint' => true,
//			]
//		];
//
//		$shapesData[] = [
//			[
//				'x' => 30,
//				'y' => 30,
//				'isOnCurvePoint' => true,
//			], [
//				'x' => 50,
//				'y' => 10,
//				'isOnCurvePoint' => true,
//			], [
//				'x' => 110,
//				'y' => 40,
//				'isOnCurvePoint' => false,
//			], [
//				'x' => 110,
//				'y' => 90,
//				'isOnCurvePoint' => true,
//			], [
//				'x' => 90,
//				'y' => 110,
//				'isOnCurvePoint' => true,
//			], [
//				'x' => 80,
//				'y' => 40,
//				'isOnCurvePoint' => false,
//			]
//		];

		$shapesData[] = [
			[
				'x' => 34.6,
				'y' => 43.0614880032,
				'isOnCurvePoint' => true,
			], [
				'x' => 34.4263143151,
				'y' => 47.4380636549,
				'isOnCurvePoint' => false,
			], [
				'x' => 34.8549002515,
				'y' => 48.0630081791,
				'isOnCurvePoint' => true,
			], [
				'x' => 34.8756451441,
				'y' => 48.0888980616,
				'isOnCurvePoint' => false,
			], [
				'x' => 37.598343522,
				'y' => 48.1003873925,
				'isOnCurvePoint' => true,
			], [
				'x' => 37.9052286281,
				'y' => 48.1185806408,
				'isOnCurvePoint' => false,
			], [
				'x' => 37.9052286281,
				'y' => 48.1185806408,
				'isOnCurvePoint' => false,
			], [
				'x' => 38.2245990505,
				'y' => 48.1358847356,
				'isOnCurvePoint' => true,
			], [
				'x' => 40.3113961509,
				'y' => 48.248951358,
				'isOnCurvePoint' => false,
			], [
				'x' => 42.9312465221,
				'y' => 48.3240560927,
				'isOnCurvePoint' => true,


			], [
				'x' => 42.7566514039,
				'y' => 54.5215980233,
				'isOnCurvePoint' => true,
			], [
				'x' => 40.7121520184,
				'y' => 54.4629872013,
				'isOnCurvePoint' => false,
			], [
				'x' => 39.0467890885,
				'y' => 54.3858166074,
				'isOnCurvePoint' => true,
			], [
				'x' => 38.1719050731,
				'y' => 54.3452757032,
				'isOnCurvePoint' => false,
			], [
				'x' => 37.401656478,
				'y' => 54.2996126075,
				'isOnCurvePoint' => true,
			], [
				'x' => 33.8732639829,
				'y' => 54.2847233825,
				'isOnCurvePoint' => false,
			], [
				'x' => 31.7771337627,
				'y' => 53.2485500467,
				'isOnCurvePoint' => true,
			], [
				'x' => 30.5981405788,
				'y' => 52.6657421235,
				'isOnCurvePoint' => false,
			], [
				'x' => 29.8722625865,
				'y' => 51.7598374089,
				'isOnCurvePoint' => true,
			], [
				'x' => 28.1496084457,
				'y' => 49.2479414722,
				'isOnCurvePoint' => false,
			], [
				'x' => 28.4,
				'y' => 42.9385119968,
				'isOnCurvePoint' => true,
			]
		];

		$shapesData[] = [
			[
				'x' => 23.5474566991,
				'y' => 60.8358529869,
				'isOnCurvePoint' => true,
			], [
				'x' => 27.768242785,
				'y' => 57.5515880764,
				'isOnCurvePoint' => false,
			], [
				'x' => 31.7771337627,
				'y' => 53.2485500467,
				'isOnCurvePoint' => true,
			], [
				'x' => 34.4129645624,
				'y' => 50.4193191059,
				'isOnCurvePoint' => false,
			], [
				'x' => 36.9571927491,
				'y' => 47.1496712436,
				'isOnCurvePoint' => true,
			], [
				'x' => 38.2245990505,
				'y' => 48.1358847356,
				'isOnCurvePoint' => true,
			], [
				'x' => 41.8503287564,
				'y' => 50.9571927491,
				'isOnCurvePoint' => true,
			], [
				'x' => 40.4678152736,
				'y' => 52.7338935486,
				'isOnCurvePoint' => false,
			], [
				'x' => 39.0467890885,
				'y' => 54.3858166074,
				'isOnCurvePoint' => true,
			], [
				'x' => 35.278895539,
				'y' => 58.7659401424,
				'isOnCurvePoint' => false,
			], [
				'x' => 31.2402341493,
				'y' => 62.2687998835,
				'isOnCurvePoint' => true,
			]
		];

		$shapeList = [];
		echo '<svg>';
		foreach ($shapesData as $s) {
			$shape = new Shape($s);
			echo $shape->toSvg();
			$shapeList[] = $shape;
		}
		echo '</svg>';

		echo '<hr />';
		echo '<h2>slice TEST</h2>';
		foreach ($shapeList as $s) {
			echo '<svg>';
			$slicedList = $s->slice();
			foreach ($slicedList as $ss) {
				echo $ss->toSvg();
			}
			echo '</svg>';
		}
		echo '<hr />';

		$outline = Outline::createFromShapeList($shapeList);
		echo $outline->toSvg();

		dump($shapesData);
	}

	public static function testStrokeToOutlineFromDatabase()
	{
		echo '<h1>testStrokeToOutlineFromDatabase</h1>';

		$svgList = [];

//		$st = Stroke::find(1);
//		$outline = new \FontObscure\Libs\Outline($st->data);
//		echo $outline->toSvg();

//		$st = Stroke::find(2);
//		$outline = new \FontObscure\Libs\Outline($st->data);
//		$ma = $outline->toSvg(false);
//		echo $ma;
//		$svgList[] = $ma;

//		$st = Stroke::find(3);
//		$outline = new \FontObscure\Libs\Outline($st->data);
//		$ya = $outline->toSvg(true);
//		echo $ya;
//		$svgList[] = $ya;


//		$st = Stroke::find(4);
//		echo $st->toSvg();
//		$outline = new \FontObscure\Libs\Outline($st->data);
//		$eki = $outline->toSvg(false);
//		echo $eki;
//		$svgList[] = $eki;

//		$st = Stroke::find(6);
//		echo $st->toSvg();
//		$outline = new \FontObscure\Libs\Outline($st->data);
//		$in = $outline->toSvg(false);
//		echo $in;
//		$svgList[] = $in;


		$st = Stroke::find(5);
		echo $st->toSvg();
		$outline = new Outline($st->data);
		$ai = $outline->toSvg(false);
		echo $ai;
		$svgList[] = $ai;

//		$st = Stroke::find(7);
//		echo $st->toSvg();
//		$outline = new Outline($st->data);
//		$kan = $outline->toSvg(false);
//		echo $kan;
//		$svgList[] = $kan;
//		echo '<hr />';

		echo '<hr />';
		foreach ($svgList as $svg) {
			echo $svg;
		}
	}

	public static function testShapeClass()
	{
		echo '<h1>Shapeクラス</h1>';


//		$svg = '<svg width="100" height="100">';
//		$svg .= ' <path d="M0,0 C0,100 100,100 100,0" fill="none" stroke="red"/>';
//		$svg .= '</svg>';
//		$st = Stroke::createFromSvg($svg);
//		dd($st->data);

		$stroke = [
			[
				'path' => [
					[
						'x' => 104,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 35,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
//					[
//						'x' => 35,
//						'y' => 65,
//						'isOnCurvePoint' => true,
//					],
					[
						'x' => 35,
						'y' => 80,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 80,
						'isOnCurvePoint' => true,
					],

				],
				'isClosed' => true,
			],

			[
				'path' => [
					[
						'x' => 80,
						'y' => 10,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 70,
						'y' => 120,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],

		];


		echo self::testStrokeToSvg($stroke);
		echo '<br />';


		$outline = new \FontObscure\Libs\Outline($stroke);


		$sl = [];
		foreach ($outline->shapes as $s) {
			$sl[] = $s->points;
		}

		echo self::testOutlineToSvg($sl);

		echo '<hr />';
		echo self::testOutlineToSvg($sl, false);


//		$slc = $sl[1]->compose($sl[2]);


//		echo self::testOutlineToSvg($outline->shapes, false);

//		if (!is_null($slc)) {
//			echo '<hr />';
//			echo self::testOutlineToSvg([$slc->getPoints()]);
//		}


		echo '<hr />';
	}

	public static function testShapeClassPenetrate()
	{
		echo '<h1>Shapeクラス</h1>';


//		$svg = '<svg width="100" height="100">';
//		$svg .= ' <path d="M0,0 C0,100 100,100 100,0" fill="none" stroke="red"/>';
//		$svg .= '</svg>';
//		$st = Stroke::createFromSvg($svg);
//		dd($st->data);

		$stroke = [
			[
				'path' => [
					[
						'x' => 104,
						'y' => 62,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 35,
						'y' => 100,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 30,
						'y' => 65,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 65,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 30,
						'isOnCurvePoint' => false,
					],
					[
						'x' => 100,
						'y' => 65,
						'isOnCurvePoint' => true,
					],

				],
				'isClosed' => true,
			],

			[
				'path' => [
					[
						'x' => 102,
						'y' => 10,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 52,
						'y' => 120,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],

		];


		echo self::testStrokeToSvg($stroke);
		echo '<br />';


		$outline = new \FontObscure\Libs\Outline($stroke);


		$sl = [];
		foreach ($outline->shapes as $s) {
			$sl[] = $s->points;
		}

		echo self::testOutlineToSvg($sl);

		echo '<hr />';
		echo self::testOutlineToSvg($sl, false);


//		$slc = $sl[1]->compose($sl[2]);


//		echo self::testOutlineToSvg($outline->shapes, false);

//		if (!is_null($slc)) {
//			echo '<hr />';
//			echo self::testOutlineToSvg([$slc->getPoints()]);
//		}


		echo '<hr />';
	}
	public static function testParallelLine()
	{
		echo '<h1>testParallelLine</h1>';
		$a = [
			[
				'path' => [
					[
						'x' => 60,
						'y' => 60,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 100,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],
			[
				'path' => [
					[
						'x' => 0,
						'y' => 0,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 50,
						'y' => 50,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],
		];

		echo self::testStrokeToSvg($a);

		dump($a);

		$aRet = self::getCrossPointByRay($a[0]['path'], $a[1]['path']);
		dump($aRet);
		echo '<hr />';

		$b = [
			[
				'path' => [
					[
						'x' => 0,
						'y' => 50,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 50,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],
			[
				'path' => [
					[
						'x' => 0,
						'y' => 20,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 20,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],
		];

		echo self::testStrokeToSvg($b);
		$bRet = self::getCrossPointByRay($b[0]['path'], $b[1]['path']);
		dump($bRet);


		echo '<br />';
	}

	public static function testBezierCtoQ()
	{
		echo '<hr /><h1> 2 -> 3</h1><br />';
		$sp = '<svg width="100" height="100">';
		$sp .= ' <path d="M0,0 C0,100 100,100 100,0" fill="none" stroke="red"/>';
		$sp .= '</svg>';
		echo $sp;
		echo '<hr /><h1>** K **</h1><br />';

		$s = [
			'x' => 0,
			'y' => 150,
		];

		$e = [
			'x' => 300,
			'y' => 150,
		];


		$a = [
			'x' => 0,
			'y' => 0,
		];
		$b = [
			'x' => 300,
			'y' => 0,
		];


		$se = self::getMidPoint($s, $e);

		$mp1 = self::getMidPoint($s, $se);
		// $mp1 = self::getMidPoint($s, $se);
		$mp2 = self::getMidPoint($e, $se);

		$sa = self::getMidPoint($s, $a);
		$eb = self::getMidPoint($e, $b);

		$ab = self::getMidPoint($a, $b);



		// $t = 0.5;
		// $k = [
		// 	'x' => ($s['x'] * pow(1 - $t, 3)) + ($a['x'] * (pow(1 - $t, 2) * 3) * $t) + ($b['x'] * (pow($t, 2) * 3 * (1 - $t))) + ($e['x'] * pow($t, 3)),
		// 	'y' => ($s['y'] * pow(1 - $t, 3)) + ($a['y'] * (pow(1 - $t, 2) * 3) * $t) + ($b['y'] * (pow($t, 2) * 3 * (1 - $t))) + ($e['y'] * pow($t, 3)),
		// ];
		$k = self::getBezier3CurvePoint($s, $e, $a, $b, 0.5);


		// $k = [
		// 	'x' => ($s['x'] * 0.125) + ($a['x'] * (0.25 * 3) * 0.5) + ($b['x'] * (0.125 * 3)) + ($e['x'] * 0.125),
		// 	'y' => ($s['y'] * 0.125) + ($a['y'] * (0.25 * 3) * 0.5) + ($b['y'] * (0.125 * 3)) + ($e['y'] * 0.125),
		// ];

		$sk = self::getMidPoint($s, $k);
		$ka = self::getMidPoint($sk, $a);
		$ek = self::getMidPoint($e, $k);
		$kb = self::getMidPoint($ek, $b);

		$aByK = self::getBezier3CurvePoint($s, $e, $a, $b, 0.25);
// dump(compact('aByK'));

		$bByK = self::getBezier3CurvePoint($s, $e, $a, $b, 0.75);

// dd($aByK);
// dump(compact('bByK'));
		$t = 0.5;

// $aByK['x'] = 10;

		$lenX1 = ($b['x'] - $k['x']);
		$lenX2 = ($k['x'] - $a['x']);

dump("lenX1={$lenX1}, lenX2={$lenX2}");

$t = 0.58;
dump(compact('t'));

		$param = ($s['x'] * pow(1 - $t, 2)) + ($k['x'] * pow($t, 2));
		$ax = (($aByK['x'] - $param) / ((1 - $t) * $t * 2));
		$param = ($s['y'] * pow(1 - $t, 2)) + ($k['y'] * pow($t, 2));
		$ay = ($aByK['y'] - $param) / ((1 - $t) * $t * 2);

$t = 0.42;
		$param = ($k['x'] * pow(1 - $t, 2)) + ($e['x'] * pow($t, 2));
		$bx = (($bByK['x'] - $param) / ((1 - $t) * $t * 2));
		$param = ($k['y'] * pow(1 - $t, 2)) + ($e['y'] * pow($t, 2));
		$by = ($bByK['y'] - $param) / ((1 - $t) * $t * 2);
dump("new a = {$ax},{$ay}");



		$kAdd = [
			'x' => ($b['x'] * (pow(0.25, 2) * 3) * 0.75),
			'y' => ($b['y'] * (pow(0.25, 2) * 3) * 0.75),
		];
// dump($inverseK);
// dump($kAdd);




		echo '<hr />逆算<br />';
		echo '<hr />';

		$sp = '<svg width="300" height="300">';
		$sp .= " <path d='M{$s['x']},{$s['y']} C{$a['x']},{$a['y']} {$b['x']},{$b['y']} {$e['x']},{$e['y']}' fill='none' stroke='red'/>";
		// $sp .= " <circle cx='{$aByK['x']}' cy='{$aByK['y']}' r='3' fill='green' />";
		$sp .= " <circle cx='{$b['x']}' cy='{$b['y']}' r='3' fill='green' />";

		// $a = [
		// 	'x' => 4,
		// 	'y' => 64,
		// ];
		// $sp .= " <path d='M{$s['x']},{$s['y']} Q{$ax},{$ay} {$k['x']},{$k['y']} Q{$bx},{$by} {$e['x']},{$e['y']}' fill='none' stroke='blue'/>";
		$sp .= " <path d='M{$s['x']},{$s['y']} {$se['x']},{$se['y']} {$a['x']},{$a['y']} z' fill='none' stroke='skyblue'/>";
		$sp .= " <path d='M{$mp1['x']},{$mp1['y']} {$a['x']},{$a['y']} ' fill='none' stroke='black'/>";

		$target = [
			'x' => 3,
			'y' => 64,
		];
dump(compact('target'));
		$sp .= " <circle cx='{$target['x']}' cy='{$target['y']}' r='3' fill='skyblue' />";

		$sk = self::getMidPoint($s, $k);


		////////////////////////////////////////
		$sp .= " <path d='M {$s['x']},{$s['y']} {$k['x']},{$k['y']}' stroke='green' />";
		$sp .= " <path d='M {$e['x']},{$e['y']} {$k['x']},{$k['y']}' stroke='green' />";
		$sp .= " <circle cx='{$k['x']}' cy='{$k['y']}' r='3' fill='red' />";
		$sp .= " <circle cx='{$aByK['x']}' cy='{$aByK['y']}' r='3' fill='gray' />";
		$sp .= " <circle cx='{$bByK['x']}' cy='{$bByK['y']}' r='3' fill='gray' />";
		// $sp .= " <circle cx='{$mp1['x']}' cy='{$mp1['y']}' r='3' fill='green' />";
		$sp .= " <circle cx='{$sk['x']}' cy='{$sk['y']}' r='3' fill='blue' />";
		// $sp .= " <path d='M {$sk['x']},{$sk['y']} {$a['x']},{$a['y']}' stroke='green' />";
		////////////////////////////////////////

		$sp .= '</svg>';
		echo $sp;
		echo '<hr />TEST<br />';


		echo '** C to Q<br />';
		$test = self::svgCtoQ($s, [$a, $b, $e]);
dump(compact('test'));
		$cl = [
			'red',
			'blue',
		];
		$sp2 = '<svg width="300" height="300">';
		foreach ($test as $i => $t) {
			$sp2 .= " <path d='M{$t[0]['x']},{$t[0]['y']} Q{$t[1]['x']},{$t[1]['y']} {$t[2]['x']},{$t[2]['y']}' fill='none' stroke='{$cl[$i]}'/>";
		}
		$sp2 .= '</svg>';
		echo $sp2.'<br />';


		echo '分解<br />';
		$sp2 = '<svg width="300" height="300">';
		$sp2 .= " <path d='M{$s['x']},{$s['y']} Q{$ax},{$ay} {$k['x']},{$k['y']}' fill='none' stroke='blue'/>";
		$sp2 .= '</svg>';
		echo "<hr />{$sp2}";

		$sp2 = '<svg width="300" height="300">';
		$sp2 .= " <path d='M{$k['x']},{$k['y']}  Q{$bx},{$by} {$e['x']},{$e['y']}' fill='none' stroke='blue'/>";
		$sp2 .= '</svg>';
		echo "{$sp2}<hr />";



		// $mpoint = [
		// 	'x' => 0 + ((100 - 0) / 2),
		// 	'y' => 0 + ((100 - 100) / 2),
		// ];



		// $cntp1 = [
		// 	'x' => $mpoint['x'] - (50 * 0.77),
		// 	'y' => $mpoint['y'] + (100 * 0.77),
		// ];
		// $cntp2 = [
		// 	'x' => $mpoint['x'] + (50 * 0.77),
		// 	'y' => $mpoint['y'] + (100 * 0.77),
		// ];
		// dd($cntp2);
		$sp .= '</svg>';

	}

	public static function testBezierCross()
	{
		echo '<hr /><h3>- testBezierCross() -</h3>';

		$b = [
			[
				'path' => [
					[
						'x' => 1,
						'y' => 180,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 0,
						'isOnCurvePoint' => false,
					],
					[
						'x' => 200,
						'y' => 180,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],

			[
				'path' => [
					[
						'x' => 160,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 50,
						'y' => 200,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			]
		];

		echo self::testStrokeToSvg($b);


		$base = $b[0]['path'];
		dump($base);
		$addition = $b[1]['path'];
		dump($addition);

		// $ma = [
		// 	'x' => $addition[0]['x'] + (($addition[1]['x'] - $addition[0]['x']) / 2),
		// 	'y' => $addition[0]['y'],
		// ];


		$p = self::getBezier2CrossPoint([$base[0], $base[1], $base[2]], [$addition[0], $addition[1]]);
dump($p);


		echo '<h2>曲線と直線の交点</h2>';
		$s = '<svg width="500px" height="300px">';

		$s .= "<path d='M {$base[0]['x']},{$base[0]['y']} Q {$base[1]['x']},{$base[1]['y']} {$base[2]['x']},{$base[2]['y']}' stroke='red' fill='none' />";
		$s .= "<path d='M {$addition[0]['x']},{$addition[0]['y']} {$addition[1]['x']},{$addition[1]['y']}' stroke='blue' fill='none' />";

		$s .= "<path d='M {$base[0]['x']},{$base[0]['y']} {$base[1]['x']},{$base[1]['y']} {$base[2]['x']},{$base[2]['y']}' stroke='#e0e0e0' fill='none' />";
		if (!empty($p)) {
			$s .= "<circle cx='{$p[0]['x']}' cy='{$p[0]['y']}' r='3' fill='black' />";
		}

		$s .= '</svg>';
		echo $s;

		echo '<hr />';
	}

	public static function testBezierSlice()
	{
		echo '<hr /><h3>- testBezierSlice() -</h3>';

		$b = [
			[
				'path' => [
					[
						'x' => 1,
						'y' => 180,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 0,
						'isOnCurvePoint' => false,
					],
					[
						'x' => 200,
						'y' => 180,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],

			[
				'path' => [
					[
						'x' => 50,
						'y' => 200,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 160,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			]
		];

		echo self::testStrokeToSvg($b);


		$base = $b[0]['path'];
		dump($base);
		$addition = $b[1]['path'];
		dump($addition);

		// $ma = [
		// 	'x' => $addition[0]['x'] + (($addition[1]['x'] - $addition[0]['x']) / 2),
		// 	'y' => $addition[0]['y'],
		// ];


		$p = self::getBezier2CrossPoint([$base[0], $base[1], $base[2]], [$addition[0], $addition[1]]);


dump(compact('p'));




		echo '<h2>元</h2>';
		$s = '<svg width="500px" height="300px">';
		$s .= "<path d='M {$base[0]['x']},{$base[0]['y']} Q {$base[1]['x']},{$base[1]['y']} {$base[2]['x']},{$base[2]['y']}' stroke='red' fill='none' />";
		$s .= "<path d='M {$addition[0]['x']},{$addition[0]['y']} {$addition[1]['x']},{$addition[1]['y']}' stroke='blue' fill='none' />";

		$s .= "<path d='M {$base[0]['x']},{$base[0]['y']} {$base[1]['x']},{$base[1]['y']} {$base[2]['x']},{$base[2]['y']}' stroke='#e0e0e0' fill='none' />";
		// if (!empty($p)) {
		// 	$s .= "<circle cx='{$p[0]['x']}' cy='{$p[0]['y']}' r='3' fill='black' />";
		// }

		$s .= '</svg>';
		echo $s;

		$sliced = self::sliceBezier2ByLine([$base[0], $base[1], $base[2]], [$addition[0], $addition[1]]);
		$base = $sliced['bezier'];
		$addition = $sliced['line'];
		$p = $sliced['p'];

		echo '<h2>Slice</h2>';
		$s = '<svg width="500px" height="300px">';
		$s .= "<path d='M {$base[0]['x']},{$base[0]['y']} Q {$base[1]['x']},{$base[1]['y']} {$base[2]['x']},{$base[2]['y']}' stroke='red' fill='none' />";
		$s .= "<path d='M {$addition[0]['x']},{$addition[0]['y']} {$addition[1]['x']},{$addition[1]['y']}' stroke='blue' fill='none' />";

		$s .= "<path d='M {$base[0]['x']},{$base[0]['y']} {$p['x']},{$p['y']} {$base[2]['x']},{$base[2]['y']}' stroke='#e0e0e0' fill='none' />";

		$s .= '</svg>';
		echo $s;


		echo '<hr />';




	}

	protected static function getLineParams($v1, $v2)
	{
		$a = ($v2['y'] - $v1['y']);
		$b = ($v2['x'] - $v1['x']);
		return [
			'a' => $a,
			'b' => -$b,
			'c' => ($v2['y'] * $b) - ($v2['x'] * $a),
		];
	}

	protected static function sliceBezier2ByLine($bezir, $line)
	{
		$lp = self::getLineParams($line[0], $line[1]);
		$a = $lp['a'];
		$b = $lp['b'];
		$c = $lp['c'];

		$b0 = $bezir[0];
		$b1 = $bezir[2];
		$cp = $bezir[1];

		$m = ($b * $b1['y']) + ($b * $b0['y']) + ($a * $b0['x']) + ($a * $b1['x']) - (2 * $b * $cp['y']) - (2 * $a * $cp['x']);
		$n = -(2 * $b * $b0['y']) - (2 * $a * $b0['x']) + (2 * $b * $cp['y']) + (2 * $a * $cp['x']);
		$l = ($b * $b0['y']) + ($a * $b0['x']) + $c;

		$tList = [];
		$d = ($n * $n) - (4 * $m * $l);

		if ($d > 0) {
			$d = sqrt($d);
			$t0 = 0.5 * (-$n + $d) / $m;
		    $t1 = 0.5 * (-$n - $d) / $m;

			if (($t0 >= 0) && ($t0 <= 1.0)) {
				$tList[] = $t0;
			}
			if(($t1 >= 0) && ($t1 <= 1.0)){
				$tList[] = $t1;
		    }
		} else if ($d == 0) {
			$t1 = 0.5 * -$n / $m;
			if(($t1 >= 0) && ($t1 <= 1.0)){
				$tList[] = $t1;
		    }
		}

		if (empty($tList)) {
			return null;
		}

		$crossPoint = null;
		$crossLength = 0;
		$crossT = 0;
		foreach ($tList as $t) {
			$p = self::getBezier2CurvePoint($b0, $b1, $cp, $t);
			$length = (($p['x'] - $line[0]['x']) ** 2) + (($p['y'] - $line[0]['y']) ** 2);

			if (is_null($crossPoint)) {
				$crossPoint = $p;
				$crossLength = $length;
				$crossT = $t;
			} else {
				if ($crossLength > $length) {
					$crossPoint = $p;
					$crossLength = $length;
					$crossT = $t;
				}
			}
		}

		$p2 = self::getBezier2CurvePoint($b0, $b1, $cp, $crossT * 0.5);
// dump(compact('p2'));
		$p3 = self::getBezier2ControlPoint($b0, $crossPoint, $p2, 0.5);

		// TODO: $p2を通る曲線を逆算


		return [
			'bezier' => [
				$bezir[0],
				$p3,
				$crossPoint,
			],
			'line' => [
				$crossPoint,
				$line[1],
			],
			'p' => $p3,
		];
	}

	protected static function getBezier2CrossPoint($bezir, $line)
	{
		$lp = self::getLineParams($line[0], $line[1]);
		$a = $lp['a'];
		$b = $lp['b'];
		$c = $lp['c'];


		$b0 = $bezir[0];
		$cp = $bezir[1];
		$b1 = $bezir[2];

		$m = ($b * $b1['y']) + ($b * $b0['y']) + ($a * $b0['x']) + ($a * $b1['x']) - (2 * $b * $cp['y']) - (2 * $a * $cp['x']);
		$n = -(2 * $b * $b0['y']) - (2 * $a * $b0['x']) + (2 * $b * $cp['y']) + (2 * $a * $cp['x']);
		$l = ($b * $b0['y']) + ($a * $b0['x']) + $c;

		$tList = [];
		$d = ($n * $n) - (4 * $m * $l);
		if (($m != 0)) {
			if ($d > 0) {
				$d = sqrt($d);
				$t0 = 0.5 * (-$n + $d) / $m;
			    $t1 = 0.5 * (-$n - $d) / $m;

				if (($t0 >= 0) && ($t0 <= 1.0)) {
					$tList[] = $t0;
				}
				if(($t1 >= 0) && ($t1 <= 1.0)){
					$tList[] = $t1;
			    }
			} else if ($d == 0) {
				$t1 = 0.5 * -$n / $m;
				if(($t1 >= 0) && ($t1 <= 1.0)){
					$tList[] = $t1;
			    }
			}
		}

		if (empty($tList)) {
			return null;
		}

		$crossPointList = [];
		foreach ($tList as $t) {
// echo "{$t}<br />";
			$point = self::getBezier2CurvePoint($b0, $b1, $cp, $t);
			$length = self::getLineLength($line ,$point);

			if (($length >= 0.0) && ($length <= 1.0)) {
//echo "len: {$length}<br />";
				$crossPointList[] = [
					'point' => $point,
					'bezierLength' => $t,
					'length' => $length,
				];
			}
		}

		// dd($crossPointList);




		return $crossPointList;
	}

	protected static function getLineLength($line, $point)
	{
		$v = [
			'x' => (float)($line[1]['x'] - $line[0]['x']),
			'y' => (float)($line[1]['y'] - $line[0]['y']),
		];

		$p = [
			'x' => (float)($point['x'] - $line[0]['x']),
			'y' => (float)($point['y'] - $line[0]['y']),
		];

		if ($v['x'] != 0) {
			return ($p['x'] / $v['x']);
		}
		if ($v['y'] != 0) {
			return ($p['y'] / $v['y']);
		}

		return 0;
	}

	public static function testOutlineCurveSelfCross()
	{
		echo '<hr /><h1>アウトライン @self-cross-curve test</h1>';
		$s = '<svg>';
		$s .= ' <path d="M78,6 50,90 10,90 10,50 Q50,0 90,50" fill="none" stroke="blue"/>';
		$s .= '</svg>';

		echo 'もとのSVG<br />'.$s.'<br />';
		dump($s);

		$stroke = self::parseStrokeSvg($s);
//		echo self::testStrokeToSvg($stroke);
		$outline = self::getOutlineFromStroke($stroke);

//		 foreach ($outline as $s) {
//		 	echo self::testOutlineToSvg([$s]);
//		 }

		echo self::testOutlineToSvg($outline, false);
	}

	public static function testOutlineSelfCross()
	{
		echo '<hr />アウトライン @self-cross test<br />';
		$s = '<svg>';
		$s .= ' <path d="M50,1 50,90 10,90 10,50 90,50" fill="none" stroke="black"/>';
		$s .= '</svg>';

echo 'SVG<br />'.$s.'<br />';

		$stroke = self::parseStrokeSvg($s);
		$outline = self::getOutlineFromStroke($stroke);
		echo self::testOutlineToSvg($outline);

	}

	public static function testOutlinePenetrate()
	{

		$s1 = [
			[
				'x' => 30,
				'y' => 30,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 60,
				'y' => 30,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 60,
				'y' => 60,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 30,
				'y' => 60,
				'isOnCurvePoint' => true,
			],
		];
		$s2 = [
			[
				'x' => 60,
				'y' => 60,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 120,
				'y' => 60,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 120,
				'y' => 120,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 60,
				'y' => 120,
				'isOnCurvePoint' => true,
			],
		];


		echo '<h2>アウトライン test-data</h2>';
		$stroke = [
			[
				'path' => [
					[
						'x' => 30,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 100,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 30,
						'y' => 100,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 30,
						'y' => 30,
						'isOnCurvePoint' => true,
					],

				],
				'isClosed' => false,
			],

			[
				'path' => [
					[
						'x' => 50,
						'y' => 10,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 50,
						'y' => 120,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],

		];
		$outline = self::getOutlineFromStroke($stroke);
		echo self::testOutlineToSvg($outline);
		echo '<hr />';



		echo '<h2>アウトライン test-data</h2>';
		$stroke = [
			[
				'path' => [
					[
						'x' => 65,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 65,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 65,
						'y' => 100,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 30,
						'y' => 65,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 65,
						'y' => 30,
						'isOnCurvePoint' => true,
					],

				],
				'isClosed' => false,
			],

			[
				'path' => [
					[
						'x' => 50,
						'y' => 10,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 50,
						'y' => 120,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],

		];


		$outline = self::getOutlineFromStroke($stroke);
		echo self::testOutlineToSvg($outline);
		echo '<hr />';

	}

	public static function testCurveOutlinePenetrate()
	{
		echo '<h1>testCurveOutlinePenetrate</h1>';


		echo '<h2>アウトライン test-data</h2>';
		$stroke = [
			[
				'path' => [
					[
						'x' => 104,
						'y' => 62,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 65,
						'y' => 100,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 30,
						'y' => 65,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 65,
						'y' => 30,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 100,
						'y' => 65,
						'isOnCurvePoint' => true,
					],

				],
				'isClosed' => true,
			],

			[
				'path' => [
					[
						'x' => 50,
						'y' => 10,
						'isOnCurvePoint' => true,
					],
					[
						'x' => 50,
						'y' => 120,
						'isOnCurvePoint' => true,
					],
				],
				'isClosed' => false,
			],

		];

//		$st = new Stroke($stroke);
//		echo $st->toSvg();

		echo self::testStrokeToSvg($stroke);

		$outline = new \FontObscure\Libs\Outline($stroke);
		echo $outline->toSvg();

		echo '<hr />結果<br />';



		return $outline;
	}

	public static function testOutlineA()
	{
		echo '<hr />アウトライン @a <br />';
		$s = '<svg>';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="14" x2="86" y1="21" y2="21" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="38" x2="38" y1="6" y2="81" />';
		$s .= '<path d="M68,40 c-8.337,27.624 -28.133,45 -43,45 c-7.852,0 -13,-4.894 -13,-15 c0,-17.828 17.78,-32 43,-32 C76.039,38 88,48.227 88,63 C88,76.87 77.755,86.868 60,90" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';

// echo $s.'<br />';

		$stroke = self::parseStrokeSvg($s);
		$outline = new \FontObscure\Libs\Outline($stroke);



		dd($outline->shapes);
//
//// dd($outline);
//		$o = [];
//		foreach ($outline as $contour) {
//			$c = [];
//			foreach ($contour as $i => $point) {
//				$flags = 0;
//				if ($point['isOnCurvePoint']) {
//					$flags |= 0x01;
//				}
//				if ($i == 0) {
//					$flags |= 0x01;
//				}
//// $flags = 0x01;
//
//				$c[] = [
//					'x' => $point['x'] * 10,
//					'y' => 500 - $point['y'] * 10,
//					'flags' => $flags,
//				];
//			}
//			if (count($c) > 0) {
//				$o[] = $c;
//			}
//		}
//
//		echo '<h1>結果</h1>';
//
//
//		$hm = new TtfHorizontalMetrix([
//			'advance_width' => 1200,
//			'lsb' => 20,
//		]);
//
//		$g = new TtfGlyph([
//			'glyph_index' => 1,
//			'number_of_contours' => count($o),
//			'x_min' => 0,
//			'y_min' => 0,
//			'x_max' => 300,
//			'y_max' => 300,
//			'coordinates' => $o,
//			'instructions' => [],
//		]);
//
//// dd($g->coordinates);
//		$s = new GlyphSvg($g, $hm);
//		echo '<h1>GlyphSvg</h1>';
//		echo $s->getSvg();

	}


	public static function testOutlineMaya()
	{
		echo '<hr />アウトライン @maya <br />';

		$svgList = [];

		$s = '<svg>';
		$s .= '<path d="M57,6 v63.0 c0,15.188 -5.467,20 -18,20 c-11.587,0 -19,-5.989 -19,-15 c0,-8.021 6.047,-13 19,-13 c9.891,0 24.002,6.69 45,24" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="84" x2="16" y1="40" y2="40" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="87" x2="13" y1="20" y2="20" />';
		$s .= '</svg>';
		$svgList[] = $s;

		$s = '<svg>';
		$s .= '<path d="M48,91 c-6.987,-26.081 -14.347,-51.367 -23,-78" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M7,46 c48.111,-18.469 57.416,-21 66,-21 c11.1,0 18,6.727 18,18 c0,11.315 -7.242,18 -19,18 c-4.578,0 -8.745,-0.591 -14,-2" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<path d="M63,44 c-2.557,-11.075 -5.676,-23.595 -9,-36" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';
		$svgList[] = $s;


echo $s.'<br />';

		$resultList = [];

		foreach ($svgList as $s) {


			$stroke = self::parseStrokeSvg($s);
			echo self::testStrokeToSvg($stroke);


			foreach ($stroke as &$l) {
				foreach ($l['path'] as &$point) {
					if ($point['y'] > 60.0) {
						$point['y'] += 80.0;
					}
					$point['y'] -= 50;
					unset($point);
				}
			}
			unset($l);

			$outline = self::getOutlineFromStroke($stroke);


			echo self::testOutlineToSvg($outline);
			// dd($stroke);

	// dd($outline);
			$o = [];
			foreach ($outline as $contour) {
				$c = [];
				foreach ($contour as $i => $point) {
					$flags = 0;
					if ($point['isOnCurvePoint']) {
						$flags |= 0x01;
					}
					if ($i == 0) {
						$flags |= 0x01;
					}
	// $flags = 0x01;

					$c[] = [
						'x' => $point['x'] * 10,
						'y' => 500 - $point['y'] * 10,
						'flags' => $flags,
					];
				}
				if (count($c) > 0) {
					$o[] = $c;
				}
			}

			echo '<h1>結果</h1>';


			$hm = new TtfHorizontalMetrix([
				'advance_width' => 1200,
				'lsb' => 20,
			]);

			$g = new TtfGlyph([
				'glyph_index' => 1,
				'number_of_contours' => count($o),
				'x_min' => 0,
				'y_min' => 0,
				'x_max' => 300,
				'y_max' => 300,
				'coordinates' => $o,
				'instructions' => [],
			]);

// dd($g->coordinates);

			$s = new GlyphSvg($g, $hm);
			echo '<h1>GlyphSvg</h1>';

			$resultList[] = $s->getSvg();
		}

		foreach ($resultList as $r) {
			echo $r;
		}
	}



	public static function testOutlineMa()
	{
		echo '<hr />アウトライン @ma <br />';
		$s = '<svg>';
		$s .= '<path d="M57,6 v63.0 c0,15.188 -5.467,20 -18,20 c-11.587,0 -19,-5.989 -19,-15 c0,-8.021 6.047,-13 19,-13 c9.891,0 24.002,6.69 45,24" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="84" x2="16" y1="40" y2="40" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="87" x2="13" y1="20" y2="20" />';
		$s .= '</svg>';

echo $s.'<br />';



		$stroke = self::parseStrokeSvg($s);
		$outline = new \FontObscure\Libs\Outline($stroke);


		echo $outline->toSvg();
dd($outline->toSvg());


		echo '<h1>結果</h1>';

//
//		$hm = new TtfHorizontalMetrix([
//			'advance_width' => 1200,
//			'lsb' => 20,
//		]);
//
//		$g = new TtfGlyph([
//			'glyph_index' => 1,
//			'number_of_contours' => count($o),
//			'x_min' => 0,
//			'y_min' => 0,
//			'x_max' => 300,
//			'y_max' => 300,
//			'coordinates' => $o,
//			'instructions' => [],
//		]);
//
//// dd($g->coordinates);
//		$s = new GlyphSvg($g, $hm);
//		echo '<h1>GlyphSvg</h1>';
//		echo $s->getSvg();

	}

	public static function testOutlineToSvg($outline, $isPointEnabled = true, $points = [])
	{
		if (empty($outline)) {
			return '<h1>null outline</h1>';
		}

		$colors = [
			'#dddddd',
			'#a0a0a0',
			'#808080',
		];

		$s = '<svg>';
		$sc = '';
		$s .= '<path d="';
		foreach ($outline as $lineNumber => $line) {

			$c = $colors[0]; //$colors[$lineNumber % count($colors)];
			$s .= 'M ';
			$cCount = 0;
			foreach ($line as $index => $l) {
				if (!$l['isOnCurvePoint']) {
					if ($cCount == 0) {
						$s .= "Q";
						$cCount = 2;
					}
				} else {
					if ($index != 0) {
						if ($cCount == 0) {
							$s .= ' L';
						}
					}
				}
				if ($cCount > 0) {
					$cCount--;
				} else{
					if (!$l['isOnCurvePoint']) {
						// dd('ばぐったー!');
					}
				}
				$s .= "{$l['x']},{$l['y']} ";
			}
			$s .= "{$line[0]['x']},{$line[0]['y']} ";
			$s .= 'z ';
		}

		$s .= '" fill="'.'rgba(200, 200, 200, 0.4)'.'" stroke="#000000" stroke-width="1" />';

		if ($isPointEnabled) {
			foreach ($outline as $lineNumber => $line) {
				foreach ($line as $index => $l) {
					$color = 'blue';
					if ($index == 0) {
						$color = 'red';
					} else if (!$l['isOnCurvePoint']) {
						$color = 'gray';
					}
					$sc .= "<circle cx='{$l['x']}' cy='{$l['y']}' r='2' fill='{$color}' data-index='{$index}'/>";
				}
			}
		}

		foreach ($points as $index => $p) {
			$sc .= "<circle cx='{$p['x']}' cy='{$p['y']}' r='2' fill='green' data-index='{$index}'/>";
		}

		$s .= $sc.'</svg>';

		return $s;
	}

	protected static function testStrokeToSvg($stroke)
	{
		$s = '<svg>';
		$sc = '';

		foreach ($stroke as $line) {
			$s .= '<path d="M ';
			$cCount = 0;
			foreach ($line['path'] as $index => $l) {
				if (!$l['isOnCurvePoint']) {
					if ($cCount == 0) {
						$cCount = 2;
						$s .= "Q";
					}
				}
				if ($cCount > 0) {
					$cCount--;
				}

				$s .= "{$l['x']},{$l['y']} ";
			}
			$s .= '" fill="none" stroke="blue" stroke-width="1" />';
		}
		$s .= $sc.'</svg>';
		return $s;
	}

	protected static function getBezier2ControlPoint($s, $e, $onCurve, $t)
	{
		$paramX = ($s['x'] * pow(1 - $t, 2)) + ($e['x'] * pow($t, 2));
		$paramY = ($s['y'] * pow(1 - $t, 2)) + ($e['y'] * pow($t, 2));

		return [
			'x' => ($onCurve['x'] - $paramX) / ((1 - $t) * $t * 2),
			'y' => ($onCurve['y'] - $paramY) / ((1 - $t) * $t * 2),
		];
	}

	protected static function svgCtoQ($s, $cParams)
	{
		$a = $cParams[0];
		$b = $cParams[1];
		$e = $cParams[2];

		// $t = 0.5;

		$k = self::getBezier3CurvePoint($s, $e, $a, $b, 0.51);

		$aByK = self::getBezier3CurvePoint($s, $e, $a, $b, 0.25);
		$bByK = self::getBezier3CurvePoint($s, $e, $a, $b, 0.75);

		$t = 0.51;
		$param = ($s['x'] * pow(1 - $t, 2)) + ($k['x'] * pow($t, 2));
		$ax = (($aByK['x'] - $param) / ((1 - $t) * $t * 2));
		$param = ($s['y'] * pow(1 - $t, 2)) + ($k['y'] * pow($t, 2));
		$ay = ($aByK['y'] - $param) / ((1 - $t) * $t * 2);

		$t = 0.49;
		$param = ($k['x'] * pow(1 - $t, 2)) + ($e['x'] * pow($t, 2));
		$bx = ($bByK['x'] - $param) / ((1 - $t) * $t * 2);
		$param = ($k['y'] * pow(1 - $t, 2)) + ($e['y'] * pow($t, 2));
		$by = ($bByK['y'] - $param) / ((1 - $t) * $t * 2);
		// dump("new a = {$ax},{$ay}");



		return [
			[
				[
					'x'=> $s['x'],
					'y'=> $s['y'],
					'isOnCurvePoint' => true,
				],
				[
					'x'=> $ax,
					'y'=> $ay,
					'isOnCurvePoint' => false,
				],
				[
					'x'=> $k['x'],
					'y'=> $k['y'],
					'isOnCurvePoint' => true,
				],
			], [
				[
					'x'=> $k['x'],
					'y'=> $k['y'],
					'isOnCurvePoint' => true,
				],
				[
					'x'=> $bx,
					'y'=> $by,
					'isOnCurvePoint' => false,
				],
				[
					'x'=> $e['x'],
					'y'=> $e['y'],
					'isOnCurvePoint' => true,
				],
			],
		];
	}

	protected static function getBezier2CurvePoint($s, $e, $a, $t)
	{
		return [
			'x' => ($s['x'] * pow(1 - $t, 2)) + ($a['x'] * ((1 - $t) * 2) * $t) + ($e['x'] * pow($t, 2)),
			'y' => ($s['y'] * pow(1 - $t, 2)) + ($a['y'] * ((1 - $t) * 2) * $t) + ($e['y'] * pow($t, 2)),
		];
	}

	protected static function getBezier3CurvePoint($s, $e, $a, $b, $t)
	{
		return [
			'x' => ($s['x'] * pow(1 - $t, 3)) + ($a['x'] * (pow(1 - $t, 2) * 3) * $t) + ($b['x'] * (pow($t, 2) * 3 * (1 - $t))) + ($e['x'] * pow($t, 3)),
			'y' => ($s['y'] * pow(1 - $t, 3)) + ($a['y'] * (pow(1 - $t, 2) * 3) * $t) + ($b['y'] * (pow($t, 2) * 3 * (1 - $t))) + ($e['y'] * pow($t, 3)),
		];
	}

	protected static function getMidPoint($start, $end)
	{
		return [
			'x' => $start['x'] + (($end['x'] - $start['x']) / 2),
			'y' => $start['y'] + (($end['y'] - $start['y']) / 2),
		];
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
		if (preg_match_all('/<line\s+[^\/]+\/>/', $svg, $pathSvg)) {
			foreach ($pathSvg[0] as $p) {
				$stroke[] = self::parseLineSvg($p);
			}
		}
		return $stroke;
	}

	public static function parseLineSvg($svg)
	{
		$attrbuteNames = [
			'x1',
			'x2',
			'y1',
			'y2',
		];

		$lineParams = [];
		foreach ($attrbuteNames as $attr) {
			if (preg_match('/'.$attr.'\s*=\s*"(\d+)"/', $svg, $d)) {
				$lineParams[$attr] = (float)$d[1];
			}
		}

		return [
			'path' => [
				[
					'x' => $lineParams['x1'],
					'y' => $lineParams['y1'],
					'isOnCurvePoint' => true,
				],
				[
					'x' => $lineParams['x2'],
					'y' => $lineParams['y2'],
					'isOnCurvePoint' => true,
				],
			],
			'isClosed' => false,
		];

	}

	public static function parsePathSvg($svg)
	{
		$pathParams = [];
		$d = [];
		if (preg_match('/\s+d=["\|\'](.[^"]*)/', $svg, $d)) {
			$pathMatches = [];
			preg_match_all('/([MmCcQqVvHh]\s?)?((-?[\d.]+),?(-?[\d.]+)?)/', $d[1], $pathMatches);

			$spline = [];
			$offCurveCount = 0;
			$isRelativePosition = false;
			$x = null;
			$y = null;
			$prevX = 0;
			$prevY = 0;

			foreach ($pathMatches[0] as $index => $svg) {
				$isOnCurvePoint = true;

				if ($pathMatches[1][$index] == 'c') {
					$isOnCurvePoint = false;
					$offCurveCount = 3;
					$isRelativePosition = true;
				}

				if ($pathMatches[1][$index] == 'C') {
					$isOnCurvePoint = false;
					$offCurveCount = 3;
					$isRelativePosition = false;
				}

				if ($pathMatches[1][$index] == 'q') {
					$isOnCurvePoint = false;
					$offCurveCount = 2;
					$isRelativePosition = true;
				}

				if ($pathMatches[1][$index] == 'Q') {
					$isOnCurvePoint = false;
					$offCurveCount = 2;
					$isRelativePosition = false;
				}

				switch ($pathMatches[1][$index]) {
					case 'v':
						$x = $prevX;
						$y = (float)$pathMatches[3][$index];
						$y += $prevY;
						break;
					case 'V':
						$x = $prevX;
						$y = (float)$pathMatches[3][$index];
						break;
					case 'h':
						$y = $prevY;
						$x = (float)$pathMatches[3][$index];
						$x += $prevY;
						break;
					case 'H':
						$y = $prevY;
						$x = (float)$pathMatches[3][$index];
						break;

					default:
						$x = (float)$pathMatches[3][$index];
						$y = (float)$pathMatches[4][$index];

						if ($isRelativePosition) {
							$x += $prevX;
							$y += $prevY;
						}
						break;
				}


				if ($offCurveCount > 0) {
					$isOnCurvePoint = false;
					$offCurveCount--;
					$spline[] = [
						'x' => $x,
						'y' => $y,
						'isOnCurvePoint' => ($offCurveCount == 0),
					];
					if ($offCurveCount <= 0) {
						$isOnCurvePoint = true;
						if (count($spline) <= 2) {
							$pathParams[] = $spline[0];
							$pathParams[] = $spline[1];
						} else {
							$cList = self::svgCtoQ(['x'=>$prevX, 'y'=>$prevY], $spline);
							foreach ($cList as $c) {
								$pathParams[] = $c[1];
								$pathParams[] = $c[2];
							}
						}

						$prevX = $x;
						$prevY = $y;
						$spline = [];
					}
				} else {
					$prevX = $x;
					$prevY = $y;

					$pathParams[] = [
						'x' => $x,
						'y' => $y,
						'isOnCurvePoint' => true, //$isOnCurvePoint,
					];
				}

			}
// dd($pathParams);
			$isClosed = preg_match('/[zZ]\s*$/', $d[1]);
			return [
				'path' => $pathParams,
				'isClosed' => $isClosed,
			];
		}

		return [];
	}


	protected static function strokeToShapeList($stroke)
	{
		$outline = [];
		$thickness = 4; // 太さ
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
						'x' => $l['x'] + ($n['x'] * $thickness),
						'y' => $l['y'] + ($n['y'] * $thickness),
						'isOnCurvePoint' => $l['isOnCurvePoint'],
					];
					$down = [
						'x' => $l['x'] + ($n['x'] * -$thickness),
						'y' => $l['y'] + ($n['y'] * -$thickness),
						'isOnCurvePoint' => $l['isOnCurvePoint'],
					];
				} else if ($index == $maxLineIndex) {
					$lPrev = $line['path'][$prevIndex];
					$n = self::getNormal($lPrev, $l);

					$up = [
						'x' => $l['x'] + ($n['x'] * $thickness),
						'y' => $l['y'] + ($n['y'] * $thickness),
						'isOnCurvePoint' => $l['isOnCurvePoint'],
					];
					$down = [
						'x' => $l['x'] + ($n['x'] * -$thickness),
						'y' => $l['y'] + ($n['y'] * -$thickness),
						'isOnCurvePoint' => $l['isOnCurvePoint'],
					];
				} else {

					$lPrev = $line['path'][$prevIndex];
					$lNext = $line['path'][($index + 1) % $lineCount];
					$up = self::getOutlinePoint($lPrev, $l, $lNext, $thickness);
					$down = self::getOutlinePoint($lPrev, $l, $lNext, -$thickness);

				}

				$outlineUp[] = [
					'x' => $up['x'],
					'y' => $up['y'],
					'isOnCurvePoint' => $up['isOnCurvePoint'],
				];
				array_unshift($outlineDown, [
					'x' => $down['x'],
					'y' => $down['y'],
					'isOnCurvePoint' => $down['isOnCurvePoint'],
				]);
			}

			$shape = array_merge($outlineUp, $outlineDown);
			$outline[] = $shape;
		}

		return $outline;
	}



	protected static function composeShapesEx($base, $addition)
	{
//echo '<h1>composeShapesEx</h1>';
//echo 'base / addition<br />';
//echo self::testOutlineToSvg([$base]);
//echo self::testOutlineToSvg([$addition]);
//dump(compact('base', 'addition'));
//echo '<br />';

		$baseInfo = [];
		foreach ($base as $p) {
			$baseInfo[] = [
				'point' => $p,
				'crossInfo' => [],
				'index' => null,
			];
		}
		$baseCount = count($base);

		$additionInfo = [];
		foreach ($addition as $a) {
			$additionInfo[] = [
				'point' => $a,
				'crossInfo' => [],
				'index' => null,
			];
		}
		$additionCount = count($addition);

		$infoList = [
			&$baseInfo,
			&$additionInfo,
		];


		$crossCount = 0;
		for ($oc = 0; $oc < $baseCount; $oc++) {
			$p = &$baseInfo[$oc]['crossInfo'];

			$crossList = self::getCorssInfoListByShape($base, $oc, $addition);
			if (!empty($crossList)) {
				$crossCount += count($crossList);
				foreach ($crossList as $cp) {
					$a = &$additionInfo[$cp['index']]['crossInfo'];
					$indexBase = count($p);
					$indexAddition = count($a);

					$p[$indexBase] = [
						'length' => $cp['point']['length'],
						'point' => [
							'x' => $cp['point']['x'],
							'y' => $cp['point']['y'],
						],
						'to' => null,
						'index' => null,
					];
					$a[$indexAddition] = [
						'length' => $cp['point']['length2'],
						'point' => [
							'x' => $cp['point']['x'],
							'y' => $cp['point']['y'],
						],
						'to' => null,
						'index' =>null,
					];

					$p[$indexBase]['to'] = &$a[$indexAddition];
					$a[$indexAddition]['to'] = &$p[$indexBase];

					unset($a);
				}

			}
			unset($p);
		}


		if ($crossCount < 2) {
			return null;
		}

		foreach ($infoList as &$sortInfo) {
			foreach ($sortInfo as &$list) {
				usort($list['crossInfo'], function($a, $b) {
					if ($a['length'] > $b['length']) {
						return 1;
					}
					if ($a['length'] < $b['length']) {
						return -1;
					}
					return 0;
				});
			}
			unset($list);
		}
		unset($sortInfo);

		foreach ($infoList as &$infos) {
			$index = 0;
			foreach ($infos as &$p) {
				$p['index'] = $index;
				$index++;
				foreach ($p['crossInfo'] as &$list) {
					$list['index'] = $index;
					$index++;
				}
				unset($list);
			}
			unset($p);
		}
		unset($infos);


		$shapeList = [];
		$corssIndexInfoList = [];
		foreach ($infoList as $list) {
			$shape = [];
			$corssIndexInfo = [];
			foreach ($list as $p) {
				$shape[] = $p['point'];
				$corssIndexInfo[] = -1;
				foreach ($p['crossInfo'] as $list) {
					$shape[] = [
						'x' => $list['point']['x'],
						'y' => $list['point']['y'],
						'isOnCurvePoint' => true,
					];
					$corssIndexInfo[] = $list['to']['index'];
				}
			}
			$shapeList[] = $shape;
			$corssIndexInfoList[] = $corssIndexInfo;
		}

		$composed = [];
		foreach ($shapeList as $shapeIndex => $shape) {
			$newShapeList = [];

			$corssIndexInfo = $corssIndexInfoList[$shapeIndex];
			$count = count($shape);

			$passedIndexInfo = [];
			foreach ($corssIndexInfo as $to) {
				if ($to < 0) {
					$passedIndexInfo[] = false;
				} else {
					$passedIndexInfo[] = true;
				}
			}

			$otherCorssIndexInfo = $corssIndexInfoList[1- $shapeIndex];
			$otherShape = $shapeList[1 - $shapeIndex];
			$otherCount = count($otherShape);

			$firstIndex = array_search(false, $passedIndexInfo);
			while ($firstIndex !== false) {
				$index = $firstIndex;
				$newShape = [];
				for ($i = 0; $i < $count; $i++) {
					$isEnd = false;
					$newShape[] = $shape[$index];
					$passedIndexInfo[$index] = true;

					$otherIndex = $corssIndexInfo[$index];
					if ($otherIndex > -1) {
						$otherIndex = ($otherIndex + 1) % $otherCount;
						for ($oc = 0; $oc < $otherCount; $oc++) {
							$newShape[] = $otherShape[$otherIndex];
							$crossTo = $otherCorssIndexInfo[$otherIndex];
							if ($crossTo > -1) {
								$index = $crossTo;
								if ($index == $firstIndex) {
									$isEnd = true;
								}
								break;
							}

							$otherIndex = ($otherIndex + 1) % $otherCount;
						}
					}

					$index = ($index + 1) % $count;
					if ($index == $firstIndex) {
						$isEnd = true;
					}

					if ($isEnd) {
						break;
					}
				}

				$newShapeList[] = $newShape;
				$firstIndex = array_search(false, $passedIndexInfo);
			}

			$composed[] = $newShapeList;
		}


//foreach ($newShapeList as $i => $s) {
//	$crossPointList = [];
//	foreach ($infoList[$i] as $list) {
//		foreach ($list['crossInfo'] as $list) {
//			$crossPointList[] = $list['point'];
//		}
////dd($pl);
//	}
//	echo self::testOutlineToSvg([$s], true, $crossPointList);
//}
//

		return [
			'base' => $composed[0],
			'addition' => $composed[1],
		];
	}

	protected static function getCorssInfoListByShape($base, $index, $addition)
	{
		$baseCount = count($base);
		$additionCount = count($addition);

		$v = [
			$base[$index],
			$base[($index + 1) % $baseCount],
		];

		$corssInfoList = [];
		for ($i = 0; $i < $additionCount; $i++) {
			$p = $addition[$i];
			$next = $addition[($i + 1) % $additionCount];

			// TODO:  曲線に対応
			$cp = self::getCrossPointEx($v, [$p, $next]);
			if (!empty($cp)) {
				$corssInfoList[] =[
					'index' => $i,
					'point' => $cp,
				];
			}

		}

		return $corssInfoList;
	}

	protected static function getOutlineFromStroke($stroke)
	{
//echo '<hr />';
//echo '<h2>getOutlineFromStroke</h2>';
		$outline = [];

		$shapeList = self::strokeToShapeList($stroke);

echo '<hr />交差あり<br />';
echo self::testOutlineToSvg($shapeList, false);
//dump($shapeList);

		$slicedShapeOutlineList = self::getNonCrossingOutline($shapeList);

//echo '<hr /><h2>sliced</h2>';
//echo self::testOutlineToSvg($slicedShapeOutlineList);
// die;

echo '<hr />clockとanticlockを分ける<br />';

		$clockwiseShapeList = [];
		$anticlockwiseShapeList = [];
		foreach ($slicedShapeOutlineList as $shape) {
			$direction = self::getShapeDirection($shape);
			if ($direction > 0) {
				$clockwiseShapeList[] = $shape;
			} else if ($direction < 0) {
				$anticlockwiseShapeList[] = $shape;
			}
		}

		echo self::testOutlineToSvg($clockwiseShapeList);
		echo self::testOutlineToSvg($anticlockwiseShapeList);
		echo '<hr />';

		//////

//dump(compact('clockwiseShapeList', 'anticlockwiseShapeList'));

//		 echo '<hr />clockとanticlockの合成<br />';

		// base / addition の shapeが XOR関係になるようにする
		$_nextClockwise = [];
		foreach ($clockwiseShapeList as $base) {
			$_nextAniticock = [];

			if (!empty($anticlockwiseShapeList)) {
				foreach ($anticlockwiseShapeList as $addition) {

					// TODO: compose, acを同時に作る
					$composed = self::composeShapesEx($base, $addition);
//					$ac = self::composeShapes($addition, $base);

					if (!is_null($composed)) {
//dd($composed);
						$isComposed = true;

//echo 'composed....<br />';
//echo self::testOutlineToSvg($composed['base']);
//echo self::testOutlineToSvg($composed['addition']);
//echo '<br />';
						$_nextClockwise = array_merge($_nextClockwise, $composed['base']);
						$_nextAniticock = array_merge($_nextAniticock, $composed['addition']);
					} else {
						// TODO: base / addition の shapeが XOR関係になるようにする
						$_nextClockwise[] = $base;
						$_nextAniticock[] = $addition;
					}
				}
				$anticlockwiseShapeList = $_nextAniticock;
			} else {
				$_nextClockwise[] = $base;
			}
		}
		$clockwiseShapeList = $_nextClockwise;

		// $a = $clockwiseShapeList;
		// foreach ($clockwiseShapeList as $shape) {
		// 	$_a = [];
		// 	foreach ($a as $s2) {
		// 		$composed = self::composeShapes($shape, $s2);
		// 		if (!is_null($composed)) {
		// 			$_a = array_merge($_a, $composed);
		// 		} else {
		// 			$_a[] = $s2;
		// 		}
		// 	}
		// 	$a = $_a;
		// }
		// $clockwiseShapeList = $a;

echo '<h1>同方向の合成</h1>';
echo self::testOutlineToSvg($clockwiseShapeList);
echo self::testOutlineToSvg($anticlockwiseShapeList);
echo '<hr />';

//die;

		// 時計回りシェイプの合成
		$_next = [];
		while (!empty($clockwiseShapeList)) {
			$c = array_shift($clockwiseShapeList);

			$isComposed = false;
			$_nextNext = [];
			foreach ($_next as $c2) {
				if (!$isComposed) {

//					$composed = self::compose($c, $c2);
					$composed = self::composeShapesEx($c, $c2);
					if (!is_null($composed)) {
						$composed = $composed['base'][0];
					}

//dump(self::composeShapesEx($c, $c2));


					if (!is_null($composed)) {
// dump(compact('composed'));
						$isComposed = true;
						// $clockwiseShapeList = array_merge($clockwiseShapeList, $composed);
						$clockwiseShapeList[] = $composed;
					} else {
						$_nextNext[] = $c2;
					}
				} else {
					$_nextNext[] = $c2;
				}
			}

			if (!$isComposed) {
				$_nextNext[] = $c;
			}
			$_next = $_nextNext;

// echo '<hr />'.self::testOutlineToSvg($_next).'<hr />';

		}
		$clockwiseShapeList = $_next;

// echo self::testOutlineToSvg($clockwiseShapeList).'<hr />';



		$outline = array_merge($clockwiseShapeList, $anticlockwiseShapeList);
// echo self::testOutlineToSvg($outline);

		$outline = self::removeLostedShape($outline);
		return $outline;
	}

	protected static function getNonCrossingOutline($crossingOutline)
	{
		$outline = [];
		foreach ($crossingOutline as $shape) {
			$outline = array_merge($outline, self::sliceShape($shape));
		}
		return $outline;
	}

	protected static function insertSelfCorssPointToShape($shape)
	{
		$pointsCount = count($shape);
		$corssToList = array_fill(0, $pointsCount, []);


		$shapeCrossedPointList = [];
		foreach ($shape as $p) {
			$shapeCrossedPointList[] = [
				'point' => $p,
				'crossedList' => [],
			];
		}

		for ($index = 0; $index < $pointsCount; $index++) {
			$crossPointBase = &$shapeCrossedPointList[$index];

			if (!$shape[$index]['isOnCurvePoint']) {
				continue;
			}

			$crossInfo = self::getSelfCrossInfoList($shape, $index, $corssToList[$index]);
			if (!empty($crossInfo)) {

				foreach ($crossInfo['crossPoints'] as $cp) {
//dump($crossInfo);
					$crossPointOther = &$shapeCrossedPointList[$cp['index']];

					$corssToList[$index][] = $cp['index'];
					$corssToList[$cp['index']][] = $index;

					$crossPointBaseCount = count($crossPointBase['crossedList']);
					$crossPointBase['crossedList'][$crossPointBaseCount] = [
						'point' => [
							'x' => $cp['point']['x'],
							'y' => $cp['point']['y'],
						],
						'length' => $cp['point']['length'],
						'index' => null,
						'to' => null,
					];

					$crossPointOtherCount = count($crossPointOther['crossedList']);
					$crossPointOther['crossedList'][$crossPointOtherCount] = [
						'point' => [
							'x' => $cp['point']['x'],
							'y' => $cp['point']['y'],
						],
						'length' => $cp['point']['length2'],
						'index' => null,
						'to' => null,
					];

					$crossPointBase['crossedList'][$crossPointBaseCount]['to'] = &$crossPointOther['crossedList'][$crossPointOtherCount];
					$crossPointOther['crossedList'][$crossPointOtherCount]['to'] = &$crossPointBase['crossedList'][$crossPointBaseCount];


//					$crossPointBase['crossedList'][] = null;
//					$crossPointOther['crossedList'][] = null;

					unset($crossPointOther);
				}

			}
		}
		unset($crossPointBase);

//echo '<h1>$shape</h1>';
//$testPoints = [];
//foreach ($shapeCrossedPointList as $tp) {
//	foreach ($tp['crossedList'] as $tp2) {
//		$testPoints[] = $tp2['point'];
//	}
//}
//dump($shapeCrossedPointList);
//echo self::testOutlineToSvg([$shape], true, $testPoints).'<br />';


		foreach ($shapeCrossedPointList as &$p) {
			usort($p['crossedList'], function($a, $b) {
				if ($a['length'] < $b['length']) {
					return -1;
				}
				if ($a['length'] > $b['length']) {
					return 1;
				}
				return 0;
			});
		}
		unset($p);

		$index = 0;
		$indexList = [];
//		foreach ($shapeCrossedPointList as $i => &$p) {
		for ($i = 0; $i < count($shapeCrossedPointList); $i++) {
			$p = $shapeCrossedPointList[$i];

//$indexList[$i] = $index;
//echo "index={$index} (org:{$i})<br />";
//echo ' ...has '.count($p['crossedList']).' corsses<br />';
			$next = $shapeCrossedPointList[($i + 1) % count($shapeCrossedPointList)];
			$index++;

			if (!empty($p['crossedList'])) {
				if (!$next['point']['isOnCurvePoint']) {
					$index++;
				}
				foreach ($p['crossedList'] as &$c) {
					$c['index'] = $index;
					$index++;
					if (!$next['point']['isOnCurvePoint']) {
//echo 'on curve...<br />';
						$index++;
					}
				}
				unset($c);

				if (!$next['point']['isOnCurvePoint']) {
					$i++;
				}

//$indexList[$i] = 'skipped';
			}

		}
		unset($p);

//echo '<hr />';
		$index = 0;
		foreach ($shapeCrossedPointList as $i => $s) {
//echo "index:{$indexList[$i]} *****************<br />";
			$index++;
			foreach ($s['crossedList'] as $c) {
//echo "...add-index{$c['index']}<br />";
				$index++;
			}
		}

//dd($shapeCrossedPointList);

		$newShape = [];
		$crossInfo = [];

		$shapePointCount = count($shapeCrossedPointList);
		for ($index = 0; $index < $shapePointCount; $index++) {
//echo '<hr />';
//echo "<h1>{$indexList[$index]}</h1>";
			$p = $shapeCrossedPointList[$index];
//dump($p);
//echo '<hr />';
			$next = $shapeCrossedPointList[($index + 1) % $shapePointCount];
			$newShape[] = $p['point'];
			$crossInfo[] = -1;

			if ($next['point']['isOnCurvePoint']) {
				foreach ($p['crossedList'] as $c) {
					$newShape[] = [
						'x' => $c['point']['x'],
						'y' => $c['point']['y'],
						'isOnCurvePoint' => true,
					];
					$crossInfo[] = $c['to']['index'];
				}
			} else {
				if (!empty($p['crossedList'])) {
					$end = $shapeCrossedPointList[($index + 2) % $shapePointCount];
					$s = 0.0;
					foreach ($p['crossedList'] as $c) {
						$bezierSegment = self::getBezier2Segmet([$p['point'], $next['point'], $end['point']], $s, $c['length']);
						$newShape[] = [
							'x' => $bezierSegment[1]['x'],
							'y' => $bezierSegment[1]['y'],
							'isOnCurvePoint' => false,
						];
						$crossInfo[] = -1;

						$newShape[] = [
							'x' => $c['point']['x'],
							'y' => $c['point']['y'],
							'isOnCurvePoint' => true,
						];
						$crossInfo[] = $c['to']['index'];
						$s = $c['length'];
					}

					$bezierSegment = self::getBezier2Segmet([$p['point'], $next['point'], $end['point']], $s, 1.0);
					$newShape[] = [
						'x' => $bezierSegment[1]['x'],
						'y' => $bezierSegment[1]['y'],
						'isOnCurvePoint' => false,
					];
					$crossInfo[] = -1;


					$index++;
				} else {
					$newShape[] = $next['point'];
					$crossInfo[] = -1;
					$index++;
				}

			}
		}

//dd(compact('newShape', 'crossInfo'));

//echo '<h1>test</h1>';
//echo self::testOutlineToSvg([$newShape], true);
		return [
			'shape' => $newShape,
			'crossInfo' => $crossInfo,
		];
	}

	protected static function getBezier2Segmet($bezier, $start, $end)
	{
		$s = $bezier[0];
		$a = $bezier[1];
		$e = $bezier[2];

		$middle = $start + (($end - $start) / 2);

		$pointStart = self::getBezier2CurvePoint($s, $e, $a, $start);
		$pointEnd = self::getBezier2CurvePoint($s, $e, $a, $end);
		$pointMiddle = self::getBezier2CurvePoint($s, $e, $a, $middle);

		$onCurvePoint = [
			'x' => $pointStart['x'] + (($pointEnd['x'] - $pointStart['x']) / 2),
			'y' => $pointStart['y'] + (($pointEnd['y'] - $pointStart['y']) / 2),
		];

		$newControlPoint = [
			'x' => $onCurvePoint['x'] + (($pointMiddle['x'] - $onCurvePoint['x']) * 2),
			'y' => $onCurvePoint['y'] + (($pointMiddle['y'] - $onCurvePoint['y']) * 2),
		];


		return [
			$pointStart,
			$newControlPoint,
			$pointEnd,
		];
	}

	protected static function sliceShape($shapePointList)
	{
// echo '<h2>sliceShape</h2>';
// echo self::testOutlineToSvg([$shapePointList]);
// echo '<hr />';
		$shapeInfo = self::insertSelfCorssPointToShape($shapePointList);
//dump($shapeInfo);
		$slicedShapeList = [];

		$points = $shapeInfo['shape'];
		$infos = $shapeInfo['crossInfo'];
		$pointsCount = count($points);
		$isPointPassedList = [];

//dd($points);
		foreach ($infos as $i => $s) {
			$p = $points[$i];
			if (($s > -1) || (!$p['isOnCurvePoint'])) {
				$isPointPassedList[] = true;
			} else {
				$isPointPassedList[] = false;
			}
		}

		$firstIndex = 0;
		while($firstIndex !== false) {
			$slicedShape = [];
			$index = $firstIndex;
			for ($i = 0; $i < $pointsCount + 1; $i++) {
				$isPointPassedList[$index] = true;

				$p = $points[$index];
				$slicedShape[] = $p;

				if ($infos[$index] > -1) {
					$index = $infos[$index];
				}

				$index = ($index + 1) % $pointsCount;
				if ($index == $firstIndex) {
					break;
				}
			}
			$slicedShapeList[] = $slicedShape;
			$firstIndex = array_search(false, $isPointPassedList);
//break;
		}

		return $slicedShapeList;
	}

	protected static function getSelfCrossInfoList($shapePointList, $index, $ignoreIndexList = [])
	{
//echo "<h3>getSelfCrossInfoList(list, index={$index}, ignore)</h3>";
//dump(compact('ignoreIndexList'));
		$pointsCount = count($shapePointList);

		$v = [
			$shapePointList[$index],
			$shapePointList[($index + 1) % $pointsCount],
		];


		if (!$v[1]['isOnCurvePoint']) {
			$v[] = $shapePointList[($index + 2) % $pointsCount];

		}

		$otherIndex = ($index + count($v)) % $pointsCount;
		$endCount = $pointsCount - (count($v) + 2);

		$crossPoints = [];
		for ($i = 0; $i < $endCount; $i++) {
			$p = $shapePointList[$otherIndex];
			$next = $shapePointList[($otherIndex + 1) % $pointsCount];

			if (!$p['isOnCurvePoint']) {
				$otherIndex = ($otherIndex + 1) % $pointsCount;
				continue;
			}

			if (in_array($otherIndex, $ignoreIndexList)) {
				//

			}

			if (!in_array($otherIndex, $ignoreIndexList)) {
//echo "<h4>other-index={$otherIndex}</h4>";

				if ($v[1]['isOnCurvePoint']) {
					$point = null;
					if ($next['isOnCurvePoint']) {
						$cp = self::getCrossPoint($v, [$p, $next]);
						if (!is_null($cp)) {
//echo "<br /> ...HIT to other-index={$otherIndex} !";
//dump($cp);
							$crossPoints[] = [
								'point' => $cp,
								'index' => $otherIndex,
							];
						}
						$otherIndex = ($otherIndex + 1) % $pointsCount;
					} else {
						$end = $shapePointList[($otherIndex + 2) % $pointsCount];
						$cpList = self::getBezier2CrossPoint([$p, $next, $end], $v);
//echo 'cpList=<br />';
//dump($cpList);
						if (!empty($cpList)) {
//echo "<br /> ...HIT to other-index={$otherIndex} !(curve)";
//dump($cpList);
							foreach ($cpList as $cp) {
								$point = $cp['point'];
								$crossPoints[] = [
									'point' => [
										'x' => $point['x'],
										'y' => $point['y'],
										'length' => $cp['length'],
										'length2' => $cp['bezierLength'],
									],
									'index' => $otherIndex,
								];
							}
						}
						$otherIndex = ($otherIndex + 2) % $pointsCount;
						$i++;
					}
				} else {

					if ($next['isOnCurvePoint']) {
						$cpList = self::getBezier2CrossPoint($v, [$p, $next]);
						if (!empty($cpList)) {
//echo '<h4>HIT! by curve</h4>';
//dump($cpList);
							foreach ($cpList as $cp) {
								$point = $cp['point'];
								$crossPoints[] = [
									'point' => [
										'x' => $point['x'],
										'y' => $point['y'],
										'length' => $cp['bezierLength'],
										'length2' => $cp['length'],
									],
									'index' => $otherIndex,
								];
							}
						}
						$otherIndex = ($otherIndex + 1) % $pointsCount;
					} else {
						$otherIndex = ($otherIndex + 1) % $pointsCount;
					}
				}
			} else {
				if ($next['isOnCurvePoint']) {
					$otherIndex = ($otherIndex + 1) % $pointsCount;
				} else {
					$otherIndex = ($otherIndex + 2) % $pointsCount;
					$i++;
				}
			}
		}

//dump($crossPoints);
// echo "<br /> -> hit-result: {$crossInfo['index']}<hr />";
		return [
			'crossPoints' => $crossPoints,
		];
	}

	protected static function getSelfCrossInfo($shapePointList, $index, $v, $ignoreIndex = -1)
	{
// echo "<h4>getSelfCrossInfo(list, {$index}, v, {$ignoreIndex})</h4>";
		$pointsCount = count($shapePointList);

		$crossInfo = null;
		$index = ($index + 2) % $pointsCount;
		for ($i = 0; $i < ($pointsCount - 3); $i++) {
			$p = $shapePointList[$index];
			if ($p['isOnCurvePoint'] || true) {
				if ($index != $ignoreIndex) {
// echo "<b>index={$index}</b>";
					$next = $shapePointList[($index + 1) % $pointsCount];

					$point = null;
					if ($next['isOnCurvePoint'] || true) {
// echo '<br />';
						$point = self::getCrossPoint($v, [$p, $next]);
// if (!is_null($point)) {
// 	echo "<br /> ...hit!";
// }
					} else {
// echo '<br />(its curve...)';
						// $point = self::getCrossPoint($v, [$p, $next]);

						$end = $shapePointList[($index + 2) % $pointsCount];
						$pointList = self::getBezier2CrossPoint([$p, $next, $end], $v);
						// $index++;
						// $i++;
						$cp = self::getNearestCrossPoint($pointList);
						if (!is_null($cp)) {
// echo "<br /> ...HIT!(curve)";
// dd($cp);
							$point = $cp['point'];
							$point['length'] = $cp['length'];
						}
					}

					if (!is_null($point)) {
						if (is_null($crossInfo)) {
							$crossInfo = [
								'point' => $point,
								'index' => $index,
							];
						} else {
							if ($point['length'] <= $crossInfo['point']['length']) {
								$crossInfo = [
									'point' => $point,
									'index' => $index,
								];
							}
						}
					}
				}
			}

			$index = ($index + 1) % $pointsCount;
		}
// echo "<br /> -> hit-result: {$crossInfo['index']}<hr />";
		return $crossInfo;
	}

	protected static function getShapeDirection($shape)
	{
		$pointCount = count($shape);
		$sum = 0;
		for ($i = 0; $i < $pointCount; $i++) {
			$v1 = [
				$shape[$i],
				$shape[($i + 1) % $pointCount],
			];
			$v2 = [
				$shape[($i + 1) % $pointCount],
				$shape[($i + 2) % $pointCount],
			];
			$d = self::crossProduct($v1, $v2);
			if ($d > 0) {
				$d = 1;
			} else if ($d < 0) {
				$d = -1;
			}
			$sum += $d;
		}

		if ($sum > 0) {
			return 1;
		} else if ($sum < 0){
			return -1;
		}

		return 0;
	}

	protected static function createDirectionList($shapeList)
	{
		$directionList = [];
		foreach ($shapeList as $lineIndex => $line) {
			$directionList[] = self::getShapeDirection($line);
		}
		return $directionList;
	}

	protected static function removeLostedShape($outline)
	{
		$directionList = self::createDirectionList($outline);

		$aliveShapes = [];
		foreach ($outline as $i => $line) {
			$outsideIndex = self::getOutsideShapeIndex($outline, $i);
			if ($outsideIndex > -1) {
				if ($directionList[$i] == $directionList[$outsideIndex]) {
					continue;
				}
			}

			$aliveShapes[] = $line;
		}

		return $aliveShapes;
	}

	protected static function getOutsideShapeIndex($outline, $insideShapeIndex)
	{
		$lineList = $outline[$insideShapeIndex];
		$p = $lineList[0];
		$v = [
			$p,
			['x' => $p['x'] + 10, 'y' => $p['y']],
		];
// echo "<hr /><h2>shape:{$insideShapeIndex}</h2>";
// echo self::testOutlineToSvg($outline);
// echo self::testOutlineToSvg([$outline[$insideShapeIndex]]);
// echo '<br />';

		$crossInfo = null;
		foreach ($outline as $i => $line) {
			if ($i != $insideShapeIndex) {

				$crossCount = 0;
				$pointCount = count($line);
				$crossInfoLine = null;
// echo "{$insideShapeIndex}→{$i}<br />";

				for ($index = 0; $index < $pointCount; $index++) {
					$p = $line[$index];
					$next = $line[($index + 1) % $pointCount];
					$crossPoint = self::getCrossPointByRay($v, [$p, $next]);
					if (!is_null($crossPoint)) {
// echo "... hit! (shape:{$i}, index={$index}, len={$crossPoint['length']})<br />";
						$crossCount++;
						if ($crossPoint['length'] > 0) {
							if (is_null($crossInfoLine)) {
								$crossInfoLine = [
									'shapeIndex' => $i,
									'point' => $crossPoint,
								];
							} else {
								if ($crossInfoLine['point']['length'] > $crossPoint['length']) {
									$crossInfoLine = [
										'shapeIndex' => $i,
										'point' => $crossPoint,
									];
								}
							}
						}
					}
				}
// echo "... {$insideShapeIndex}→{$i} : count={$crossCount}<br />";
				if (($crossCount % 2) != 0) {
					if (!is_null($crossInfoLine)) {
						if (is_null($crossInfo)) {
							$crossInfo = $crossInfoLine;
						} else {
							if ($crossInfo['point']['length'] > $crossInfoLine['point']['length'] ) {
								$crossInfo = $crossInfoLine;
							}
						}
					}
				}
			}
		}

// dump(compact('insideShapeIndex', 'crossInfo'));
		if (is_null($crossInfo)) {
// echo 'result: no hit..<br />';
			return -1;
		}
// echo "result: hit to shape{$crossInfo['shapeIndex']}<br />";
		return $crossInfo['shapeIndex'];
	}

	protected static function isInsideShapePoint($shape, $point)
	{
		$v = [
			$point,
			['x' => $point['x'] + 10, 'y' => $point['y']],
		];

		$crossCount = 0;
		$pointCount = count($shape);
		$crossInfoLine = null;
		for ($index = 0; $index < $pointCount; $index++) {
			$p = $shape[$index];
			$next = $shape[($index + 1) % $pointCount];

			$crossPoint = self::getCrossPointByRay($v, [$p, $next]);
			if (!is_null($crossPoint)) {
				if ($crossPoint['length'] > 0) {
					$crossCount++;
				}
			}
		}

		if (($crossCount % 2) > 0) {
			return true;
		}

		return false;
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
		$point = self::getCrossPointToOutsideOfVector([$prevO1, $prevO2], [$o1, $o2]);
		if (is_null($point)) {
			$point = $o1;
		}

		return [
			'x' => $point['x'],
			'y' => $point['y'],
			'isOnCurvePoint' => $currentPoint['isOnCurvePoint'],
		];
	}

	protected static function getNormal($start, $end)
	{
		// dump(compact('start', 'end'));

		$vector = [
			'x' => $end['x'] - $start['x'],
			'y' => $end['y'] - $start['y'],
		];

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
		$compoased = self::compose($rectangle1, $rectangle2);
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


	public static function getCrossPoint($v1, $v2)
	{
// dump('getCrossPoint *********************');
// dump($v1);
// dump($v2);

		$a = self::crossProduct(
			$v1,
			[$v1[0], $v2[0]]
		);
		$b = self::crossProduct(
			$v1,
			[$v2[1], $v1[0]]
		);

		$ab = $a + $b;
		if ($ab) {
			$length2  = ($a / $ab);
			if (($length2 < 0) || ($length2 > 1.0)) {
				return null;
			}
		} else {
			return null;
		}


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
		if (($crossVectorLengthBase < 0) || ($crossVectorLengthBase > 1.0)) {
			return null;
		}

		$crossed = [
			'x' => $v1[0]['x'] + (($v1[1]['x'] - $v1[0]['x']) * $crossVectorLengthBase),
			'y' => $v1[0]['y'] + (($v1[1]['y'] - $v1[0]['y']) * $crossVectorLengthBase),
			'length' => $crossVectorLengthBase,
			'length2' => $length2,
		];

		return $crossed;
	}

	public static function getCrossPointEx($v1, $v2)
	{
// dump('getCrossPoint *********************');
// dump($v1);
// dump($v2);

		$a = self::crossProduct(
			$v1,
			[$v1[0], $v2[0]]
		);
		$b = self::crossProduct(
			$v1,
			[$v2[1], $v1[0]]
		);

		$ab = $a + $b;
		if ($ab) {
			$length2  = ($a / $ab);
			if (($length2 < 0) || ($length2 >= 1.0)) {
				return null;
			}
		} else {
			return null;
		}


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
		if (($crossVectorLengthBase < 0) || ($crossVectorLengthBase >= 1.0)) {
			return null;
		}

		$crossed = [
			'x' => $v1[0]['x'] + (($v1[1]['x'] - $v1[0]['x']) * $crossVectorLengthBase),
			'y' => $v1[0]['y'] + (($v1[1]['y'] - $v1[0]['y']) * $crossVectorLengthBase),
			'length' => $crossVectorLengthBase,
			'length2' => $length2,
		];

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

		return $crossed;
	}

	protected static function isInRectanble($rectangle, $point)
	{
		$s = [
			'x' => min($rectangle[0]['x'], $rectangle[1]['x']),
			'y' => min($rectangle[0]['y'], $rectangle[1]['y']),
		];

		$e = [
			'x' => max($rectangle[0]['x'], $rectangle[1]['x']),
			'y' => max($rectangle[0]['y'], $rectangle[1]['y']),
		];

		if ($s['x'] < 0) {

		}

	}

	public static function getCrossPointByRay($v1, $v2)
	{
// dump('getCrossPoint *********************');
// dump($v1);
// dump($v2);
		$a = self::crossProduct(
			$v1,
			[$v1[0], $v2[0]]
		);
		$b = self::crossProduct(
			$v1,
			[$v2[1], $v1[0]]
		);

		if (($a == 0) && ($b == 0)) {
// echo '平行<br />';
			$crossPointInfo = null;
			$baseLength = (($v1[1]['x'] - $v1[0]['x']) ** 2) + (($v1[1]['y'] - $v1[0]['y']) ** 2);
			foreach ($v2 as $i => $p) {
				$length = (($p['x'] - $v1[0]['x']) ** 2) + (($p['y'] - $v1[0]['y']) ** 2);
				if (is_null($crossPointInfo)) {
					$crossPointInfo = [
						'point' => $p,
						'length' => $length,
						'i' => $i,
					];
				} else {
					if ($length < $crossPointInfo['length']) {
						$crossPointInfo = [
							'point' => $p,
							'length' => $length,
							'i' => $i,
						];
					}
				}
			}

			$len = (($crossPointInfo['point']['x'] - $v1[0]['x']) ** 2) + (($crossPointInfo['point']['y'] - $v1[0]['y']) ** 2);


			$ll = 0;
			if (($v1[1]['x'] - $v1[0]['x']) != 0) {
				$xx = ($crossPointInfo['point']['x'] - $v1[0]['x']);
				$ll = $xx / ($v1[1]['x'] - $v1[0]['x']);
			} else if (($crossPointInfo['point']['y'] - $v1[0]['y']) != 0) {
				$yy = ($crossPointInfo['point']['y'] - $v1[0]['y']);
				$ll = $yy / ($v1[1]['y'] - $v1[0]['y']);
			}
// dump("ll={$ll}");

			if ($ll < 0) {
				return null;
			}
// echo '重なってる！<br />';

			return [
				'x' => $crossPointInfo['point']['x'],
				'y' => $crossPointInfo['point']['y'],
				'length' => $ll,
			];
		}

		$ab = $a + $b;

// dump("a = {$a}");
// dump("b = {$b}");
// dump("ab = {$ab}");

		if ($ab) {
			$length2  = ($a / $ab);
			if (($length2 < 0) || ($length2 >= 1.0)) {
				return null;
			}
		} else {
			return null;
		}


		$a = self::crossProduct(
			[$v2[0], $v1[0]],
			$v2
		) /* / 2 */;

		$b = self::crossProduct(
			$v2,
			[$v2[0], $v1[1]]
		) /* / 2 */;

		$ab = ($a + $b);

// dump("a = {$a}");
// dump("b = {$b}");
// dump("ab = {$ab}");

		if (!$ab) {
			return null;
		}

		$crossVectorLengthBase = ($a / $ab);
// echo('<br />v-len='.$crossVectorLengthBase.'<br />');
		if (($crossVectorLengthBase < 0)) {
			return null;
		}

		$crossed = [
			'x' => $v1[0]['x'] + (($v1[1]['x'] - $v1[0]['x']) * $crossVectorLengthBase),
			'y' => $v1[0]['y'] + (($v1[1]['y'] - $v1[0]['y']) * $crossVectorLengthBase),
			'length' => $crossVectorLengthBase,
		];

		return $crossed;
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

	public static function addShapeToOutline($baseOutline, $shape)
	{
// echo '<hr /><h2>addShapeToOutline</h2>';
		$outline = [
			$shape,
		];

// echo '引数：baseOutline<br />';
// echo self::testOutlineToSvg($baseOutline);
// echo '<br />';
// echo '引数：add-shape<br />';
// echo self::testOutlineToSvg($outline);
// echo '<br />';

		while(!empty($baseOutline)) {
// echo '<br />結果（仮）--------------------------------<br />';
// echo self::testOutlineToSvg($baseOutline);

			$contour = array_shift($baseOutline);

			$isComposed = false;
			$outlineCount = count($outline);

// echo '<ul>';
			for ($i = 0; $i < $outlineCount; $i++) {
// echo "index;{$i}<br />";
				$composed = self::composeShapes($contour, $outline[$i]);
				if (!is_null($composed)) {
					$isComposed = true;

echo '<li>composed.. <br />'.self::testOutlineToSvg($composed).'</li>';



					///////////////////// TESTE
					// $outline = array_merge($outline, $composed);
					// $baseOutline[] = $composed[0];
					///////////////////////

					//org
					$baseOutline = array_merge($baseOutline, $composed);

					unset($outline[$i]);
					$outline = array_values($outline);

					break;
				}
			}
// echo '</ul>';

			if (!$isComposed) {
				$outline[] = $contour;
			}
// echo self::testOutlineToSvg($outline);
// echo '<br />';

		}

// echo '<hr /><h4>結果</h4>';
// echo self::testOutlineToSvg($outline);
// echo '<hr />';
		return $outline;
	}

	public static function composeShapes($base, $addition)
	{

// echo '<h2>composeShapes</h2>';
// echo '引数： $base<br />';
// echo self::testOutlineToSvg([$base]).'<br />';
// echo '引数： $addition<br />';
// echo self::testOutlineToSvg([$addition]).'<br />';
// echo '<hr />';


		$composedList = [];
		$baseCount = count($base);
		$isPointPassedList = array_fill(0, $baseCount, false);

		$additionCount = count($addition);


		// TODO: 結果が複数シェイプ！..に対応
		$isComposed = false;
		$index = 0;
		$crossInfoList = [];
		$infoAddtion = [];
		while (false !== $index) {

			$composed = [];
			$firstIndex = $index;
			for ($i = 0; $i < $baseCount; $i++) {
				if (self::isInsideShapePoint($addition, $base[$index])) { // TODO: 内外判定
					$isPointPassedList[$index] = true;
					break;
				}


				$isPointPassedList[$index] = true;
				if (self::addCoordinateToContour($composed, $index, $base, $addition, $infoAddtion)) {
					$isComposed = true;
				}

				$index = ($index + 1) % $baseCount;
				if ($index == $firstIndex) {
					break;
				}
			}
			if ($isComposed) {
				if (!empty($composed)) {
					$composedList[] = $composed;
				}
			}
			$index = array_search(false, $isPointPassedList);
		}

		if (empty($composedList)) {
// echo "unconmposed............<hr />";
			return null;
		}

// echo self::testOutlineToSvg($composedList).'<br />';


return $composedList;







		$isAddtionPassedList = [];
		$additionCrossedToList = [];
		$crossedAddtion = [];
echo '<hr />';
		foreach ($addition as $i => $a) {
			$crossedAddtion[] = $a;
			$isAddtionPassedList[] = true;
			$additionCrossedToList[] = null;
			if (array_key_exists($i, $infoAddtion)) {
				foreach ($infoAddtion[$i] as $info) {
echo "addition after{$i}: {$info['point']['x']}, {$info['point']['y']}<br />";
					$crossedAddtion[] = $info['point'];
					$isAddtionPassedList[] = false;
					$additionCrossedToList[] = null;  // TODO: index!
				}
			}
		}

echo 'cross pointed<br />';
echo self::testOutlineToSvg([$crossedAddtion]).'<hr />';


		$dummy = [];
		$isComposed = false;
		$index = array_search(false, $isAddtionPassedList);
		$crossedAddtionCount = count($crossedAddtion);
		while (false !== $index) {
			$firstIndex = $index;
			$composed = [];
echo "<hr />開始<br />";
			for ($i = 0; $i < $crossedAddtionCount; $i++) {
				$isAddtionPassedList[$index] = true;

				// if (self::addCoordinateToContour($composed, $index, $crossedAddtion, $base, $dummy)) {
				// 	$isComposed = true;
				// } else {
					$composed[] = $crossedAddtion[$index];
				// }

echo "....index = {$index}  c:{$isComposed}<br />";
				$index = ($index + 1) % $crossedAddtionCount;
				if ($index == $firstIndex) {
					break;
				}
			}
echo "<br />";

			if (!empty($composed)) {
				$composedList[] = $composed;
			}
			$index = array_search(false, $isAddtionPassedList);
		}

echo '<hr />$composedList<br />';
echo self::testOutlineToSvg($composedList).'<br />';
foreach ($composedList as $cs) {
	echo self::testOutlineToSvg([$cs]).'<br />';
}
echo '<hr />';

		dump(compact('crossedAddtion'));
		dump(compact('isAddtionPassedList'));

// dump($isPointPassedListAddtion);
//
// $composedList = self::removeLostedShape($composedList);
echo '****** composed:<br />'.self::testOutlineToSvg($composedList).'<br />';

		return $composedList;

	}



	public static function composeShapes2($base, $addition)
	{

echo '<h2>composeShapes2</h2>';
echo '引数： $base<br />';
echo self::testOutlineToSvg([$base]).'<br />';
echo '引数： $addition<br />';
echo self::testOutlineToSvg([$addition]).'<br />';
echo '<hr />';


//////////////////
// $a = self::comcom($base, $addition);
// echo self::testOutlineToSvg($a);
/////////////////


		$composedList = [];
		$baseCount = count($base);
		$isPointPassedList = array_fill(0, $baseCount, false);

		$additionCount = count($addition);


		// TODO: 結果が複数シェイプ！..に対応
		$isComposed = false;
		$index = 0;
		$crossInfoList = [];
		$infoAddtion = [];
		while (false !== $index) {
			$composed = [];
			$firstIndex = $index;
			for ($i = 0; $i < $baseCount; $i++) {
				$isPointPassedList[$index] = true;
				if (self::addCoordinateToContour($composed, $index, $base, $addition, $infoAddtion)) {
					$isComposed = true;
				}

				$index = ($index + 1) % $baseCount;
				if ($index == $firstIndex) {
					break;
				}
			}
			if ($isComposed) {
				$composedList[] = $composed;
			}
			$index = array_search(false, $isPointPassedList);

			break;
		}

		if (empty($composedList)) {
echo "unconmposed............<hr />";
			return null;
		}

return $composedList;

	}

//	protected static function comcom($base, $addition)
//	{
//echo '<h2>comcom</h2>';
//echo self::testOutlineToSvg([$base]);
//echo self::testOutlineToSvg([$addition]);
//echo '<hr />';
//		$corssedShapes = self::insertCrossPointToShape($base, $addition);
//// dump($corssedShapes);
//
//		$base = $corssedShapes['base'];
//		$baseCount = count($base);
//		$baseCrossInfo = $corssedShapes['baseCrossInfoList'];
//
//		$addition = $corssedShapes['addition'];
//		$additionCount = count($addition);
//		$additionCrossInfo = $corssedShapes['additionCrossInfoList'];
//
//dump(compact('baseCrossInfo', 'additionCrossInfo'));
//
//		$isComposed = false;
//		$composed = [];
//		while (true) {
//			$firstIndex = 0;
//// dump($baseCrossInfo);
//			for ($i = 0; $i < $baseCount; $i++) {
//				$crossInfo = &$baseCrossInfo[$i];
//				if ($crossInfo['count'] < 1) {
//					$firstIndex++;
//				} else {
//					break;
//				}
//			}
//// dump(compact('firstIndex', 'baseCount'));
//			if ($firstIndex >= ($baseCount - 1)) {
//				break;
//			}
//
//
//// echo "<hr />start from: base-{$firstIndex}<ul>";
//
//			$isLastPoint = false;
//			$shape = [];
//			$index = $firstIndex;
//			for ($i = 0; $i < $baseCount; $i++) {
//// echo "<li>base-index: {$index}</li>";
//				$basePoint = $base[$index];
//				$basePointInfo = &$baseCrossInfo[$index];
//
//				$shape[] = $basePoint;
//				$basePointInfo['count']--;
//				if ($i != 0) {
//					if (!is_null($basePointInfo['toIndex'])) {
//						$idCrossStartBase = $basePointInfo['idCross'];
//
//						$aIndex = ($basePointInfo['toIndex'] + 1) % $additionCount;
//						$additionCount = count($addition);
//// echo '<ul>';
//						for ($j = 0; $j < $additionCount; $j++) {
//// echo "<li>addition index: {$aIndex}</li>";
//							$additionPoint = $addition[$aIndex];
//							$additionPointInfo = &$additionCrossInfo[$aIndex];
//
//							$shape[] = $additionPoint;
//							$additionPointInfo['count']--;
//							if (!is_null($additionPointInfo['toIndex'])) {
//								$index = $additionPointInfo['toIndex'];
//dump("idCross:{$additionPointInfo['idCross']} = idCrossStartBase:{$idCrossStartBase} ?");
//if ($additionPointInfo['idCross'] == $idCrossStartBase) {
//	echo '<h1>同じところに帰ってる</h1>';
//}
//								if ($index == $firstIndex) {
//									$isLastPoint = true;
//								}
//
//
//// echo "<li>addition index:{$aIndex} -> base index:{$index}</li>";
//								$basePointInfo = &$baseCrossInfo[$index];
//								$basePointInfo['count']--;
//// echo '</ul>';
//								break;
//							}
//							$aIndex = ($aIndex + 1) % $additionCount;
//						}
//
//						unset($additionPointInfo);
//						$isComposed = true;
//					}
//				}
//				// echo '</ul>';
//
//				unset($basePointInfo);
//
//				$index = ($index + 1) % $baseCount;
//				if ($index == $firstIndex) {
//					$isLastPoint = true;
//				}
//
//				if ($isLastPoint) {
//					break;
//				}
//			}
//
//			$composed[] = $shape;
//// echo '</ul>';
//
//		}
//
//		if (!$isComposed) {
//echo 'comcom not composed......<br />';
//			return null;
//		}
//
//echo 'comcom result<br />';
//echo self::testOutlineToSvg($composed);
//		return $composed;
//	}

	protected static function insertCrossPointToShape($base, $addition)
	{
// echo '<h2>insertCrossPointToShape</h2>';
// echo self::testOutlineToSvg([$base]);
// echo self::testOutlineToSvg([$addition]);
// echo '<hr />';

		$baseCount = count($base);
		$additionCount = count($addition);

		$crossPointList = [];

		$basePointList = [];
		foreach ($base as $point) {
			$basePointList[] = [
				'point' => $point,
				'crossedList' => [],
			];
		}

		$additionPointList = [];
		foreach ($addition as $point) {
			$additionPointList[] = [
				'point' => $point,
				'crossedList' => [],
			];
		}

		for ($index = 0; $index < $baseCount; $index++) {
			$vBase = [
				$basePointList[$index]['point'],
				$basePointList[($index + 1) % $baseCount]['point'],
			];

			$baseCrossPointList = &$basePointList[$index]['crossedList'];
			for ($indexAddition = 0; $indexAddition < $additionCount; $indexAddition++) {
				$vAddition = [
					$additionPointList[$indexAddition]['point'],
					$additionPointList[($indexAddition + 1) % $additionCount]['point'],
				];

				$additionCrossPointList = &$additionPointList[$indexAddition]['crossedList'];

				$cp = self::getCrossPoint($vBase, $vAddition);

				if (!is_null($cp)) {
					$crossInfo = [
						'x' => $cp['x'],
						'y' => $cp['y'],
						'base' => [
							'index' => $index,
							'length' => $cp['length'],
						],
						'addition' => [
							'index' => $indexAddition,
							'length' => $cp['length2'],
						],
					];

					$indexCrossPoint = count($crossPointList);
					$crossPointList[] = $crossInfo;

					$listCount = count($baseCrossPointList);
					$isInsterted = false;
					if ($listCount > 0) {
						for ($i = 0; $i < $listCount; $i++) {
							if ($crossPointList[$baseCrossPointList[$i]]['base']['length'] > $crossInfo['base']['length']) {
								array_splice($baseCrossPointList, $i, 0, [$indexCrossPoint]);
								$isInsterted = true;
								break;
							}
						}
					}
					if (!$isInsterted) {
						$baseCrossPointList[] = $indexCrossPoint;
					}

					$listCount = count($additionCrossPointList);
					$isInsterted = false;
					if ($listCount > 0) {
						for ($i = 0; $i < $listCount; $i++) {
							if ($crossPointList[$additionCrossPointList[$i]]['addition']['length'] > $crossInfo['addition']['length']) {
								array_splice($additionCrossPointList, $i, 0, [$indexCrossPoint]);
								$isInsterted = true;
								break;
							}
						}
					}
					if (!$isInsterted) {
						$additionCrossPointList[] = $indexCrossPoint;
					}
				}
				unset($additionCrossPointList);
			}
			unset($baseCrossPointList);
		}

// dump(compact('crossPointList'));




		$newBase = [];
		$newBaseCrossInfoList = [];
		foreach ($basePointList as $i => $b) {
			$newBase[] = $b['point'];
			$newBaseCrossInfoList[] = [
				'count' => 1,
				'toIndex' => null,
				'idCross' => null,
			];
			foreach ($b['crossedList'] as $i) {
				$c = $crossPointList[$i];

				$newBase[] = [
					'x' => $c['x'],
					'y' => $c['y'],
					'isOnCurvePoint' => true,
				];


				$iii = $c['addition']['index'];
				$toIndex = 0;
				// $idCross = null;
				foreach ($additionPointList as $ai => $a) {
					if ($ai == $iii) {
						$additionPoint = $additionPointList[$iii];
						foreach ($additionPoint['crossedList'] as $listIndex) {
							$toIndex++;
							if ($i == $listIndex) {
								break;
							}
						}
						break;
					}

					$toIndex++;
					$toIndex += count($a['crossedList']);
				}

				$newBaseCrossInfoList[] = [
					'count' => 2,
					'toIndex' => $toIndex,
					'idCross' => $i,
				];

			}
		}


		$newAdditionCrossInfoList = [];
		$newAddition = [];
		foreach ($additionPointList as $i => $a) {
			$newAdditionCrossInfoList[] = [
				'count' => 1,
				'toIndex' => null,
				'idCross' => null,
			];
			$newAddition[] = $a['point'];
			foreach ($a['crossedList'] as $i) {
				$c = $crossPointList[$i];

				$newAddition[] = [
					'x' => $c['x'],
					'y' => $c['y'],
					'isOnCurvePoint' => true,
				];

				$iii = $c['base']['index'];
				$toIndex = 0;
				foreach ($basePointList as $bi => $b) {
					if ($bi == $iii) {
						$basePoint = $basePointList[$iii];
						foreach ($basePoint['crossedList'] as $listIndex) {
							$toIndex++;
							if ($i == $listIndex) {
								break;
							}
						}
						break;
					}

					$toIndex++;
					$toIndex += count($b['crossedList']);
				}

				$newAdditionCrossInfoList[] = [
					'count' => 2,
					'toIndex' => $toIndex,
					'idCross' => $i,
				];
			}
		}

		$newBaseCount = count($newBase);
		$newAdditionCount = count($newAddition);


		////////////////////////////////////////////// TEST
		if (false) {

			echo '<hr /><h2>base</h2>';
			echo '<ul>';
			for ($i = 0; $i < $newBaseCount; $i++) {
				// echo "{$newBase[$i]['x']}, {$newBase[$i]['x']} -> ";

				$basePoint = $newBase[$i];
				$basePointInfo = &$newBaseCrossInfoList[$i];

				if (!is_null($basePointInfo['toIndex'])) {
					$aIndex = $basePointInfo['toIndex'];
					$aPoint = $newAddition[$aIndex];
					echo '<li>';
					echo "base:{$i}({$newBase[$i]['x']}, {$newBase[$i]['y']}) -> addition: $aIndex({$aPoint['x']}, {$aPoint['y']})";
					echo "";
					echo '</li>';
				}
			}
			echo '</ul>';

			echo '<hr /><h2>addition</h2>';
			echo '<ul>';
			for ($i = 0; $i < $newAdditionCount; $i++) {
				// echo "{$newBase[$i]['x']}, {$newBase[$i]['x']} -> ";

				$additionPoint = $newAddition[$i];
				$additionPointInfo = &$newAdditionCrossInfoList[$i];

				if (!is_null($additionPointInfo['toIndex'])) {
					$bIndex = $additionPointInfo['toIndex'];
					$bPoint = $newBase[$bIndex];
					echo '<li>';
					echo "addition:{$i}({$newAddition[$i]['x']}, {$newAddition[$i]['y']}) -> base:{$bIndex}({$bPoint['x']}, {$bPoint['y']})";
					echo '</li>';
				}
			}
			echo '</ul>';
		}
		////////////////////////////////////////////////////////

		return [
			'base' => $newBase,
			'addition' => $newAddition,
			'baseCrossInfoList' => $newBaseCrossInfoList,
			'additionCrossInfoList' => $newAdditionCrossInfoList,
		];
	}


	public static function compose($base, $addition)
	{
// echo '<hr /><h2>compose</h2>';
// echo '$base<br />'.self::testOutlineToSvg([$base]).'<br />';
// echo '$addition<br />'.self::testOutlineToSvg([$addition]).'<br />';

		$composed = [];
		$isComposed = false;
		$count = count($base);
		for ($i = 0; $i < $count; $i++) {
			$isPointPassedList[$i] = true;
			if (self::addCoordinate($composed, $i, $base, $addition)) {
				$isComposed = true;
			}
		}

		if (!$isComposed) {
// echo 'unconmposed...<br />';
			return null;
		}
// echo self::testOutlineToSvg([$composed]).'<br />';
		return $composed;
	}

	protected static function addCoordinateToContour(&$coordinateList, &$index, $base, $addition, &$additionInfo)
	{

// echo '<h3>addCoordinateToContour</h3>';
// dump($base);
// echo self::testOutlineToSvg($base).'<rh />';

		$coordinateList[] = $base[$index];
		$baseVector = [
			$base[$index],
			$base[($index + 1) % count($base)],
		];

		$crossInfo = self::getCrossPointToShapeByVector($addition, $baseVector, null);


// dump($crossInfo);
		if (is_null($crossInfo)) {
			return false;
		}

		$p = [
			'x' => $crossInfo['point']['x'],
			'y' => $crossInfo['point']['y'],
			'isOnCurvePoint' => true,
		];

		$coordinateList[] = $p;

		$startPointIndex = $crossInfo['index'];
		$startPointInfo = [
			'point' => $p,
			'index' => $index,
			'count' => null,
		];
		$additionInfo[$startPointIndex][] = $startPointInfo;


		$count = count($addition);
		$additionIndex = ($crossInfo['index'] + 1) % $count;
		$prevPoint = $p;
		for ($i = 0; $i < $count; $i++) {
			$p = $addition[$additionIndex];

			$ignoreIndex = null;
			if ($i == 0) {
				$ignoreIndex = $index;
			}

			$crossPoint = self::getCrossPointToShapeByVector($base, [$prevPoint, $p], $ignoreIndex);
			if (is_null($crossPoint)) {
				$coordinateList[] = $p;
			} else {
				$p2 = [
					'x' => $crossPoint['point']['x'],
					'y' => $crossPoint['point']['y'],
					'isOnCurvePoint' => true,
					'isOnCurvePoint' => $p['isOnCurvePoint'],
				];
				$coordinateList[] = $p2;
				$index = $crossPoint['index'];

				//
				$endPointIndex = $additionIndex - 1;
				if ($endPointIndex < 0) {
					$endPointIndex += $count;
				}

				// $startPointInfo['count'] = 100;
				$additionInfo[$endPointIndex][] = [
					'point' => $p2,
					'index' => $index,
					// 'count' => -1,
				];

				break;
			}

			$prevPoint = $p;
			$additionIndex  = ($additionIndex + 1) % $count;
		}

		// dump(compact('additionInfo'));
		return true;
	}

	protected static function addCoordinate(&$coordinateList, &$index, $base, $addition)
	{
		$coordinateList[] = $base[($index)];

		$baseCount = count($base);
		$s = $base[$index];
		$n = $base[($index + 1) % $baseCount];

		// if ($n['isOnCurvePoint']) {
			$baseVector = [
				$s,
				$n,
			];

			$crossInfo = self::getCrossPointToShapeByVector($addition, $baseVector, null);

		// } else {
		// 	$e = $base[($index + 2) % $baseCount];
		// 	$baseBezier  = [
		// 		$s,
		// 		$n,
		// 		$e,
		// 	];
		//
		// 	$crossInfo = self::getCrossPointToShapeByBezier2($addition, $baseBezier, null);
		// }


		if (is_null($crossInfo)) {
			return false;
		}

		$p = $crossInfo['point'];
		$coordinateList[] = [
			'x' => $p['x'],
			'y' => $p['y'],
			'isOnCurvePoint' => true,
		];
		$additionOffset = $crossInfo['index'] + 1;

		$count = count($addition);
		$prevPoint = $crossInfo['point'];
		for ($i = 0; $i <= $count; $i++) {
			$s = $addition[($i + $additionOffset) % $count];
			$ignoreIndex = null;
			if ($i == 0) {
				$ignoreIndex = $index;
			}
			$crossPoint = self::getCrossPointToShapeByVector($base, [$prevPoint, $s], $ignoreIndex);
			if (is_null($crossPoint)) {
				$coordinateList[] = $s;
			} else {
				$p = $crossPoint['point'];
				$coordinateList[] = [
					'x' => $p['x'],
					'y' => $p['y'],
					'isOnCurvePoint' => true,
				];
				$index = $crossPoint['index'];
				break;
			}

			$prevPoint = $s;
		}
		return true;
	}

	protected static function getNearestCrossPoint($pointList, $additionPoint = null)
	{
		if (!is_array($pointList)) {
			return null;
		}

		$point = null;
		foreach ($pointList as $i => $p) {
			if ($i <= 0) {
				$point = $p;
				continue;
			}
			if ($p['length'] < $point['length']) {
				$point = $p;
			}
		}
		return $point;
	}

	protected static function getCrossPointToShapeByBezier2($shape, $bezier, $ignoreIndex = null)
	{
// echo '<h2>getCrossPointToShapeByBezier2</h2>';
		$count = count($shape);
		$crossInfo = null;

		for ($i = 0; $i < $count; $i++) {
			if ($i !== $ignoreIndex) {

				$s = $shape[$i];
				$e = $shape[($i + 1) % $count];

				$cpList = self::getBezier2CrossPoint($bezier, [$e, $e]);
				if (!is_null($cpList)) {
					if (is_null($crossInfo)) {
						$crossInfo = self::getNearestCrossPoint($cpList);
					} else {
						$cp = self::getNearestCrossPoint($cpList);
						if ($cp['bezierLength'] < $crossInfo['bezierLength']) {
							$crossInfo = $cp;
						}
					}
				}
			}
		}
		return $crossInfo;
	}

	protected static function getCrossPointToShapeByVector($shape, $vector, $ignoreIndex = null)
	{
		$count = count($shape);
// echo "<h4>getCrossPointToShapeByVector (ignoreIndex : c:{$count} {$ignoreIndex})</h4>";

		$crossInfo = null;
		for ($i = 0; $i < $count; $i++) {
// echo '辺：'.$i.' -> '.(($i + 1) % $count)." <span>({$s['x']},{$s['y']})->({$e['x']},{$e['y']})</span><br />";
			if ($i !== $ignoreIndex) {
				$s = $shape[$i];
				$n = $shape[($i + 1) % $count];

				// if ($n['isOnCurvePoint']) {
					$cp = self::getCrossPoint($vector, [$s, $n]);
// 				} else {
// 					$e = $shape[($i + 2) % $count];
// 					$bezir = [
// 						$s,
// 						$n,
// 						$e,
// 					];
// 					$cpList = self::getBezier2CrossPoint($bezir, $vector);
// 					$cp = [
// 						'x' => $cpList[0]['point']['x'],
// 						'y' => $cpList[0]['point']['y'],
// 						'length' => $cpList[0]['length'],
// 					];
// // dd($cp);
// 				}

				if (!is_null($cp)) {
// echo "...HIT Index={$i} : len={$cp['length']}<br />";
// dump($vector);
					if (is_null($crossInfo)) {
						$crossInfo = [
							'point' => $cp,
							'index' => $i,
						];
// dump($crossInfo);
					} else {
						if ($cp['length'] < $crossInfo['point']['length']) {
							$crossInfo = [
								'point' => $cp,
								'index' => $i,
							];
// dump($crossInfo);
						}
					}
				}
			}
		}
// dump($crossInfo);
// echo '<hr />';
		return $crossInfo;
	}



}
