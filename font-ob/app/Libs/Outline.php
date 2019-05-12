<?php


namespace FontObscure\Libs;

use FontObscure\Http\Controllers\TestController;


class Outline
{
	public $shapes = [];

	public function __construct($strokes)
	{
		$this->shapes = $this->getOutlineFromStroke($strokes);
	}

	public function getOutlineFromStroke($strokes)
	{
echo '<h1>getOutlineFromStroke</h1>';
		$shapeList = self::strokeToShapeList($strokes);

echo '<h4 >$shapeList..</h4>';
foreach ($shapeList as $s) {
	echo '<svg>';
	echo $s->toSvg();
	echo '</svg>';
}

		$slicedShapeOutlineList = [];
		foreach ($shapeList as $shape) {
			$slicedShapeOutlineList = array_merge($slicedShapeOutlineList, $shape->slice());
		}

echo '<h4 >sliced..</h4>';
foreach ($slicedShapeOutlineList as $s) {
	echo '<svg>';
	echo $s->toSvg();
	echo '</svg>';
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
						$_nextClockwise = array_merge($_nextClockwise, $composed[0]);
						$_nextAniticock = array_merge($_nextAniticock, $composed[1]);
					} else {
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

echo '<hr />';
echo '<h4 >clock +++++++++++++++++++++++++++++++</h4>';
foreach ($clockwiseShapeList as $cws) {
	echo '<svg>';
	echo $cws->toSvg();
	echo '</svg>';
}
echo '<h4 >anti-clock +++++++++++++++++++++++++++++++</h4>';
foreach ($anticlockwiseShapeList as $cws) {
	echo '<svg>';
	echo $cws->toSvg();
	echo '</svg>';
}
echo '<hr />';
echo '<svg>';
foreach ($clockwiseShapeList as $cws) {
	echo $cws->toSvg();
}
foreach ($anticlockwiseShapeList as $cws) {
	echo $cws->toSvg();
}
echo '</svg>';
		// 時計回りシェイプの合成
		$_next = [];
		while (!empty($clockwiseShapeList)) {
			$c = array_shift($clockwiseShapeList);

			$isComposed = false;
			$_nextNext = [];
			foreach ($_next as $c2) {
				if (!$isComposed) {
					$composed = $c->compose($c2);
					if (!empty($composed)) {

echo '<h4>$composed</h4>';
foreach ($composed as $cws) {

	echo '<svg>';
	echo $cws->toSvg();
	echo '</svg>';
}
echo '<hr />';

						$isComposed = true;
						$clockwiseShapeList[] = $composed[0]; // TODO: [0]を一番外側に修正する

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

echo '<h4>clock</h4>';
foreach ($clockwiseShapeList as $cws) {
	echo '<svg>';
	echo $cws->toSvg();
	echo '</svg>';
}
echo '<hr />';


		$this->shapes = array_merge($clockwiseShapeList, $anticlockwiseShapeList);
		$outline = $this->removeLostedShape();

echo '<h4>shapes....</h4>';
//dd($outline);
foreach ($outline as $cws) {
	echo '<svg>';
	echo $cws->toSvg();
	echo '</svg>';
}
echo '<hr />';

		return $outline;
	}

	protected static function strokeToShapeList($strokes)
	{
		$outline = [];
		foreach ($strokes as $index => $line) {
			$outline[] = Shape::createFromStroke($line);
		}
		return $outline;
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

	protected function createDirectionList()
	{
		$directionList = [];
		foreach ($this->shapes as $shape) {
			$directionList[] = $shape->getShapeDirection();
		}
		return $directionList;
	}

	protected function removeLostedShape()
	{
		$directionList = self::createDirectionList();
		$aliveShapes = [];
		foreach ($this->shapes as $i => $shape) {
			$outsideIndex = $this->getOutsideShapeIndex($i);
			if ($outsideIndex > -1) {
				if ($directionList[$i] == $directionList[$outsideIndex]) {
					continue;
				}
			}

			$aliveShapes[] = $shape;
		}

		return $aliveShapes;
	}

	protected function getOutsideShapeIndex($insideShapeIndex)
	{
//		$outline = $this->shapes;

		$lineList = $this->shapes[$insideShapeIndex]->points;
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
		foreach ($this->shapes as $i => $shape) {
			if ($i != $insideShapeIndex) {

				$crossCount = 0;
				$pointCount = count($shape->points);
				$crossInfoLine = null;
// echo "{$insideShapeIndex}→{$i}<br />";

				for ($index = 0; $index < $pointCount; $index++) {
					$p = $shape->points[$index];
					$next = $shape->points[($index + 1) % $pointCount];

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

		if (is_null($crossInfo)) {
			return -1;
		}

		return $crossInfo['shapeIndex'];
	}

	public function toSvg($isPointEnabled = false)
	{
		$svg = '<path d="';
		foreach ($this->shapes as $shape) {
			$svg .= $shape->toSvgPath($isPointEnabled);
			$svg .= ' ';
		}
		$svg .= '" fill="'.'rgba(200, 200, 200, 0.4)'.'" stroke="#000000" stroke-width="1" />';

		return "<svg width='100'>{$svg}</svg>";
	}
}
