<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class Stroke extends Model
{
	public static function createFromSvg($svg, $code = 0)
	{
		if (!is_null($svg)) {
			$data = self::parseStrokeSvg($svg);
		} else {
			$data = [];
		}

		return new Self([
			'code' => $code,
			'data' => $data,
		]);
	}

	protected static function parseStrokeSvg($svg)
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
		if (preg_match_all('/<polyline\s+[^\/]+\/>/', $svg, $pathSvg)) {
			foreach ($pathSvg[0] as $p) {
				$stroke[] = self::parsePolylineSvg($p);
			}
		}

		return $stroke;
	}

	public static function parsePolylineSvg($svg)
	{
		$points = [];
		if (preg_match('/points\s*=\s*"([^"]+)"/', $svg, $d)) {

			if (preg_match_all('/((-?[\d.]+),?(-?[\d.]+)?)/', $d[1], $pathMatches)) {
				$count = count($pathMatches[0]);
				for ($i = 0; $i < $count; $i++) {
					$points[] = [
						'x' => (float)$pathMatches[2][$i],
						'y' => (float)$pathMatches[3][$i],
						'isOnCurvePoint' => true,
					];
				}
			}
		}

		return [
			'path' => $points,
			'isClosed' => false,
		];
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
			if (preg_match('/'.$attr.'\s*=\s*"([^"]+)"/', $svg, $d)) {
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
		$points = [];

		$d = [];
		if (preg_match('/\s+d=["\|\'](.[^"]*)/', $svg, $d)) {
			$pathMatches = [];
			preg_match_all('/([MmCcSsQqVvHh]\s?)?((-?[\d.]+),?(-?[\d.]+)?)/', $d[1], $pathMatches);

			$prev = [
				'x' => 0,
				'y' => 0,
			];

			$prevC = null;
			$prevS = null;


			$isRelativePosition = false;
			$count = count($pathMatches[0]);
			for ($index = 0; $index < $count; $index++) {
				$command = $pathMatches[1][$index];
//dump($pathMatches[0][$index]);
				switch ($command) {
					case 'C':
					case 'c':
						$pointList = self::parsePathSvgCommandC($index, $prev,  $isRelativePosition, $pathMatches, $prevC);
						$points = array_merge($points, $pointList);

						break;

					case 'S':
					case 's':
						$pointList = self::parsePathSvgCommandS($index, $prev,  $isRelativePosition, $pathMatches, $prevC);
						$points = array_merge($points, $pointList);
						break;
					case 'Q':
					case 'q':
						$pointList = self::parsePathSvgCommandQ($index, $prev, $isRelativePosition, $pathMatches);
						$points = array_merge($points, $pointList);
						break;

					case 'H':
					case 'h':
						$points[] = self::parsePathSvgCommandH($index, $prev, $isRelativePosition, $pathMatches);
						break;

					case 'V':
					case 'v':
						$points[] = self::parsePathSvgCommandV($index, $prev, $isRelativePosition, $pathMatches);
						break;

					case 'M':
					default:
						$points[] = self::parsePathSvgPoint($index, $prev, $isRelativePosition, $pathMatches);
						break;
				}
//dump($points);

			}


			$isClosed = preg_match('/[zZ]\s*$/', $d[1]);
			return [
				'path' => $points,
				'isClosed' => $isClosed,
			];
		}

		return self::_parsePathSvg($svg);
	}

	public static function parsePathSvgPoint(&$index, &$prev, &$isRelativePosition, $pathMatches)
	{
		if ($isRelativePosition) {
			$point = [
				'x' => (float)$pathMatches[3][$index] + $prev['x'],
				'y' => (float)$pathMatches[4][$index] + $prev['y'],
				'isOnCurvePoint' => true,
			];
		} else {
			$point = [
				'x' => (float)$pathMatches[3][$index],
				'y' => (float)$pathMatches[4][$index],
				'isOnCurvePoint' => true,
			];
		}

		$prev = [
			'x' => $point['x'],
			'y' => $point['y'],
		];

		return $point;
	}

	public static function parsePathSvgCommandH(&$index, &$prev, &$isRelativePosition, $pathMatches)
	{
		if ($pathMatches[1][$index] == 'H') {
			$point = [
				'x' => (float)$pathMatches[3][$index],
				'y' => $prev['y'],
				'isOnCurvePoint' => true,
			];
			$isRelativePosition = false;
		} else {
			$point = [
				'x' => (float)$pathMatches[3][$index] + $prev['x'],
				'y' => $prev['y'],
				'isOnCurvePoint' => true,
			];
			$isRelativePosition = true;
		}

		$prev = [
			'x' => $point['x'],
			'y' => $point['y'],
		];

		return $point;
	}

	public static function parsePathSvgCommandV(&$index, &$prev, &$isRelativePosition, $pathMatches)
	{
		if ($pathMatches[1][$index] == 'V') {
			$point = [
				'x' => $prev['x'],
				'y' => (float)$pathMatches[3][$index],
				'isOnCurvePoint' => true,
			];
			$isRelativePosition = false;
		} else {
			$point = [
				'x' => $prev['x'],
				'y' => (float)$pathMatches[3][$index] + $prev['y'],
				'isOnCurvePoint' => true,
			];
			$isRelativePosition = true;
		}

		$prev = [
			'x' => $point['x'],
			'y' => $point['y'],
		];

		return $point;
	}

	public static function parsePathSvgCommandC(&$index, &$prev, &$isRelativePosition, $pathMatches, &$prevC)
	{
		$points = [];
		if ($pathMatches[1][$index] == 'C') {
			$isRelativePosition = false;
			for ($i = 0; $i < 3; $i++) {
				$points[] = [
					'x' => (float)$pathMatches[3][$index + $i],
					'y' => (float)$pathMatches[4][$index + $i],
				];
			}
		} else {
			$isRelativePosition = true;
			for ($i = 0; $i < 3; $i++) {
				$points[] = [
					'x' => (float)$pathMatches[3][$index + $i] + $prev['x'],
					'y' => (float)$pathMatches[4][$index + $i] + $prev['y'],
				];
			}
		}

		$index += 2;

		$curves = self::svgCtoQ($prev, $points);
		$prevC = $points[1];
		$prev = $points[2];

		return [
			[
				'x' => $curves[0][1]['x'],
				'y' => $curves[0][1]['y'],
				'isOnCurvePoint' => false,
			],
			[
				'x' => $curves[0][2]['x'],
				'y' => $curves[0][2]['y'],
				'isOnCurvePoint' => true,
			],
			[
				'x' => $curves[1][1]['x'],
				'y' => $curves[1][1]['y'],
				'isOnCurvePoint' => false,
			],
			[
				'x' => $curves[1][2]['x'],
				'y' => $curves[1][2]['y'],
				'isOnCurvePoint' => true,
			],
		];
	}

	public static function parsePathSvgCommandS(&$index, &$prev, &$isRelativePosition, $pathMatches, $prevC)
	{
		$points = [];


		$points[] = [
			'x' => $prev['x'] - ($prevC['x'] - $prev['x']),
			'y' => $prev['y'] - ($prevC['y'] - $prev['y']),
		];


		if ($pathMatches[1][$index] == 'S') {
			$isRelativePosition = false;
			for ($i = 0; $i < 2; $i++) {
				$points[] = [
					'x' => (float)$pathMatches[3][$index + $i],
					'y' => (float)$pathMatches[4][$index + $i],
				];
			}
		} else {
			$isRelativePosition = true;
			for ($i = 0; $i < 2; $i++) {
				$points[] = [
					'x' => (float)$pathMatches[3][$index + $i] + $prev['x'],
					'y' => (float)$pathMatches[4][$index + $i] + $prev['y'],
				];
			}
		}

		$index += 2;

		$curves = self::svgCtoQ($prev, $points);
		$prev = $points[2];

		return [
			[
				'x' => $curves[0][1]['x'],
				'y' => $curves[0][1]['y'],
				'isOnCurvePoint' => true,
			],
			[
				'x' => $curves[0][2]['x'],
				'y' => $curves[0][2]['y'],
				'isOnCurvePoint' => true,
			],
			[
				'x' => $curves[1][1]['x'],
				'y' => $curves[1][1]['y'],
				'isOnCurvePoint' => true,
			],
			[
				'x' => $curves[1][2]['x'],
				'y' => $curves[1][2]['y'],
				'isOnCurvePoint' => true,
			],
		];
	}

	public static function parsePathSvgCommandQ(&$index, &$prev, &$isRelativePosition, $pathMatches)
	{
		$points = [];
		if ($pathMatches[1][$index] == 'Q') {
			$isRelativePosition = false;
			for ($i = 0; $i < 2; $i++) {
				$points[] = [
					'x' => (float)$pathMatches[3][$index + $i],
					'y' => (float)$pathMatches[4][$index + $i],
					'isOnCurvePoint' => ($i == 1),
				];
			}
		} else {
			$isRelativePosition = true;
			for ($i = 0; $i < 2; $i++) {
				$points[] = [
					'x' => (float)$pathMatches[3][$index + $i] + $prev['x'],
					'y' => (float)$pathMatches[4][$index + $i] + $prev['y'],
					'isOnCurvePoint' => ($i == 1),
				];
			}
		}

		$index += 1;
		$prev = $points[1];

		return $points;
	}

	public static function _parsePathSvg($svg)
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

	public function toSvg()
	{
		$svg = '<svg>';
		foreach ($this->data as $line) {
			$svg .= '<path d="M ';
			$cCount = 0;

			$count = count($line['path']);
			for ($index = 0; $index < $count; $index++) {
				$p = $line['path'][$index];
				$next = $line['path'][($index + 1) % $count];

				if ($p['isOnCurvePoint']) {
					if ($index != 0) {
						$svg .= 'L';
					}
					$svg .= "{$p['x']},{$p['y']} ";
				} else {
					$svg .= "Q {$p['x']},{$p['y']} ";
					$svg .= "{$next['x']},{$next['y']} ";
					$index++;
				}
			}
			$svg .= '" fill="none" stroke="blue" stroke-width="1" />';
		}
		$svg .= '</svg>';
		return $svg;
	}

	protected $fillable = [
		'code',
		'data',
	];
	protected $casts = [
		'data' => 'json',
	];
}
