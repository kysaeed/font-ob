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

		// dd('hey');
dd($outline);

		///////////////
		// $a = self::getCrossPointXxxx(
		// 	[['x' => 0, 'y' => 100,], ['x' => 100, 'y' => 100,]],
		// 	[['x' => 200, 'y' => 0,], ['x' => 200, 'y' => 100,]]
		// );
		// dd($a);
		///////////////


		$base = [
			[
				'x' => 54.0,
				'y' => 10.0,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 54.0,
				'y' => 120.0,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 46.0,
				'y' => 120.0,
				'isOnCurvePoint' => true,
			],
			[
				'x' => 46.0,
				'y' => 10.0,
				'isOnCurvePoint' => true,
			],
		];
		echo self::testOutlineToSvg([$base]).'<br />';

		$v = [
			['x' => 104.0, 'y' => 104.0],
			['x' => 26.0, 'y' => 104.0],
		];

		// echo '<h1>getCrossPoint</h1>';
		// $s = $base[0];
		// $e = $base[1];
		// $c = self::getCrossPoint($v, [$s, $e]);
		// dump($c);


		$crossPoint = self::getCrossPointToShape($base, $v, null);



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
		$k = self::getBezierOnCurvePoint($s, $e, $a, $b, 0.5);


		// $k = [
		// 	'x' => ($s['x'] * 0.125) + ($a['x'] * (0.25 * 3) * 0.5) + ($b['x'] * (0.125 * 3)) + ($e['x'] * 0.125),
		// 	'y' => ($s['y'] * 0.125) + ($a['y'] * (0.25 * 3) * 0.5) + ($b['y'] * (0.125 * 3)) + ($e['y'] * 0.125),
		// ];

		$sk = self::getMidPoint($s, $k);
		$ka = self::getMidPoint($sk, $a);
		$ek = self::getMidPoint($e, $k);
		$kb = self::getMidPoint($ek, $b);

		$aByK = self::getBezierOnCurvePoint($s, $e, $a, $b, 0.25);
// dump(compact('aByK'));

		$bByK = self::getBezierOnCurvePoint($s, $e, $a, $b, 0.75);

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

		echo '<hr />アウトライン @o test<br />';
		$s = '<svg>';
		$s .= ' <path d="M50,1 50,90 10,90 10,50 90,50" fill="none" stroke="black"/>';
		$s .= '</svg>';

echo 'SVG<br />'.$s.'<br />';

		$stroke = self::parseStrokeSvg($s);
		$outline = self::getOutlineFromStroke($stroke);
		echo self::testOutlineToSvg($outline);



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
// dd($g->coordinates);
		$s = new GlyphSvg($g, $hm);
echo $s->getSvg();
echo '<hr />';



		echo '<hr />ストローク @a <br />';
		$shi = '<svg>';
		$shi .= ' <path d="M86,57 c-10.481,21.489 -24.904,32 -39,32 c-13.079,0 -21,-8.539 -21,-28 c0,-16.818 2,-32.069 2,-50" fill="none" stroke="black"/>';
		$shi .= '</svg>';

		$stroke = self::parseStrokeSvg($shi);
		echo self::testStrokeToSvg($stroke);



	// dd($outline);


		dump($stroke);

		echo '<hr />アウトライン @a <br />';
		$s = '<svg>';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="14" x2="86" y1="21" y2="21" />';
		$s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="38" x2="38" y1="6" y2="81" />';
		$s .= '<path d="M68,40 c-8.337,27.624 -28.133,45 -43,45 c-7.852,0 -13,-4.894 -13,-15 c0,-17.828 17.78,-32 43,-32 C76.039,38 88,48.227 88,63 C88,76.87 77.755,86.868 60,90" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';

// echo $s.'<br />';

		$stroke = self::parseStrokeSvg($s);
		$outline = self::getOutlineFromStroke($stroke);

		echo self::testOutlineToSvg($outline);

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
echo $s->getSvg();


		echo '<hr />glyph ke test<br />';
		$s = '<svg>';
		$s .= '<path d="M67.6,25.2 c0,10.696 0.801,20.25 0.801,30.399 c0,22.076 -4.486,28.258 -20,33.601" fill="none" stroke="#000000" stroke-width="1" />';
		$s .= '<path d="M82.801,42 c-16.955,1.841 -27.952,2.4 -44,2.4" fill="none" stroke="#000000" stroke-width="1" />';
		$s .= '<path d="M26.801,88.4 c-2.852,-10.643 -4,-19.333 -4,-30.4 c0,-11.066 1.148,-19.758 4,-30.4" fill="none" stroke="#000000" stroke-width="1" />';
		$s .= '</svg>';
		echo $s;

		$stroke = self::parseStrokeSvg($s);
		$outline = self::getOutlineFromStroke($stroke);

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
					'y' => 500 - $point['y'] * 10,
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
// dd($g->coordinates);
		$s = new GlyphSvg($g, $hm);
