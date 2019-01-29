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

		///////////////
		// $a = self::getCrossPointXxxx(
		// 	[['x' => 0, 'y' => 100,], ['x' => 100, 'y' => 100,]],
		// 	[['x' => 200, 'y' => 0,], ['x' => 200, 'y' => 100,]]
		// );
		// dd($a);
		///////////////


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
		$s = '<svg>';
		$sc = '';

// dd($stroke);
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
		echo $s;

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

		echo '<hr />ストローク shi<br />';
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

		echo '<hr />アウトライン o<br />';
		$s = '<svg>';
		$s .= ' <path d="M50,1 50,90 10,90 10,50 90,50" fill="none" stroke="black"/>';
		$s .= '</svg>';

echo 'SVG<br />'.$s.'<br />';

		$stroke = self::parseStrokeSvg($s);
		$outline = self::getOutlineFromStroke($stroke);
		$s = '<svg>';
		$sc = '';
		$s .= '<path d="';
		foreach ($outline as $line) {
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
		$s .= ' " fill="#dddddd" stroke="#000000" stroke-width="1" />';
		foreach ($outline as $line) {
			foreach ($line as $index => $l) {
				$sc .= "<circle cx='{$l['x']}' cy='{$l['y']}' r='2' />";
			}
		}
		$s .= $sc.'</svg>';
		echo $s;


		echo '<hr />ストローク shi<br />';
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

		echo '<hr />アウトライン shi<br />';
		$s = '<svg>';
		$s .= ' <path d="M86,57 c-10.481,21.489 -24.904,32 -39,32 c-13.079,0 -21,-8.539 -21,-28 c0,-16.818 2,-32.069 2,-50" fill="none" stroke="black"/>';
		$s .= '</svg>';

// echo $s.'<br />';

		$stroke = self::parseStrokeSvg($s);
		$outline = self::getOutlineFromStroke($stroke);
		$s = '<svg>';
		$sc = '';
// dd($outline);
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



		echo '<hr />ストローク a<br />';
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

		echo '<hr />アウトライン a<br />';
		$s = '<svg>';
		// $s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="14" x2="86" y1="21" y2="21" />';
		// $s .= '<line fill="none" stroke="#000000" stroke-width="2" x1="38" x2="38" y1="6" y2="81" />';
		$s .= '<path d="M68,40 c-8.337,27.624 -28.133,45 -43,45 c-7.852,0 -13,-4.894 -13,-15 c0,-17.828 17.78,-32 43,-32 C76.039,38 88,48.227 88,63 C88,76.87 77.755,86.868 60,90" fill="none" stroke="#000000" stroke-width="2" />';
		$s .= '</svg>';

// echo $s.'<br />';

		$stroke = self::parseStrokeSvg($s);
		$outline = self::getOutlineFromStroke($stroke);
		$s = '<svg>';
		$sc = '';
		$s .= '<path d="';
		foreach ($outline as $lineNumber => $line) {

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
		$s .= ' " fill="#dddddd" stroke="#000000" stroke-width="1" />';
		foreach ($outline as $line) {
			foreach ($line as $index => $l) {
				$sc .= "<circle cx='{$l['x']}' cy='{$l['y']}' r='2' />";
			}
		}
		$s .= $sc.'</svg>';
		echo $s;



		echo '<hr />glyph ke test<br />';
		$s = '<svg>';
		$s .= '<path d="M67.6,25.2 c0,10.696 0.801,20.25 0.801,30.399 c0,22.076 -4.486,28.258 -20,33.601" fill="none" stroke="#000000" stroke-width="1" />';
		$s .= '<path d="M82.801,42 c-16.955,1.841 -27.952,2.4 -44,2.4" fill="none" stroke="#000000" stroke-width="1" />';
		$s .= '<path d="M26.801,88.4 c-2.852,-10.643 -4,-19.333 -4,-30.4 c0,-11.066 1.148,-19.758 4,-30.4" fill="none" stroke="#000000" stroke-width="1" />';
		$s .= '</svg>';
		echo $s.$shi;

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

// dd('OK');

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

	public static function getOutlineFromStroke($stroke)
	{
		$outline = [];
		$thickness = 8; // 太さ
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
// dump($up);
// dump($down);
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


			$shapeList = self::getShapeOutlines($shape);

			foreach ($shapeList as $s) {
				$outline[] = $s;
				// self::addShapeToOutline($outline, $s);
			}

			// $outline[] = array_merge($outlineUp, $outlineDown);
		}
dump($outline);
		return $outline;
	}

	protected static function getShapeOutlines($shapePointList)
	{
// return [$shapePointList];
		$pointsCount = count($shapePointList);
		$isPointPassedList = array_fill(0, $pointsCount, false);

		$outlineList = [];
		$index = 0;
		while($index !== false) {
			$firstIndex = $index;
			$outline = [];
			for ($i = 0; $i < $pointsCount; $i++) {
				$p = $shapePointList[$index];
				$isPointPassedList[$index] = true;
$p['isOnCurvePoint'] = true;
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
			$outlineList[] = $outline;

			$index = array_search(false, $isPointPassedList);
		}

// dd($outlineList);
// dump($isPointPassedList);

		$outlineList = self::removeLostedShape($outlineList);
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

	protected static function removeLostedShape($outline)
	{
		$directionList = [];
		foreach ($outline as $lineIndex => $line) {
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
		// dump($directionList);

		$aliveShapes = [];
		foreach ($outline as $i => $line) {
			$outsideIndex = self::getOutsideShapeIndex($outline, $i);

// echo "t:{$i}({$directionList[$i]})";
// if ($outsideIndex > -1) {
// 	echo " , out:{$outsideIndex}({$directionList[$outsideIndex]})<br />";
// } else {
// 	echo '<br />';
// }

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

	protected static function isInsideShape($shape, $index)
	{
		$base = $shape[$index];
		$crossCount = 0;

		$shapePointCount = count($shape);
		foreach ($shape as $i => $p) {
			$nextIndex = ($i + 1) % $shapePointCount;
			$next = $shape[$nextIndex];

			$yMin = min($p['y'], $next['y']);
			$yMax = min($p['y'], $next['y']);
			if (($yMin <= $base['y']) && ($yMax >= $base['y'])) {
				if ($base['x'] <= $p['x']) {
					if ($base['x'] <= $next['x']) {



						return true;


					}
				}
			}
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

		if (!self::isInsideBox($v2, $crossed)) {
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

		// if (!self::isInsideBox($v2, $crossed)) {
		// 	return null;
		// }

		return $crossed;
	}


	public static function getCrossPointXxxx($v1, $v2)
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
		if (($crossVectorLengthBase < 0)) {
			return null;
		}

		$crossed = [
			'x' => $v1[0]['x'] + (($v1[1]['x'] - $v1[0]['x']) * $crossVectorLengthBase),
			'y' => $v1[0]['y'] + (($v1[1]['y'] - $v1[0]['y']) * $crossVectorLengthBase),
			'length' => $crossVectorLengthBase,
		];

		if (!self::isInsideBox($v2, $crossed)) {
			return null;
		}

		return $crossed;
	}

	public static function isInsideBox($box, $point)
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

	public static function addShapeToOutline(&$baseOutline, $shape)
	{
		$isComposed = false;
		foreach ($baseOutline as &$contour) {
			if (self::composeShapes($contour, $shape)) {
				$isComposed = true;
			}
		}
		unset($contour);
		if (!$isComposed) {
			$baseOutline[] = $shape;
		}

		return $baseOutline;
	}

	public static function composeShapes(&$base, $addition)
	{
		$composed = [];

		$count = count($base);
		$isComposed = false;
		for ($i = 0; $i < $count; $i++) {
			if (self::addCoordinateToContour($composed, $i, $base, $addition)) {
				$isComposed = true;
			}
		}
		if ($isComposed) {
			$base = $composed;
		}

		return $isComposed;
	}

	protected static function addCoordinateToContour(&$coordinateList, &$index, $base, $other)
	{
// return false;
		$coordinateList[] = $base[($index)];

		$baseVector = [
			$base[$index],
			$base[($index + 1) % count($base)],
		];

		$crossInfo = self::getCrossPointToShape($other, $baseVector, null);
		if (is_null($crossInfo['point'])) {
			return false;
		}
		$p = [
			'x' => $crossInfo['point']['x'],
			'y' => $crossInfo['point']['y'],
			'isOnCurvePoint' => true,
		];
		$coordinateList[] = $p;
		$addtionOffset = $crossInfo['index'] + 1;

		$count = count($other);
		$prevPoint = $p;
		for ($i = 0; $i <= $count; $i++) {
			$s = $other[($i + $addtionOffset) % $count];
			$ignoreIndex = null;
			if ($i == 0) {
				$ignoreIndex = $index;
			}
			$crossPoint = self::getCrossPointToShape($base, [$prevPoint, $s], $ignoreIndex);

			if (is_null($crossPoint['point'])) {
				$coordinateList[] = $s;
			} else {
				$coordinateList[] = [
					'x' => $crossPoint['point']['x'],
					'y' => $crossPoint['point']['y'],
					'isOnCurvePoint' => true,
				];
				$index = $crossPoint['index'];
				break;
			}

			$prevPoint = $s;
		}
		return true;
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

		$crossInfo = self::getCrossPointToShape($other, $baseVector, null);
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
			$crossPoint = self::getCrossPointToShape($base, [$prevPoint, $s], $ignoreIndex);

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

	protected static function getCrossPointToShape($shape, $vector, $ignoreIndex)
	{
		$count = count($shape);

		$crossPoint = null;
		$crossIndex = null;
		for ($i = 0; $i < $count; $i++) {
			$s = $shape[$i];
			$e = $shape[($i + 1) % $count];

			if ($i !== $ignoreIndex) {
				$cp = self::getCrossPoint($vector, [$s, $e]);
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
