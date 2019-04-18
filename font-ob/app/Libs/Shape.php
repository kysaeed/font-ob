<?php

namespace FontObscure\Libs;


class Shape
{

	protected $points = [];


	function __construct($points)
	{
		$this->points = self::sliceShape($points);
	}

	public function getPoints()
	{
		return $this->points;
	}

	protected static function sliceShape($shape)
	{
// echo '<h2>sliceShape</h2>';
// echo self::testOutlineToSvg([$shapePointList]);
// echo '<hr />';
		$shapeInfo = self::insertSelfCorssPointToShape($shape);
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

	protected static function getBezier2CurvePoint($s, $e, $a, $t)
	{
		return [
			'x' => ($s['x'] * pow(1 - $t, 2)) + ($a['x'] * ((1 - $t) * 2) * $t) + ($e['x'] * pow($t, 2)),
			'y' => ($s['y'] * pow(1 - $t, 2)) + ($a['y'] * ((1 - $t) * 2) * $t) + ($e['y'] * pow($t, 2)),
		];
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

	public static function crossProduct($v1, $v2)
	{
		return (
			($v1[1]['x'] - $v1[0]['x']) * ($v2[1]['y'] - $v2[0]['y']) -
			($v1[1]['y'] - $v1[0]['y']) * ($v2[1]['x'] - $v2[0]['x'])
		);
	}

}