echo $s->getSvg();


// dd($s->getSvg());

dd('OK');

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

	protected static function testOutlineToSvg($outline, $isPointEnabled = false)
	{
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
			$s .= 'z ';
		}

		$s .= '" fill="'.'#dddddd'.'" stroke="#000000" stroke-width="1" />';

		if (!$isPointEnabled) {
			foreach ($outline as $lineNumber => $line) {
				foreach ($line as $index => $l) {
					$color = 'black';
					if ($index == 0) {
						$color = 'red';
					}
					$sc .= "<circle cx='{$l['x']}' cy='{$l['y']}' r='2' fill='" .$color. "'/>";
				}
			}
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

	protected static function svgCtoQ($s, $cParams)
	{
		$a = $cParams[0];
		$b = $cParams[1];
		$e = $cParams[2];

		// $t = 0.5;

		$k = self::getBezierOnCurvePoint($s, $e, $a, $b, 0.51);

		$aByK = self::getBezierOnCurvePoint($s, $e, $a, $b, 0.25);
		$bByK = self::getBezierOnCurvePoint($s, $e, $a, $b, 0.75);

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

	protected static function getBezierOnCurvePoint($s, $e, $a, $b, $t)
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
			preg_match_all('/([MmCc]\s?)?(-?[\d.]+),(-?[\d.]+)/', $d[1], $pathMatches);

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
				$x = (float)$pathMatches[2][$index];
				$y = (float)$pathMatches[3][$index];
				if ($isRelativePosition) {
					$x += $prevX;
					$y += $prevY;
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
						$cList = self::svgCtoQ(['x'=>$prevX, 'y'=>$prevY], $spline);
						foreach ($cList as $c) {
							$pathParams[] = $c[1];
							$pathParams[] = $c[2];
						}
						// foreach ($spline as $p) {
						// 	$pathParams[] = $p;
						// }

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

	protected static function getOutlineFromStroke($stroke)
	{
echo '<h1>getOutlineFromStroke</h1>';
		$outline = [];

		$shapeList = self::strokeToShapeList($stroke);

echo '<hr />合成前<br />';
dump($shapeList);
echo self::testOutlineToSvg($shapeList);
echo '<hr />';


		$slicedShapeOutlineList = [];
		foreach ($shapeList as $shape) {
			$outline = self::sliceShape($shape);
dump(compact('outline'));
echo self::testOutlineToSvg($outline);
			$slicedShapeOutlineList[] = $outline;
		}

echo '<hr />';

		$composedOutline = [];
// dd($slicedShapeOutlineList);
		// foreach ($slicedShapeOutlineList as $slicedOuline) {
		while (!empty($slicedShapeOutlineList)) {
			$slicedOuline = array_shift($slicedShapeOutlineList);

echo '<h1>remain count:'.count($slicedShapeOutlineList).'</h1>';
			foreach ($slicedOuline as $shape) {

echo self::testOutlineToSvg([$shape]);
echo '<br />';
				$isComposed = false;

				$_next = [];
				if (true) {
// echo '元の状態 ーーーーーーーーーーーーーーーーーー<br />';
					$_next = [];
					while (!empty($composedOutline)) {
						$os = array_shift($composedOutline);

						// TODO: 交点情報を保存しておく！


						// $composed = null;
						$composed = self::comcom($shape, $os);
						if (!is_null($composed)) {
// echo '<hr />composedが出来た<br />';
// echo self::testOutlineToSvg($composed).'<br />';
							$isComposed = true;
							// foreach ($composed as $c) {
							// 	$_next[] = $c;
							// }

							$slicedShapeOutlineList[] = $composed;
						} else {
							$_next[] = $os;
						}

// echo self::testOutlineToSvg([$os]).'<br />';
					}
				} else {

					// foreach ($composedOutline as $os) {
					while (!empty($composedOutline)) {
						$os = array_shift($composedOutline);

						$composed = self::comcom($shape, $os);
						if (!is_null($composed)) {
							$isComposed = true;
							$slicedShapeOutlineList[] = $composed;
						}

					}

				}
				if (!$isComposed) {
					$_next[] = $shape;
				}

				$composedOutline = $_next;

echo self::testOutlineToSvg($composedOutline);


			}
		}

$col = self::removeLostedShape($composedOutline);

echo '<hr />composedOutline=<br />';
echo self::testOutlineToSvg($col);
die;

		return $composedOutline;







		// $outline = $slicedShapeOutlineList[0];

		$ouline = [];
		foreach ($slicedShapeOutlineList as $i => $addtionOutline) {
			foreach ($addtionOutline as $s) {


				$isComposed = false;
				foreach ($outline as $po) {
					$composed = self::composeShapes($po, $s);
					if (!is_null($composed)) {
						$isComposed = true;
						foreach ($composed as $c) {
							$outline[] = $c;
						}
						break;
					}
				}
				if (!$isComposed) {
					$outline[] = $s;
				}

			// if (0 != $i) {
			// 	foreach ($outline as $shape) {
			// 		// $composed = self::composeShapes($shape, $addtionShape);

// echo '<hr /><hr />もとの要素:<br />';
// echo self::testOutlineToSvg([$shape]).'<br />';
// echo '<hr />';
//
// echo '加算:<br />';
// echo self::testOutlineToSvg($addtionOutline).'<br />';

// 					$c = [];
// 					$isComposed = false;
// 					foreach ($addtionOutline as $ai => $addtionShape) {
// 						if (!$isComposed) {
// 							$composed = self::composeShapes($shape, $addtionShape);
// // dump($composed);
// 							if (!is_null($composed)) {
// 								$c = array_merge($c, $composed);
//
// 								$idComposed = true;
// echo self::testOutlineToSvg($composed).'<br />';
// 							}
// 						}
// 						if (!$isComposed) {
// 							$c[] = $addtionShape;
// 						}
// 					}
				// 	$outline[] = $shape;
				// }
			}
		}
		// $slicedShapeOutlineList[0] = $outline;

echo '<hr />結果：<br />';
echo self::testOutlineToSvg($outline).'<hr />';
		return $outline;
	}

	protected static function sliceShape($shapePointList)
	{


// return [$shapePointList];
		$pointsCount = count($shapePointList);
		$isPointPassedList = array_fill(0, $pointsCount, false);

		$shapeList = [];
		$index = 0;
		while($index !== false) {
			$firstIndex = $index;
			$outline = [];
			for ($i = 0; $i < $pointsCount; $i++) {
				$p = $shapePointList[$index];
				$isPointPassedList[$index] = true;
				$outline[] = $p;
				$crossInfo = self::getSelfCrossInfo($shapePointList, $index);

				// アウトラインを沿わせる
				if (!is_null($crossInfo)) {
					$outline[] = [
						'x' => $crossInfo['point']['x'],
						'y' => $crossInfo['point']['y'],
						'isOnCurvePoint' => true,
					];
					$index = $crossInfo['index'];
				}

				$index = ($index + 1) % $pointsCount;
				if ($index == $firstIndex) {
					break;
				}
			}
			$shapeList[] = $outline;
			$index = array_search(false, $isPointPassedList);
		}

// dd($outlineList);
// dump($isPointPassedList);

		$outlineList = self::removeLostedShape($shapeList);
		return $outlineList;
	}

	protected static function getSelfCrossInfo($shapePointList, $index)
	{
		$pointCount = count($shapePointList);
		$v = [
			$shapePointList[$index],
			$shapePointList[($index + 1) % $pointCount],
		];

		$crossInfo = null;
		$index = ($index + 2) % $pointCount;
		for ($i = 0; $i < ($pointCount - 3); $i++) {
			$p = $shapePointList[$index];
			$next = $shapePointList[($index + 1) % $pointCount];

			$point = self::getCrossPoint($v, [$p, $next]);
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
			$index = ($index + 1) % $pointCount;
		}
		return $crossInfo;
	}

	protected static function createDirectionList($shapeList)
	{
		$directionList = [];
		foreach ($shapeList as $lineIndex => $line) {
			$sum = 0;
			$pointCount = count($line);
			for ($i = 0; $i < $pointCount; $i++) {
				$v1 = [
					$line[$i],
					$line[($i + 1) % $pointCount],
				];
				$v2 = [
					$line[($i + 1) % $pointCount],
					$line[($i + 2) % $pointCount],
				];
				$sum += self::crossProduct($v1, $v2);
			}

			if ($sum > 0) {
				$directionList[] = 1;
			} else if ($sum < 0){
				$directionList[] = -1;
			} else {
				$directionList[] = 0;
			}
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
		$line = $outline[$insideShapeIndex];
		$p = $line[0];
		$v = [
			$p,
			['x' => $p['x'] + 10, 'y' => $p['y']],
		];

		$crossInfo = null;
		foreach ($outline as $i => $line) {
			if ($i != $insideShapeIndex) {

				$crossCount = 0;
				$pointCount = count($line);
				$crossInfoLine = null;
				for ($index = 0; $index < $pointCount; $index++) {
					$p = $line[$index];
					$next = $line[($index + 1) % $pointCount];

					$crossPoint = self::getCrossPointXxxx($v, [$p, $next]);
					if (!is_null($crossPoint)) {
						if ($crossPoint['length'] > 0) {
							$crossCount++;
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

				if (($crossCount % 2) > 0) {
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

		if (is_null($crossInfo)) {
			return -1;
		}

		return $crossInfo['shapeIndex'];
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
		if (($crossVectorLengthBase < 0) || ($crossVectorLengthBase > 1)) {
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


	public static function getCrossPointXxxx($v1, $v2)
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

echo '<h2>composeShapes</h2>';
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

		$addtionCount = count($addition);


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
		}

		if (empty($composedList)) {
			return null;
		}


		$isAddtionPassedList = [];
		$addtionCrossedToList = [];
		$crossedAddtion = [];
echo '<hr />';
		foreach ($addition as $i => $a) {
			$crossedAddtion[] = $a;
			$isAddtionPassedList[] = true;
			$addtionCrossedToList[] = null;
			if (array_key_exists($i, $infoAddtion)) {
				foreach ($infoAddtion[$i] as $info) {
echo "addtion after{$i}: {$info['point']['x']}, {$info['point']['y']}<br />";
					$crossedAddtion[] = $info['point'];
					$isAddtionPassedList[] = false;
					$addtionCrossedToList[] = null;  // TODO: index!
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


	protected static function comcom($base, $addtion)
	{
echo '<h2>comcom</h2>';
echo self::testOutlineToSvg([$base]);
echo self::testOutlineToSvg([$addtion]);
echo '<hr />';

		$corssedShapes = self::insertCrossPointToShape($base, $addtion);

		$base = $corssedShapes['base'];
		$baseCount = count($base);
		$baseCrossInfo = $corssedShapes['baseCrossInfoList'];

		$addition = $corssedShapes['addition'];
		$additionCount = count($addition);
		$additionCrossInfo = $corssedShapes['additionCrossInfoList'];


		$isComposed = false;
		$composed = [];
		while (true) {
			$firstIndex = 0;
// dump($baseCrossInfo);
			for ($i = 0; $i < $baseCount; $i++) {
				$crossInfo = &$baseCrossInfo[$i];
				if ($crossInfo['count'] < 1) {
					$firstIndex++;
				} else {
					break;
				}
			}
// dump(compact('firstIndex', 'baseCount'));
			if ($firstIndex >= ($baseCount - 1)) {
				break;
			}


// echo "<hr />start from: base-{$firstIndex}<ul>";

			$isLastPoint = false;
			$shape = [];
			$index = $firstIndex;
			for ($i = 0; $i < $baseCount; $i++) {
// echo "<li>base-index: {$index}</li>";
				$basePoint = $base[$index];
				$basePointInfo = &$baseCrossInfo[$index];

				$shape[] = $basePoint;
				$basePointInfo['count']--;
				if ($i != 0) {
					if (!is_null($basePointInfo['toIndex'])) {
						$crossStartBaseIndex = $index;

						$aIndex = ($basePointInfo['toIndex'] + 1) % $additionCount;
						$additionCount = count($addition);
// echo '<ul>';
						for ($j = 0; $j < $additionCount; $j++) {
// echo "<li>addtion index: {$aIndex}</li>";
							$additionPoint = $addition[$aIndex];
							$additionPointInfo = &$additionCrossInfo[$aIndex];

							$shape[] = $additionPoint;
							$additionPointInfo['count']--;
							if (!is_null($additionPointInfo['toIndex'])) {
								$index = $additionPointInfo['toIndex'];
dump(compact('index', 'crossStartBaseIndex'));
if ($index == $crossStartBaseIndex) {
	echo '<h1>同じところに帰ってる</h1>';
}
								if ($index == $firstIndex) {
									$isLastPoint = true;
								}


// echo "<li>addtion index:{$aIndex} -> base index:{$index}</li>";
								$basePointInfo = &$baseCrossInfo[$index];
								$basePointInfo['count']--;
// echo '</ul>';
								break;
							}
							$aIndex = ($aIndex + 1) % $additionCount;
						}

						unset($additionPointInfo);
						$isComposed = true;
					}
				}
				// echo '</ul>';

				unset($basePointInfo);

				$index = ($index + 1) % $baseCount;
				if ($index == $firstIndex) {
					$isLastPoint = true;
				}

				if ($isLastPoint) {
					break;
				}
			}

			$composed[] = $shape;
// echo '</ul>';

		}

		if (!$isComposed) {
echo 'comcom not composed......<br />';
			return null;
		}

echo 'comcom result<br />';
echo self::testOutlineToSvg($composed);
		return $composed;
	}

	protected static function insertCrossPointToShape($base, $addtion)
	{
// echo '<h2>insertCrossPointToShape</h2>';
// echo self::testOutlineToSvg([$base]);
// echo self::testOutlineToSvg([$addtion]);
// echo '<hr />';

		$baseCount = count($base);
		$addtionCount = count($addtion);

		$crossPointList = [];

		$basePointList = [];
		foreach ($base as $point) {
			$basePointList[] = [
				'point' => $point,
				'crossedList' => [],
			];
		}

		$additionPointList = [];
		foreach ($addtion as $point) {
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
			for ($indexAddition = 0; $indexAddition < $addtionCount; $indexAddition++) {
				$vAddition = [
					$additionPointList[$indexAddition]['point'],
					$additionPointList[($indexAddition + 1) % $addtionCount]['point'],
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


	public function compose($base, $addition)
	{
		$composed = [];
		$count = count($base);
		for ($i = 0; $i < $count; $i++) {
			$isPointPassedList[$i] = true;
			$this->addCoordinate($composed, $i, $base, $addition);
		}

		return $composed;
	}

	protected static function addCoordinateToContour(&$coordinateList, &$index, $base, $addition, &$addtionInfo)
	{
		$coordinateList[] = $base[$index];
		$index++;







		return false;
	}

	protected static function addCoordinateToContour__(&$coordinateList, &$index, $base, $addition, &$addtionInfo)
	{

// echo '<h3>addCoordinateToContour</h3>';
// dump($base);
// echo self::testOutlineToSvg($base).'<rh />';

		$coordinateList[] = $base[$index];
		$baseVector = [
			$base[$index],
			$base[($index + 1) % count($base)],
		];

		$crossInfo = self::getCrossPointToShape($addition, $baseVector, null);
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
		$addtionInfo[$startPointIndex][] = $startPointInfo;


		$count = count($addition);
		$additionIndex = ($crossInfo['index'] + 1) % $count;
		$prevPoint = $p;
		for ($i = 0; $i < $count; $i++) {
			$p = $addition[$additionIndex];

			$ignoreIndex = null;
			if ($i == 0) {
				$ignoreIndex = $index;
			}

			$crossPoint = self::getCrossPointToShape($base, [$prevPoint, $p], $ignoreIndex);
			if (is_null($crossPoint)) {
				$coordinateList[] = $p;
			} else {
				$p2 = [
					'x' => $crossPoint['point']['x'],
					'y' => $crossPoint['point']['y'],
					'isOnCurvePoint' => true,
				];
				$coordinateList[] = $p2;
				$index = $crossPoint['index'];

				//
				$endPointIndex = $additionIndex - 1;
				if ($endPointIndex < 0) {
					$endPointIndex += $count;
				}

				// $startPointInfo['count'] = 100;
				$addtionInfo[$endPointIndex][] = [
					'point' => $p2,
					'index' => $index,
					// 'count' => -1,
				];

				break;
			}

			$prevPoint = $p;
			$additionIndex  = ($additionIndex + 1) % $count;
		}

		dump(compact('addtionInfo'));
		return true;
	}

	protected function addCoordinate(&$coordinateList, &$index, $base, $other)
	{
		$coordinateList[] = $base[($index)];

		$baseVector = [
			$base[$index],
			$base[($index + 1) % count($base)],
		];

		$crossInfo = self::getCrossPointToShape($other, $baseVector, null);
		if (is_null($crossInfo)) {
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
			$crossPoint = self::getCrossPointToShape($base, [$prevPoint, $s], $ignoreIndex);
			if (is_null($crossPoint)) {
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

	protected static function getCrossPointToShape($shape, $vector, $ignoreIndex = null)
	{
		$count = count($shape);
// echo "<h4>getCrossPointToShape (ignoreIndex : c:{$count} {$ignoreIndex})</h4>";

		$crossInfo = null;
		for ($i = 0; $i < $count; $i++) {
			$s = $shape[$i];
			$e = $shape[($i + 1) % $count];
// echo '辺：'.$i.' -> '.(($i + 1) % $count)." <span>({$s['x']},{$s['y']})->({$e['x']},{$e['y']})</span><br />";
			if ($i !== $ignoreIndex) {
				$cp = self::getCrossPoint($vector, [$s, $e]);
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
