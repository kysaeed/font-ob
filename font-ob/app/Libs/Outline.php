<?php


namespace FontObscure\Libs;

use FontObscure\Http\Controllers\TestController;


class Outline
{
	public function __construct()
	{
		;
	}

	public static function getOutlineFromStroke($stroke)
	{
//echo '<hr />';
//echo '<h2>getOutlineFromStroke</h2>';
		$outline = [];

		$shapeList = self::strokeToShapeList($stroke);

echo '<hr />交差あり<br />';
echo TestController::testOutlineToSvg($shapeList, false);
//dump($shapeList);

//		$slicedShapeOutlineList = self::getNonCrossingOutline($shapeList);
		$slicedShapeOutlineList = ($shapeList);

//echo '<hr /><h2>sliced</h2>';
//echo TestController::testOutlineToSvg($slicedShapeOutlineList);
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

		echo TestController::testOutlineToSvg($clockwiseShapeList);
		echo TestController::testOutlineToSvg($anticlockwiseShapeList);
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
//echo
echo 'composed....<br />';
echo TestController::testOutlineToSvg($composed['base']);
echo TestController::testOutlineToSvg($composed['addition']);
echo '<br />';
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
echo TestController::testOutlineToSvg($clockwiseShapeList);
echo TestController::testOutlineToSvg($anticlockwiseShapeList);
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

// echo '<hr />'.TestController::testOutlineToSvg($_next).'<hr />';

		}
		$clockwiseShapeList = $_next;

// echo TestController::testOutlineToSvg($clockwiseShapeList).'<hr />';



		$outline = array_merge($clockwiseShapeList, $anticlockwiseShapeList);
// echo TestController::testOutlineToSvg($outline);

		$outline = self::removeLostedShape($outline);
		return $outline;
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
//echo TestController::testOutlineToSvg([$base]);
//echo TestController::testOutlineToSvg([$addition]);
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
//	echo TestController::testOutlineToSvg([$s], true, $crossPointList);
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

	public static function crossProduct($v1, $v2)
	{
		return (
			($v1[1]['x'] - $v1[0]['x']) * ($v2[1]['y'] - $v2[0]['y']) -
			($v1[1]['y'] - $v1[0]['y']) * ($v2[1]['x'] - $v2[0]['x'])
		);
	}

	protected static function getNonCrossingOutline($crossingOutline)
	{
		$outline = [];
		foreach ($crossingOutline as $shape) {
			$outline = array_merge($outline, self::sliceShape($shape));
		}
		return $outline;
	}

	protected static function createDirectionList($shapeList)
	{
		$directionList = [];
		foreach ($shapeList as $lineIndex => $line) {
			$directionList[] = self::getShapeDirection($line);
		}
		return $directionList;
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

}
