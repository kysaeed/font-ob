<?php


namespace FontObscure\Libs;

use FontObscure\Http\Controllers\TestController;


class Outline
{
	public $shapes = [];

	public function __construct($strokes)
	{
		$this->shapes = self::getOutlineFromStroke($strokes);
	}

	public function getShapes()
	{
		return $this->shapes;
	}


	public static function getOutlineFromStroke($strokes)
	{
		$shapeList = self::strokeToShapeList($strokes);

		$slicedShapeOutlineList = [];
		foreach ($shapeList as $shape) {
			$slicedShapeOutlineList = array_merge($slicedShapeOutlineList, $shape->slice());
		}

		$clockwiseShapeList = [];
		$anticlockwiseShapeList = [];
		foreach ($slicedShapeOutlineList as $shape) {
			$direction = $shape->getShapeDirection();
			if ($direction > 0) {
				$clockwiseShapeList[] = $shape;
			} else if ($direction < 0) {
				$anticlockwiseShapeList[] = $shape;
			}
		}

		$_nextClockwise = [];
		foreach ($clockwiseShapeList as $base) {
			$_nextAniticock = [];

			if (!empty($anticlockwiseShapeList)) {
				foreach ($anticlockwiseShapeList as $addition) {

					$composed = $base->composeXor($addition);
					if (!is_null($composed)) {
						$isComposed = true;
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

		// 時計回りシェイプの合成
		$_next = [];
		while (!empty($clockwiseShapeList)) {
			$c = array_shift($clockwiseShapeList);

			$isComposed = false;
			$_nextNext = [];
			foreach ($_next as $c2) {
				if (!$isComposed) {
					$composed = $c->composeXor($c2);
					if (!is_null($composed)) {
						$composed = $composed['base'][0];
					}

					if (!is_null($composed)) {
						$isComposed = true;
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
		}
		$clockwiseShapeList = $_next;

		$outline = array_merge($clockwiseShapeList, $anticlockwiseShapeList);
		$outline = self::removeLostedShape($outline);

		return $outline;
	}

	protected static function strokeToShapeList($strokes)
	{
		$outline = [];
		$thickness = 4; // 太さ
		foreach ($strokes as $index => $line) {
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
			$outline[] = new Shape($shape);
		}

		return $outline;
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
		foreach ($shapeList as $lineIndex => $shape) {
			$directionList[] = $shape->getShapeDirection();
		}
		return $directionList;
	}

	protected static function getOutsideShapeIndex($outline, $insideShapeIndex)
	{
		$lineList = $outline[$insideShapeIndex]->getPoints();
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
				$pointCount = count($line->points);
				$crossInfoLine = null;
// echo "{$insideShapeIndex}→{$i}<br />";

				for ($index = 0; $index < $pointCount; $index++) {
					$p = $line->points[$index];
					$next = $line->points[($index + 1) % $pointCount];
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
