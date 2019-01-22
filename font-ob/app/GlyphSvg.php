<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

use FontObscure\TtfGlyph;

class GlyphSvg extends Model
{
	protected $glyph = null;
	protected $hmtx = null;
	const ON_CURVE_POINT = 0x01;

    public function __construct($glyph, $hmtx)
	{
		$this->glyph = $glyph;
		$this->hmtx = $hmtx;
	}

	public function getSvg()
	{
		$glyph = $this->glyph;
		$hmtx = $this->hmtx;

if (!$glyph) {
	return '';
}

		$testCurves = [];
		$testPoints = [];

		$sizeBase = 10;

		$lsb = $hmtx->lsb / $sizeBase;
		$width = $hmtx->advance_width / $sizeBase;

		$ON_CURVE_POINT = (0x01 << 0);

		$h = 2800 / $sizeBase;
		$svg = '<svg width="'.($width + $lsb).'px" height="'.$h.'px">';

		$coordinates = $glyph['coordinates'];

		$svg .= '<path d="';
		foreach ($coordinates as $indexEndPonts => $contours) {
			$isCurve = false;
			$curvePoints = [];
			$maxIndexContours = count($contours) - 1;
			foreach ($contours as $index => $c) {
				$x = $c['x'] / $sizeBase;
				$y = -$c['y'] / $sizeBase;
				$x += $lsb;
				$y += (2000 / $sizeBase);
				$testPoints[] = ['x' => $x, 'y' => $y, 'isCurve'=>(bool)$isCurve];

				if ($isCurve) {
					if ($c['flags'] & self::ON_CURVE_POINT) {
						$svg .= $this->getCurvePathSvg($curvePoints, ['x'=>$x, 'y'=>$y]);
						$nextFlags = $contours[($index + 1) % ($maxIndexContours + 1)]['flags'];
						if (!($nextFlags & self::ON_CURVE_POINT)) {
							$isCurve = true;
							$curvePoints = [];
						} else {
							$isCurve = false;
							$curvePoints = [];
						}

					} else if (count($curvePoints) >= 1) {
						$diffX = $x - $curvePoints['x'];
						$diffY = $y - $curvePoints['y'];

						$middleX = $curvePoints['x'] + ($diffX / 2);
						$middleY = $curvePoints['y'] + ($diffY / 2);

						$svg .= $this->getCurvePathSvg($curvePoints, ['x'=>$middleX, 'y'=>$middleY]);

						$curvePoints = [
							'x' => $x,
							'y' => $y,
						];
						$testCurves[] = [
							'x' => $x,
							'y' => $y,
						];

					} else {
						$curvePoints = [
							'x' => $x,
							'y' => $y,
						];
						$testCurves[] = [
							'x' => $x,
							'y' => $y,
						];
					}
				} else {
					if (($index == 0)) {
						$cmd = 'M';

						if (!($c['flags'] & self::ON_CURVE_POINT)) {
							if ($maxIndexContours < 1) {
								dd('$maxIndexContours Error');
							}
							$nextCoodinate = $contours[1];

							$sX = $x;
							$sY = $y;

							// $eX = $nextCoodinate['x'];
							// $eY = $nextCoodinate['y'];

							$eX = ($nextCoodinate['x'] / $sizeBase);
							$eY = -($nextCoodinate['y'] / $sizeBase);

							$eX += $lsb;
							$eY += (2000 / $sizeBase);

							$diffX = $eX - $x;
							$diffY = $eY - $y;

							$middleX = $x + ($diffX / 2);
							$middleY = $y + ($diffY / 2);

							$x = $middleX;
							$y = $middleY;

							$curvePoints = [];
							$isCurve = true;
						} else {
							$nextFlags = $contours[($index + 1) % ($maxIndexContours + 1)]['flags'];
							if (!($nextFlags & $ON_CURVE_POINT)) {
								$isCurve = true;	// @@カーブ開始
								$curvePoints = [];
							}
						}
						$svg .= "{$cmd} {$x},{$y} ";
					} else {
						$cmd = 'L';
						$nextFlags = $contours[($index + 1) % ($maxIndexContours + 1)]['flags'];
						if (!($nextFlags & $ON_CURVE_POINT)) {
							$isCurve = true;	// @@カーブ開始
							$curvePoints = [];
						}
						$svg .= "{$cmd} {$x},{$y} ";
					}
				}

				$index++;
			}

			// 閉じパス
			if ($isCurve) {
				$startCoodinate = $contours[0];
				if ($contours[0]['flags'] & self::ON_CURVE_POINT) {
					// $startCoodinate = $contours[0];
					$x = ($contours[0]['x'] / $sizeBase);
					$y = -($contours[0]['y'] / $sizeBase);
					$x += $lsb;
					$y += 2000 / $sizeBase;
// $x += 60;
// $curvePoints['x'] += 60;
					$svg .= "Q {$curvePoints['x']},{$curvePoints['y']} {$x},{$y} ";

				} else {
					if ($maxIndexContours < 1) {
						dd('oioi');
					}

// dump($curvePoints);
					if (count($curvePoints) >= 1) {
						// NOTE: curvePointを通って から 始点と終点の中点までを引く！
						$startPoint = $this->getMiddlePoint($contours[$maxIndexContours], $contours[0]);
						$startPoint['x'] = $lsb + ($startPoint['x'] / $sizeBase);
						$startPoint['y'] = -($startPoint['y'] / $sizeBase) + (2000 / $sizeBase);


						$svg .= "Q {$curvePoints['x']},{$curvePoints['y']} {$startPoint['x']},{$startPoint['y']} ";



						// NOTE: 終点を通って、始点と２番めの中点まで！
						$startPoint = $this->getMiddlePoint($contours[0], $contours[1]);
						$startPoint['x'] = $lsb + ($startPoint['x'] / $sizeBase);
						$startPoint['y'] = -($startPoint['y'] / $sizeBase) + (2000 / $sizeBase);


						$x = $contours[0]['x'] / $sizeBase;
						$y = -$contours[0]['y'] / $sizeBase;
						$x += $lsb;
						$y += (2000 / $sizeBase);

						$svg .= "Q {$x},{$y} {$startPoint['x']},{$startPoint['y']} ";

// $svg .= "L {$x},{$y} ";

						$curvePoints = [];
					}
				}
			}
			$svg .= 'z ';
		}
		$svg .= '" fill="#e0e0e0" stroke="black" stroke-width="1" />';


		// foreach ($testCurves as $i => $tc) {
		// 	$color = 'blue';
		// 	if ($i == 0) {
		// 		$color = 'red';
		// 	}
		// 	if ($i == 1) {
		// 		$color = 'red';
		// 	}
		// 	$svg .= "<circle id='{$i}' cx='{$tc['x']}' cy='{$tc['y']}' r='2' fill='{$color}' stroke='none' stroke-width='1'/>";
		// }

		if (false) {
			foreach ($testPoints as $i => $tc) {
				$color = 'blue';
				if ($tc['isCurve']) {
					$color = 'blue';
				} else {
					$color = 'red';
				}
				$svg .= "<circle id='{$i}' cx='{$tc['x']}' cy='{$tc['y']}' r='1' fill='{$color}' stroke='red' stroke-width='1'/>";
			}
		}


		$svg .= '</svg>';

		return $svg;
	}

	protected function getLinePathSvg($end)
	{
		return "L {$end['x']},{$end['y']} ";
	}

	protected function getCurvePathSvg($curve, $end)
	{
		return "Q {$curve['x']},{$curve['y']} {$end['x']},{$end['y']} ";
	}

	protected function toLocalCoordinate($coordinate)
	{
		$sizeBase = 10;

		return [
			'x' => ($coordinate['x'] + $this->hmtx['lsb']) / $sizeBase,
			'y' => -($coordinate['y'] / $sizeBase) + (2000 / $sizeBase),
			'flags' => $coordinate['flags'],
		];
	}

	protected function getMiddlePoint($startCoordinate, $endCoordinate)
	{
		$paramList = ['x', 'y'];
		$middlePoint = [];
		foreach ($paramList as $p) {
			$diff = $startCoordinate[$p] - $endCoordinate[$p];
			$middlePoint[$p] = $endCoordinate[$p] + ($diff / 2);

		}
		return $middlePoint;
	}

}
