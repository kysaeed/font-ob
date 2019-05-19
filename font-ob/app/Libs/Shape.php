<?php

namespace FontObscure\Libs;
use FontObscure\Http\Controllers\TestController;


class Shape
{

	public $points = [];

	function __construct($points)
	{
		$this->points = $points;
	}

	public static function createFromStroke($stroke)
	{
		$thickness = 4; // 太さ


		$outlineUp = [];
		$outlineDown = [];
		$lineCount = count($stroke['path']);
		$maxLineIndex = $lineCount - 1;
		foreach ($stroke['path'] as $index => $l) {
			$prevIndex = ($index + ($lineCount - 1)) % $lineCount;
			if ($index == 0) {
				$lNext = $stroke['path'][($index + 1) % $lineCount];
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
				$lPrev = $stroke['path'][$prevIndex];
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

				$lPrev = $stroke['path'][$prevIndex];
				$lNext = $stroke['path'][($index + 1) % $lineCount];
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

		$points = array_merge($outlineUp, $outlineDown);
		return new Self($points);
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

	public function getPoints()
	{
		return $this->points;
	}

	public function slice()
	{
		$shapeInfo = $this->insertSelfCorssPointToShape();
		$slicedShapeList = [];

		$points = $shapeInfo['shape'];
		$infos = $shapeInfo['crossInfo'];
		$pointsCount = count($points);
		$isPointPassedList = [];

		foreach ($infos as $i => $s) {
			$p = $points[$i];
			if (($s > -1) || (!$p['isOnCurvePoint'])) {
				$isPointPassedList[] = true;
			} else {
				$isPointPassedList[] = false;
			}
		}

		foreach ($points as $i => $p) {
			if (self::isInsideShapePoint($points, $p, $i)) {
				$isPointPassedList[$i] = true;
			}
		}

		$firstIndex = array_search(false, $isPointPassedList);
		$hasClockwiseShape = false;
		while($firstIndex !== false) {
			$slicedPoints = [];
			$index = $firstIndex;
			for ($i = 0; $i < $pointsCount + 1; $i++) {
				$isPointPassedList[$index] = true;

				$p = $points[$index];
				$slicedPoints[] = $p;

				if ($i != 0) {
					if ($infos[$index] > -1) {
						$index = $infos[$index];
					}
				}

				$index = ($index + 1) % $pointsCount;
				if ($index == $firstIndex) {
					break;
				}
			}
			$shape = new Shape($slicedPoints);
			$direction = $shape->getShapeDirection();
			if ($direction > 0) {
				if (!$hasClockwiseShape) {
					$slicedShapeList[] = $shape;
					$hasClockwiseShape = true;
				}
			} else {
				$slicedShapeList[] = $shape;
			}

			$firstIndex = array_search(false, $isPointPassedList);
		}

		return $slicedShapeList;
	}

	public function compose($addition)
	{
//z
		$baseInfo = [];
		foreach ($this->points as $p) {
			$baseInfo[] = [
				'point' => $p,
				'crossInfo' => [],
				'index' => null,
			];
		}
		$baseCount = count($this->points);

		$additionInfo = [];
		foreach ($addition->points as $a) {
			$additionInfo[] = [
				'point' => $a,
				'crossInfo' => [],
				'index' => null,
			];
		}
		$additionCount = count($addition->points);

		$infoList = [
			&$baseInfo,
			&$additionInfo,
		];

		$crossCount = 0;
		for ($oc = 0; $oc < $baseCount; $oc++) {
			$p = &$baseInfo[$oc]['crossInfo'];
			if (!$baseInfo[$oc]['point']['isOnCurvePoint']) {
				continue;
			}
			$crossList = $this->getCorssInfoListByShape($oc, $addition);
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

///////////////////////////////////////
//$o = [];
//$o[] = $this->points;
//$o[] = $addition->points;
//
//$cross = [];
//foreach ($baseInfo as $info) {
//	foreach ($info['crossInfo'] as $ci) {
//		$cross[] = $ci['point'];
//	}
//}
//echo TestController::testOutlineToSvg($o, false, $cross);
////dd($o);
////return null;
//////////////////////////////////

//echo "cross-count={$crossCount}<br />";
//echo '<svg>';
//echo $this->toSvg();
//echo $addition->toSvg();
//foreach ($baseInfo as $list) {
//	foreach ($list['crossInfo'] as $c) {
//		$p = $c['point'];
//		echo "<circle cx='{$p['x']}' cy='{$p['y']}' r='3' fill='green'>";
//	//	dump($p);
//	}
//}
//echo '</svg>';

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
			$count = count($infos);
			$index = 0;
			foreach ($infos as $i => &$p) {
				$isCurve = !$infos[($i + 1) % $count]['point']['isOnCurvePoint'];

				$p['index'] = $index;
				$index++;

				foreach ($p['crossInfo'] as &$list) {
					if ($isCurve) {
						$index++;
					}
					$list['index'] = $index;
					$index++;
				}
				unset($list);
			}
			unset($p);
		}
		unset($infos);
//dump($infoList);

		$shapeList = [];
		$corssIndexInfoList = [];
		foreach ($infoList as $list) {
			$count = count($list);
			$shape = [];
			$corssIndexInfo = [];

			foreach ($list as $i => $p) {
				if (!$p['point']['isOnCurvePoint']) {
					continue;
				}

				$shape[] = $p['point'];
				$corssIndexInfo[] = -1;

				$next = $list[($i + 1) % $count];
				$isCurve = false;
				if (!$next['point']['isOnCurvePoint']) {
					$isCurve = true;
				}

				if (empty($p['crossInfo'])) {
					if ($isCurve) {
						$shape[] = $next['point'];
						$corssIndexInfo[] = -1;
					}
				} else {
					$end = $list[($i + 2) % $count];
					$start = 0;
					foreach ($p['crossInfo'] as $cross) {
						if ($isCurve) {


							$bezier = [
								$p['point'],
								$next['point'],
								$end['point'],
							];

							$segment = self::getBezier2Segmet($bezier, $start, $cross['length']);
							$start = $cross['length'];

							$shape[] = [
								'x' => $segment[1]['x'],
								'y' => $segment[1]['y'],
								'isOnCurvePoint' => false,
							];
							$corssIndexInfo[] = -1;

						}


						$shape[] = [
							'x' => $cross['point']['x'],
							'y' => $cross['point']['y'],
							'isOnCurvePoint' => true,
						];
						$corssIndexInfo[] = $cross['to']['index'];
						$start = $cross['length'];

//						$shape[] = $next['point'];
//						$corssIndexInfo[] = -1;

					}

					if ($isCurve) {
						$segment = self::getBezier2Segmet($bezier, $start, 1);
						$shape[] = [
							'x' => $segment[1]['x'],
							'y' => $segment[1]['y'],
							'isOnCurvePoint' => false,
						];
						$corssIndexInfo[] = -1;
					}
				}
			}

			$shapeList[] = new Shape($shape);
			$corssIndexInfoList[] = $corssIndexInfo;
		}

		$passedIndexInfoList = [];
		foreach ($shapeList as $shapeIndex => $shape) {
			$corssIndexInfo = $corssIndexInfoList[$shapeIndex];
			$passedIndexInfo = [];
			foreach ($corssIndexInfo as $to) {
				if ($to < 0) {
					$passedIndexInfo[] = false;
				} else {
					$passedIndexInfo[] = true;
				}
			}
			$passedIndexInfoList[] = $passedIndexInfo;
		}

//echo '<p><h1>shapeList</h1>';
//foreach ($shapeList as $s) {
//	echo '<svg>'.$s->toSvg().'</svg>';
//}
//echo '</p>';

		$newShapeList = [];
		$hasClockWiseShape = false;
		foreach ($shapeList as $shapeIndex => $shape) {

			$corssIndexInfo = $corssIndexInfoList[$shapeIndex];
			$count = count($shape->points);
			$passedIndexInfo = &$passedIndexInfoList[$shapeIndex];

			$otherPassedIndexInfo = &$passedIndexInfoList[1- $shapeIndex];
			$otherCorssIndexInfo = $corssIndexInfoList[1- $shapeIndex];
			$otherShape = $shapeList[1 - $shapeIndex];
			$otherCount = count($otherShape->points);

			$firstIndex = 0;
			foreach ($shape->points as $i => $p) {
				if (!$p['isOnCurvePoint']) {
					continue;
				}
				if ($corssIndexInfo[$i] > -1) {
					$firstIndex = $i;
					break;
				}
				if (!$otherShape->isInsidePoint($p)) {
					$firstIndex = $i;
					break;

				}
			}

			while ($firstIndex !== false) {
				$index = $firstIndex;
				$newShape = [];
				for ($i = 0; $i < $count; $i++) {
					$isEnd = false;
					$newShape[] = $shape->points[$index];

//					$isCurve = false;
//					if (!$shape->points[$index]['isOnCurvePoint']) {
//						$isCurve = true;
//					}

					$passedIndexInfo[$index] = true;

					$otherIndex = $corssIndexInfo[$index];
					if ($i != 0) {
						if ($otherIndex > -1) {
//dd($corssIndexInfo);
//							if ($isCurve) {
//								$s = $shape->points[$index];
//								$segment = self::getBezier2Segmet([$s], 0, 1);
//								$newShape[] = [
//									'x' => $segment[1]['x'],
//									'y' => $segment[1]['y'],
//									'isOnCurvePoint' => false,
//								];
//							}

							$otherIndex = ($otherIndex + 1) % $otherCount;
							for ($oc = 0; $oc < $otherCount; $oc++) {
								$otherPassedIndexInfo[$otherIndex] = true;
								$newShape[] = $otherShape->points[$otherIndex];
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
					}


					$index = ($index + 1) % $count;
					if ($index == $firstIndex) {
						$isEnd = true;
					}

					if ($isEnd) {
						break;
					}
				}

				$s = new Shape($newShape);
				if ($s->getShapeDirection() > 0) {
					if (!$hasClockWiseShape) {
						$hasClockWiseShape = true;
						$newShapeList[] = $s;
					}
				} else {
					$newShapeList[] = $s;
				}

				$firstIndex = array_search(false, $passedIndexInfo);
			}
			unset($passedIndexInfo);
			unset($otehrPassedIndexInfo);
		}

//echo '<h3>結果</h3>';
//echo 'count='.count($newShapeList).'<br />';
//foreach ($newShapeList as $shape) {
//	echo '<svg>';
//	echo $shape->toSvg();
//	echo '</svg>';
//}
//echo '<hr />';

		return $newShapeList;
	}

	public function composeXorByAnticlockList($anticlockList)
	{
echo '<h1>composeXorByAnticlockList</h1>';
echo '<svg>'.$this->toSvg().'</svg>';

dump($anticlockList);
foreach ($anticlockList as $s) {
	echo '<svg>'.$s->toSvg().'</svg>';
}
echo '<br />- - - - - - - -<br />';

		$newClockList = [$this];
		$newAnticlockList = [];

		foreach ($anticlockList as $anticlock) {
			$nc = [];
			$scliedAnticlock = [$anticlock];
			foreach ($newClockList as $c) {
				$_ac = [];
				foreach ($scliedAnticlock as $a) {
					$composed = $c->composeXor($a);
					if (empty($composed)) {
						$nc[] = $c;
						$_ac[] = $a;
					} else {
						$nc = array_merge($nc, $composed[0]);
						$_ac = array_merge($_ac, $composed[1]);
					}
				}
				$scliedAnticlock = $_ac;
			}
			$newClockList = $nc;
			$newAnticlockList = array_merge($newAnticlockList, $scliedAnticlock);
		}

echo '<hr />結果：<br />';
foreach ($newClockList as $s) {
	echo '<svg>'.$s->toSvg().'</svg>';
}
echo '**';
foreach ($newAnticlockList as $s) {
	echo '<svg>'.$s->toSvg().'</svg>';
}
echo '<hr />';

		return [
			$newClockList,
			$newAnticlockList,
		];
	}

	public function composeXor($addition)
	{
//echo '<h1>composeXor</h1>';
//echo '<svg>'.$this->toSvg().'</svg>';
//echo '<svg>'.$addition->toSvg().'</svg>';
//echo '<hr />';

		$baseInfo = [];
		foreach ($this->points as $p) {
			$baseInfo[] = [
				'point' => $p,
				'crossInfo' => [],
				'index' => null,
			];
		}
		$baseCount = count($this->points);

		$additionInfo = [];
		foreach ($addition->points as $a) {
			$additionInfo[] = [
				'point' => $a,
				'crossInfo' => [],
				'index' => null,
			];
		}
		$additionCount = count($addition->points);

		$infoList = [
			&$baseInfo,
			&$additionInfo,
		];

		$crossCount = 0;
		for ($oc = 0; $oc < $baseCount; $oc++) {
			$p = &$baseInfo[$oc]['crossInfo'];
			if (!$baseInfo[$oc]['point']['isOnCurvePoint']) {
				continue;
			}

			$crossList = $this->getCorssInfoListByShape($oc, $addition);
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
			$count = count($infos);
			$index = 0;
			foreach ($infos as $i => &$p) {
				$isCurve = !$infos[($i + 1) % $count]['point']['isOnCurvePoint'];

				$p['index'] = $index;
				$index++;

				foreach ($p['crossInfo'] as &$list) {
					if ($isCurve) {
						$index++;
					}
					$list['index'] = $index;
					$index++;
				}
				unset($list);
			}
			unset($p);
		}
		unset($infos);
//dump($infoList);

		$shapeList = [];
		$corssIndexInfoList = [];
		foreach ($infoList as $list) {
			$count = count($list);
			$shape = [];
			$corssIndexInfo = [];

			foreach ($list as $i => $p) {
				if (!$p['point']['isOnCurvePoint']) {
					continue;
				}

				$shape[] = $p['point'];
				$corssIndexInfo[] = -1;

				$next = $list[($i + 1) % $count];
				$isCurve = false;
				if (!$next['point']['isOnCurvePoint']) {
					$isCurve = true;
				}

				if (empty($p['crossInfo'])) {
					if ($isCurve) {
						$shape[] = $next['point'];
						$corssIndexInfo[] = -1;
					}
				} else {
					$end = $list[($i + 2) % $count];
					$start = 0;
					foreach ($p['crossInfo'] as $cross) {
						if ($isCurve) {
							$bezier = [
								$p['point'],
								$next['point'],
								$end['point'],
							];

							$segment = self::getBezier2Segmet($bezier, $start, $cross['length']);
							$start = $cross['length'];

							$shape[] = [
								'x' => $segment[1]['x'],
								'y' => $segment[1]['y'],
								'isOnCurvePoint' => false,
							];
							$corssIndexInfo[] = -1;

						}

						$shape[] = [
							'x' => $cross['point']['x'],
							'y' => $cross['point']['y'],
							'isOnCurvePoint' => true,
						];
						$corssIndexInfo[] = $cross['to']['index'];
						$start = $cross['length'];

//						$shape[] = $next['point'];
//						$corssIndexInfo[] = -1;

					}

					if ($isCurve) {
						$segment = self::getBezier2Segmet($bezier, $start, 1);
						$shape[] = [
							'x' => $segment[1]['x'],
							'y' => $segment[1]['y'],
							'isOnCurvePoint' => false,
						];
						$corssIndexInfo[] = -1;
					}
				}
			}

			$shapeList[] = new Shape($shape);
			$corssIndexInfoList[] = $corssIndexInfo;
		}

		$composed = [];
		foreach ($shapeList as $shapeIndex => $shape) {
			$newShapeList = [];

			$otherCorssIndexInfo = $corssIndexInfoList[1- $shapeIndex];
			$otherShape = $shapeList[1 - $shapeIndex];
			$otherCount = count($otherShape->points);

			$corssIndexInfo = $corssIndexInfoList[$shapeIndex];
			$count = count($shape->points);

			$passedIndexInfo = [];
			foreach ($corssIndexInfo as $i => $to) {
				if ($to < 0) {
					if ($shapeIndex == 1) {
						if ($this->isInsidePoint($shape->points[$i])) {
							$passedIndexInfo[] = true;
						} else {
							$passedIndexInfo[] = false;
						}
					} else {
						$passedIndexInfo[] = false;
					}
				} else {
					$passedIndexInfo[] = true;
				}
			}
//dd(compact('shape','corssIndexInfo'));

			$firstIndex = array_search(false, $passedIndexInfo);
			while ($firstIndex !== false) {
				$index = $firstIndex;
				$newShape = [];
				for ($i = 0; $i < $count; $i++) {
					$isEnd = false;
					$newShape[] = $shape->points[$index];

//					$isCurve = false;
//					if (!$shape->points[$index]['isOnCurvePoint']) {
//						$isCurve = true;
//					}

					$passedIndexInfo[$index] = true;

					$otherIndex = $corssIndexInfo[$index];
					if ($otherIndex > -1) {
//dd($corssIndexInfo);
//						if ($isCurve) {
//							$s = $shape->points[$index];
//							$segment = self::getBezier2Segmet([$s], 0, 1);
//							$newShape[] = [
//								'x' => $segment[1]['x'],
//								'y' => $segment[1]['y'],
//								'isOnCurvePoint' => false,
//							];
//						}

						$otherIndex = ($otherIndex + 1) % $otherCount;
						for ($oc = 0; $oc < $otherCount; $oc++) {
							$newShape[] = $otherShape->points[$otherIndex];
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
				$newShapeList[] = new Shape($newShape);
				$firstIndex = array_search(false, $passedIndexInfo);
			}

			$composed[] = $newShapeList;
		}

//echo '<h1>結果</h1>';
//foreach ($composed[0] as $c) {
//	echo '<svg>'.$c->toSvg().'</svg>';
//}
//echo '<hr />';

		return $composed;
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

	public function getShapeDirection()
	{
		$pointCount = count($this->points);

		$sum = 0;
		for ($i = 0; $i < $pointCount; $i++) {
			$vector = [
				$this->points[$i % $pointCount],
				$this->points[($i + 1) % $pointCount],
			];

			$d = ($vector[0]['x'] * $vector[1]['y']) - ($vector[1]['x'] * $vector[0]['y']);

			$sum += $d;
		}

		if ($sum > 0) {
			return 1;
		} else if ($sum < 0){
			return -1;
		}
		return 0;
	}

	public static function getCrossPointEx($v1, $v2)
	{
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

		$b = self::crossProduct(
			$v2,
			[$v2[0], $v1[1]]
		) /* / 2 */;

		$ab = ($a + $b);
		if (!$ab) {
			return null;
		}

		$crossVectorLengthBase = ($a / $ab);
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

	protected function getCorssInfoListByShape($index, $addition)
	{
//echo "<h1>{$index}</h1>";
		$baseCount = count($this->points);
		$additionCount = count($addition->points);


		$corssInfoList = [];

		$v = [
			$this->points[$index],
			$this->points[($index + 1) % $baseCount],
		];

		if ($v[1]['isOnCurvePoint']) {
			for ($i = 0; $i < $additionCount; $i++) {
				$p = $addition->points[$i];
				if (!$p['isOnCurvePoint']) {
					continue;
				}
				$next = $addition->points[($i + 1) % $additionCount];

				if ($next['isOnCurvePoint']) {
					$cp = self::getCrossPointEx($v, [$p, $next]);
					if (!empty($cp)) {
						$corssInfoList[] =[
							'index' => $i,
							'point' => $cp,
						];
					}

				} else {
					$end = $addition->points[($i + 2) % $additionCount];

					// TODO:  曲線に対応
	//				$cp = self::getCrossPointEx($v, [$p, $next]);

					$cpList = self::getBezier2CrossPoint([$p, $next, $end], $v);
					if (!empty($cpList)) {
						foreach ($cpList as $cp) {
							$corssInfoList[] =[
								'index' => $i,
								'point' => [
									'x' => $cp['point']['x'],
									'y' => $cp['point']['y'],
									'length' => $cp['length'],
									'length2' => $cp['bezierLength'],
								],
							];
						}
					}
				}
			}
		} else {
			$v[] = $this->points[($index + 2) % $baseCount];

			for ($i = 0; $i < $additionCount; $i++) {
				$p = $addition->points[$i];
				if (!$p['isOnCurvePoint']) {
					continue;
				}
				$next = $addition->points[($i + 1) % $additionCount];
				if ($next['isOnCurvePoint']) {
					$cpList = self::getBezier2CrossPoint($v, [$p, $next]);
					if (!empty($cpList)) {
//dd($cpList);
						foreach ($cpList as $cp) {
							$corssInfoList[] =[
								'index' => $i,
								'point' => [
									'x' => $cp['point']['x'],
									'y' => $cp['point']['y'],
									'length' => $cp['bezierLength'],
									'length2' => $cp['length'],
								],
							];
						}
					}
				} else {
//echo '<h1>bezier-bezier</h1>';
					$end = $addition->points[($i + 2) % $additionCount];
$cpList = self::getBezier2CrossPointByBezier2($v, [$p, $next, $end]);
if (!empty($cpList)) {
	foreach ($cpList as $cp) {
//dd($cp);
		$corssInfoList[] =[
			'index' => $i,
			'point' => [
				'x' => $cp['x'],
				'y' => $cp['y'],
				'length' => $cp['length'],
				'length2' => $cp['length2'],
			],
		];
	}
}

//dd($cpList);


//
//					// TODO:  曲線に対応
//	//				$cp = self::getCrossPointEx($v, [$p, $next]);
//
//					$cpList = self::getBezier2CrossPoint([$p, $next, $end], $v);
//
//					if (!empty($cpList)) {
//						foreach ($cpList as $cp) {
//							$corssInfoList[] =[
//								'index' => $i,
//								'point' => [
//									'x' => $cp['point']['x'],
//									'y' => $cp['point']['y'],
//									'length' => $cp['bezierLength'],
//									'length2' => $cp['length'],
//								],
//							];
//						}
//					}
				}
			}
		}

		return $corssInfoList;
	}

	protected function insertSelfCorssPointToShape()
	{
		$pointList = $this->points;

		$pointsCount = count($pointList);
		$corssToList = array_fill(0, $pointsCount, []);


		$shapeCrossedPointList = [];
		foreach ($pointList as $p) {
			$shapeCrossedPointList[] = [
				'point' => $p,
				'crossedList' => [],
			];
		}

		for ($index = 0; $index < $pointsCount; $index++) {
			$crossPointBase = &$shapeCrossedPointList[$index];

			if (!$pointList[$index]['isOnCurvePoint']) {
				continue;
			}

			$crossInfo = $this->getSelfCrossInfoList($index, $corssToList[$index]);
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

//echo '<h1>交点あり</h1>';
//$s = new Shape($newShape);
//echo '<svg>'.$s->toSvg().'</svg>';

		return [
			'shape' => $newShape,
			'crossInfo' => $crossInfo,
		];
	}

	protected function getSelfCrossInfoList($index, $ignoreIndexList = [])
	{

		$shapePointList = $this->points;

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
						$end = $shapePointList[($otherIndex + 2) % $pointsCount];
$cpList = self::getBezier2CrossPointByBezier2($v, [$p, $next, $end]);
if (!empty($cpList)) {
	foreach ($cpList as $cp) {
		$crossPoints[] = [
			'point' => [
				'x' => $cp['x'],
				'y' => $cp['y'],
				'length' => $cp['length'],
				'length2' => $cp['length2'],
			],
			'index' => $otherIndex,
		];
	}
}

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

				if (($t0 >= 0) && ($t0 < 1.0)) {
					$tList[] = $t0;
				}
				if(($t1 >= 0) && ($t1 < 1.0)){
					$tList[] = $t1;
			    }
			} else if ($d == 0) {
				$t1 = 0.5 * -$n / $m;
				if(($t1 >= 0) && ($t1 < 1.0)){
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

			if (($length >= 0.0) && ($length < 1.0)) {
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

	public static function getCrossPointByInfLine($v1, $v2)
	{
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
//		if (($crossVectorLengthBase < 0)) {
//			return null;
//		}

		$crossed = [
			'x' => $v1[0]['x'] + (($v1[1]['x'] - $v1[0]['x']) * $crossVectorLengthBase),
			'y' => $v1[0]['y'] + (($v1[1]['y'] - $v1[0]['y']) * $crossVectorLengthBase),
			'length' => $crossVectorLengthBase,
		];

		return $crossed;
	}

	protected static function getLineLength($line, $point)
	{
		$vector = [
			'x' => $point['x'] - $line[0]['x'],
			'y' => $point['y'] - $line[0]['y'],
		];
		$ray = [
			$point,
			[
				'x' => ($vector['y']),
				'y' => -($vector['x']),
			]
		];

		$cross = self::getCrossPointByInfLine($line, $ray);
		return $cross['length'];
	}

	public static function crossProduct($v1, $v2)
	{
		return (
			($v1[1]['x'] - $v1[0]['x']) * ($v2[1]['y'] - $v2[0]['y']) -
			($v1[1]['y'] - $v1[0]['y']) * ($v2[1]['x'] - $v2[0]['x'])
		);
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

	public function isInsidePoint($point)
	{
		return self::isInsideShapePoint($this->points, $point);
	}

	protected static function isInsideShapePoint($shape, $point, $ignoreIndex = -1)
	{
		$v = [
			$point,
			['x' => $point['x'] + 10, 'y' => $point['y']],
		];

		$crossCount = 0;
		$pointCount = count($shape);
		$crossInfoLine = null;

		$index = 0;
		$loopCount = $pointCount;
		if ($ignoreIndex > -1) {
			$index = ($ignoreIndex + 1) % $pointCount;
			$loopCount = ($pointCount - 2);
		}

		for ($i = 0; $i < $loopCount; $i++) {
			$p = $shape[$index];
			$next = $shape[($index + 1) % $pointCount];

			$crossPoint = self::getCrossPointByRay($v, [$p, $next]);
			if (!is_null($crossPoint)) {
				if ($crossPoint['length'] > 0) {
					$crossCount++;
				}
			}

			$index = ($index + 1) % $pointCount;
		}

		if (($crossCount % 2) > 0) {
			return true;
		}

		return false;
	}

	protected static function getBezier2CrossPointByBezier2($b1, $b2)
	{
		$bezierList = [
			$b1,
			$b2,
		];

		$tangentials = [
			[$b1],
			[$b2],
		];

		$crossLengthList = [];
		for ($loopCounter = 0; $loopCounter < 16; $loopCounter++) {
			$isCrossedToTangential = false;
			for ($i = 0; $i < 2; $i++) {
				$crossLengthListToTangentials = [];
				foreach ($tangentials[1 - $i] as $tangentialList) {
					$length = self::getBezier2CrossLengthListByTangentialList($bezierList[$i], $tangentialList);
					$crossLengthListToTangentials = array_merge($crossLengthListToTangentials, $length);
				}
				sort($crossLengthListToTangentials);

				$count = count($crossLengthListToTangentials);
				if ($count >= 2) {
					if ($i == 0) {
						$oldCrossLengthList = $crossLengthListToTangentials;
						$crossLengthListToTangentials = [];
						$count = count($oldCrossLengthList);
						for ($ci = 0; $ci < $count; $ci += 2) {
							$diff =	 $oldCrossLengthList[$ci + 1] - $oldCrossLengthList[$ci];
							if ($diff < 0.05) {
								$s = self::getBezier2CurvePoint($bezierList[$i][0], $bezierList[$i][2], $bezierList[$i][1], $oldCrossLengthList[$ci]);
								$e = self::getBezier2CurvePoint($bezierList[$i][0], $bezierList[$i][2], $bezierList[$i][1], $oldCrossLengthList[$ci + 1]);
								$line = [$s, $e];

								$otehrCross = self::getBezier2CrossPoint($bezierList[1 - $i], $line);
								if (!empty($otehrCross)) {
									$crossLengthList[] = [
										$oldCrossLengthList[$ci] + ($diff / 2),
										$otehrCross[0]['bezierLength'],
									];
								}

							} else {
								$isCrossedToTangential = true;
								$crossLengthListToTangentials[] = $oldCrossLengthList[$ci];
								$crossLengthListToTangentials[] = $oldCrossLengthList[$ci + 1];
							}
						}
					}
				}


				if (false) {
					if (!empty($crossLengthListToTangentials)) {
						echo '<ul>';
						foreach ($crossLengthListToTangentials as $t) {
							echo "<li>{$t}</li>";
						}
						echo '</ul>';
					}

					echo '<svg width="600px" height="600px">';
					echo "<path d='M{$b1[0]['x']},{$b1[0]['y']} Q{$b1[1]['x']},{$b1[1]['y']} {$b1[2]['x']},{$b1[2]['y']}' stroke='black' fill=none />";
					echo "<path d='M{$b2[0]['x']},{$b2[0]['y']} Q{$b2[1]['x']},{$b2[1]['y']} {$b2[2]['x']},{$b2[2]['y']}' stroke='blue' fill=none />";
					if ($i != 0) {
						foreach ($tangentials[0] as $t) {
							echo "<path d='M{$t[0]['x']},{$t[0]['y']} {$t[1]['x']},{$t[1]['y']} {$t[2]['x']},{$t[2]['y']} z' stroke='#c0c0c0' fill=none />";
						}
					} else {
						foreach ($tangentials[1] as $t) {
							echo "<path d='M{$t[0]['x']},{$t[0]['y']} {$t[1]['x']},{$t[1]['y']} {$t[2]['x']},{$t[2]['y']} z' stroke='#c0c0ff' fill=none />";
						}
					}
					foreach ($crossLengthListToTangentials as $t) {
						$p = self::getBezier2CurvePoint($bezierList[$i][0], $bezierList[$i][2], $bezierList[$i][1], $t);
						echo "<circle cx='{$p['x']}' cy='{$p['y']}' r='3' fill='red'/>";
					}
					echo '</svg>';
				}


				$tangentials[$i] = [];
				$count = count($crossLengthListToTangentials);
				for ($ci = 0; $ci < $count; $ci += 2) {
					$s = $crossLengthListToTangentials[$ci];
					$e = $crossLengthListToTangentials[$ci + 1];
					$tangentials[$i][] = self::getBezier2SegmentTangentialLines($bezierList[$i], $s, $e);
				}


				if ($loopCounter > 0) {
					if (empty($tangentials[$i])) {
						break;
					}
				}

			}

			if (!$isCrossedToTangential) {
				break;
			}
		}

		$corossPoints = [];
		if (!empty($crossLengthList)) {
			foreach ($crossLengthList as $t) {
				$bezier = $bezierList[0];
				$point = self::getBezier2CurvePoint($bezier[0], $bezier[2], $bezier[1], $t[0]);
				$corossPoints[] = [
					'x' => $point['x'],
					'y' => $point['y'],
					'length' => $t[0],
					'length2' => $t[1],
				];
			}
		}
		return $corossPoints;
	}

	protected static function getBezier2SegmentTangentialLines($bezier, $s, $e)
	{
		$sp = self::getBezier2CurvePoint($bezier[0], $bezier[2], $bezier[1], $s);
		$ep = self::getBezier2CurvePoint($bezier[0], $bezier[2], $bezier[1], $e);
		$mp = self::getBezier2CurvePoint($bezier[0], $bezier[2], $bezier[1], $s + (($e - $s) / 2));


		$v1 = [
			'x' => ($bezier[1]['x'] - $bezier[0]['x']),
			'y' => ($bezier[1]['y'] - $bezier[0]['y']),
		];
		$v2 = [
			'x' => ($bezier[2]['x'] - $bezier[1]['x']),
			'y' => ($bezier[2]['y'] - $bezier[1]['y']),
		];


		$m = [
			'x' => $sp['x'] + (($ep['x'] - $sp['x']) / 2),
			'y' => $sp['y'] + (($ep['y'] - $sp['y']) / 2),
		];

		$v3 = [
			'x' => $m['x'] + ($mp['x'] - $m['x']) * 2.0,
			'y' => $m['y'] + ($mp['y'] - $m['y']) * 2.0,
		];

		return [
			$sp,
			$v3,
			$ep,
		];
	}

	protected static function getBezier2CrossLengthListByTangentialList($bezir, $tangentialList)
	{
		$count = count($tangentialList);
		$crossLengthList = [];
		for ($i = 0; $i < $count; $i++) {
			$line = [
				$tangentialList[$i],
				$tangentialList[($i + 1) % $count],
			];

			$crossInfoList = self::getBezier2CrossPoint($bezir, $line);
			if (!empty($crossInfoList)) {
				foreach ($crossInfoList as $c) {
					$crossLengthList[] = $c['bezierLength'];
				}
			}
		}

		if (empty($crossLengthList)) {
			return [];
		}

		if ((count($crossLengthList) % 2) != 0) {
			if (self::isInsideTriangle($tangentialList, $bezir[0])) {
				$crossLengthList[] = 0.0;
			} else {
				$crossLengthList[] = 1.0;
			}
		}

		return $crossLengthList;
	}

	protected static function isInsideTriangle($triangle, $point)
	{
		$count = count($triangle);
		for($i = 0; $i < $count; $i++) {
			$line = [
				$triangle[$i],
				$triangle[($i + 1) % $count],
			];
			$c = self::crossProduct($line, [$triangle[$i], $point]);
			if ($c > 0) {
				return false;
			}
		}
		return true;
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

	public function toSvg($isPointEnabled = true)
	{
		$svg = '<path d="';
		$svg .= $this->toSvgPath();
		$svg .= '" fill="'.'rgba(200, 200, 200, 0.4)'.'" stroke="#000000" stroke-width="1" />';

		if ($isPointEnabled) {
			foreach ($this->points as $index => $p) {
				$color = 'blue';
				if ($index == 0) {
					$color = 'red';
				} else if (!$p['isOnCurvePoint']) {
					$color = 'gray';
				}
				$svg .= "<circle cx='{$p['x']}' cy='{$p['y']}' r='2' fill='{$color}' data-index='{$index}' />";
			}
		}

		return $svg;
	}

	public function toSvgPath()
	{
		if (empty($this->points)) {
			return '';
		}

		$svg = 'M ';
		$curveCount = 0;
		$count = count($this->points);

		foreach ($this->points as $index => $p) {
			if (!$p['isOnCurvePoint']) {
				if ($curveCount == 0) {
					if ($index == 0) {
						$s = $this->points[$count - 1];
						$svg .= "{$s['x']},{$s['y']} ";
					}

					$svg .= "Q";
					$curveCount = 2;
				}
			} else {
				if ($index != 0) {
					if ($curveCount == 0) {
						$svg .= ' L';
					}
				}
			}

			if ($curveCount > 0) {
				$curveCount--;
			} else{
				if (!$p['isOnCurvePoint']) {
					// dd('ばぐったー!');
				}
			}
			$svg .= "{$p['x']},{$p['y']} ";
		}

		$p = $this->points[$count - 1];
		if (!$p['isOnCurvePoint']) {
			$p = $this->points[0];
			$svg .= "{$p['x']},{$p['y']} ";
		}

		$svg .= 'z';
//		$svg .= '" fill="'.'rgba(200, 200, 200, 0.4)'.'" stroke="#000000" stroke-width="1" />';


		return $svg;
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
}
