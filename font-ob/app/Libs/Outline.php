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



echo '<h3 >sliced..</h3>';
echo '<h4 >clock -------------------------------------------</h4>';
foreach ($clockwiseShapeList as $cws) {
	echo '<svg>';
	echo $cws->toSvg();
	echo '</svg>';
}
echo '<h4 >anti-clock --------------------------------------</h4>';
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


		echo '<hr />';
//		$newAnticlockwiseList = [];
//		foreach ($anticlockwiseShapeList as $anitclock) {
//
//echo '<svg>';
//echo $anitclock->toSvg();
//echo '</svg>';
//
//			$newAnticlockwiseList[] = $anitclock;
//
//		}

		///////////////
echo '<hr /><h1>合成</h1>';
		$list = $clockwiseShapeList;
		$aclist = [];

		$newClockwiseList = [];
		$newAnticlockwiseList = $anticlockwiseShapeList;
		while (!empty($clockwiseShapeList)) {
echo '<h4>$clockwiseShapeList!!!</h4>';
dump($clockwiseShapeList);
echo '<svg>';
foreach ($clockwiseShapeList as $c) {
echo $c->tosvg();
}
echo '</svg>';
echo '<hr />';
echo '<h4>$anticlockwiseShapeList!!!</h4>';
dump($anticlockwiseShapeList);
echo '<svg>';
foreach ($anticlockwiseShapeList as $c) {
echo $c->tosvg();
}
echo '</svg>';
echo '<hr />';

			$s = array_shift($clockwiseShapeList);
echo '<p><h1>$s=</h1><br />';
echo '<svg>'.$s->tosvg().'</svg>';
echo '</p>';

			$na = [];
			foreach ($newAnticlockwiseList as $a) {
				$composed = $a->compose($s);
				if (empty($composed)) {
					$na[] = $a;
				} else {
					$na = array_merge($na, $composed);
echo '<h4>first - $anticlockwiseShapeList!!!</h4>';
foreach ($composed as $c) {
	echo '<svg>'.$c->tosvg().'</svg>';
}
echo '<hr />';

				}
			}
			$newAnticlockwiseList = $na;

			if (!empty($clockwiseShapeList)) {
				$isComposed = false;
				$nc = [];
				foreach ($clockwiseShapeList as $index => $addition) {
echo '<p>$addition=<br />';
echo '<svg>'.$addition->tosvg().'</svg>';
echo '</p>';
					if (!$isComposed) {
						$composed = $s->compose($addition);
						if (empty($composed)) {
							$nc[] = $addition;
						} else {
echo '<h4>COMPOSED!!!</h4>';
foreach ($composed as $c) {
	echo '<svg>'.$c->tosvg().'</svg>';
}
echo '<hr />';

							$isComposed = true;

							$na = [];
							foreach ($newAnticlockwiseList as $s) {
								$composedAnticlock = $s->compose($addition);
								if (!empty($composedAnticlock)) {
echo '<h4>Anticlock-COMPOSED!!!</h4>';
foreach ($composedAnticlock as $c) {
	echo '<svg>'.$c->tosvg().'</svg>';
}
echo '<hr />';

									foreach ($composedAnticlock as $s) {
										if ($s->getShapeDirection() < 0) {
											$na[] = $s;
										}
									}
								} else {
									$na[] = $s;
								}
							}
							$newAnticlockwiseList = $na;
							foreach ($composed as $s) {
								if ($s->getShapeDirection() > 0) {
									$nc[] = $s;
								} else {
									$newAnticlockwiseList[] = $s;
								}
							}
						}

					} else {
						$nc[] = $addition;
					}
				}
				$clockwiseShapeList = $nc;

				if (!$isComposed) {
					$newClockwiseList[] = $s;
					$na = [];
					foreach ($newAnticlockwiseList as $ac) {
						$composedAnticlock = $ac->compose($s);
						if (!empty($composedAnticlock)) {
							foreach ($composedAnticlock as $ac) {
								if ($ac->getShapeDirection() < 0) {
									$na[] = $ac;
								}
							}
						} else {
							$na[] = $ac;
						}
					}
					$newAnticlockwiseList = $na;
				}
			} else {
				$newClockwiseList[] = $s;
			}
		}


echo '<hr />';
echo '<h4 >clock +++++++++++++++++++++++++++++++</h4>';
foreach ($newClockwiseList as $cws) {
	echo '<svg>';
	echo $cws->toSvg();
	echo '</svg>';
}
echo '<h4 >anti-clock +++++++++++++++++++++++++++++++</h4>';
foreach ($newAnticlockwiseList as $cws) {
	echo '<svg>';
	echo $cws->toSvg();
	echo '</svg>';
}
echo '<hr />';

		$this->shapes = array_merge($newClockwiseList, $newAnticlockwiseList);
		$outline = $this->removeLostedShape();

echo '<h4>shapes....</h4>';
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
		$directionList = $this->createDirectionList();
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

		return "<svg width='110'>{$svg}</svg>";
	}
}
