<?php

namespace FontObscure\Libs;
use FontObscure\Http\Controllers\TestController;


class Shape
{

	public $points = [];


	function __construct($points)
	{
		$this->points = /* self::sliceShape*/ ($points);
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

		$firstIndex = 0;
		while($firstIndex !== false) {
			$slicedPoints = [];
			$index = $firstIndex;
			for ($i = 0; $i < $pointsCount + 1; $i++) {
				$isPointPassedList[$index] = true;

				$p = $points[$index];
				$slicedPoints[] = $p;

				if ($infos[$index] > -1) {
					$index = $infos[$index];
				}

				$index = ($index + 1) % $pointsCount;
				if ($index == $firstIndex) {
					break;
				}
			}

			$slicedShapeList[] = new Shape($slicedPoints);
			$firstIndex = array_search(false, $isPointPassedList);
//break;
		}

		return $slicedShapeList;
	}

	public function compose($addition)
	{
//echo '<h1>composeXor</h1>';
//echo \FontObscure\Http\Controllers\TestController::testOutlineToSvg([$this->points]);
//echo \FontObscure\Http\Controllers\TestController::testOutlineToSvg([$addition->points]);
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

		$newShapeList = [];
		foreach ($shapeList as $shapeIndex => $shape) {

			$corssIndexInfo = $corssIndexInfoList[$shapeIndex];
			$count = count($shape->points);
			$passedIndexInfo = &$passedIndexInfoList[$shapeIndex];

			$otherPassedIndexInfo = &$passedIndexInfoList[1- $shapeIndex];
			$otherCorssIndexInfo = $corssIndexInfoList[1- $shapeIndex];
			$otherShape = $shapeList[1 - $shapeIndex];
			$otherCount = count($otherShape->points);

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
			unset($passedIndexInfo);
			unset($otehrPassedIndexInfo);
		}

//
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

	public function composeXor($addition)
	{
//echo '<h1>composeXor</h1>';
//echo \FontObscure\Http\Controllers\TestController::testOutlineToSvg([$this->points]);
//echo \FontObscure\Http\Controllers\TestController::testOutlineToSvg([$addition->points]);
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

//echo '<hr />new<br >';
//dump($corssIndexInfoList);
//foreach ($shapeList as $s) {
//	echo TestController::testOutlineToSvg([$s->points]);
//}


		$composed = [];
		foreach ($shapeList as $shapeIndex => $shape) {
			$newShapeList = [];

			$corssIndexInfo = $corssIndexInfoList[$shapeIndex];
			$count = count($shape->points);
//			$cross = $infoList[$shapeIndex];
//dd($cross);
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
			$otherCount = count($otherShape->points);

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

//echo '<h3>結果</h3>';
//foreach ($composed as $outline) {
//	$o = [];
//	foreach ($outline as $shape) {
//		$o[] = $shape->points;
//	}
//	echo TestController::testOutlineToSvg($o);
//}
//echo '<hr />';

		return $composed;
	}


	public function getShapeDirection()
	{
		$pointCount = count($this->points);

		$sum = 0;
		for ($i = 0; $i < $pointCount; $i++) {
			$v1 = [
				$this->points[$i],
				$this->points[($i + 1) % $pointCount],
			];
			$v2 = [
				$this->points[($i + 1) % $pointCount],
				$this->points[($i + 2) % $pointCount],
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

////					$end = $addition->points[($i + 2) % $additionCount];
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

//dd(compact('newShape', 'crossInfo'));

//echo '<h1>test</h1>';
//echo self::testOutlineToSvg([$newShape], true);
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

	public function toSvg($isPointEnabled = true)
	{
		$svg = '<path d="';
		$svg .= 'M ';
		$curveCount = 0;
		foreach ($this->points as $index => $p) {
			if (!$p['isOnCurvePoint']) {
				if ($curveCount == 0) {
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

//		$svg .= "{$this->points[0]['x']},{$this->points[0]['y']} ";
		$svg .= 'z';
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
}
