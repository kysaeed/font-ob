<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class Stroke extends Model
{
	public static function createFromSvg($code, $svg = null)
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
		return $stroke;
	}


	protected static function parseLineSvg($svg)
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

	protected static function parsePathSvg($svg)
	{
		$pathParams = [];
		$d = [];
		if (preg_match('/\s+d=["\|\'](.[^"]*)/', $svg, $d)) {
			$pathMatches = [];
			preg_match_all('/([MmCcVvHh]\s?)?((-?[\d.]+),?(-?[\d.]+)?)/', $d[1], $pathMatches);

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

	protected $fillable = [
		'code',
		'data',
	];
	protected $casts = [
		'data' => 'json',
	];
}
