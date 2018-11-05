<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Storage;


class TestController extends Controller
{
	public function test(Request $request)
    {
		// font
		// mplus-1c-light

        $file = Storage::disk('local')->get('strokes/mplus-1c-light.ttf');
		// $file = Storage::disk('local')->get('strokes/font.ttf');

        $header = unpack('Nver/nnum/nrange/nselector/nshift', $file);
		$tableRecords = [];
        $readOffset = 12;
        for ($i = 0; $i < $header['num']; $i++) {
            $tag = substr($file, $readOffset, 4);
            $tableRecordData = substr($file, ($readOffset + 4), (16 - 4));
            $tableRecords[$tag] = unpack('Nsum/Noffset/Nlength', $tableRecordData);
            $readOffset += 16;
        }
		// echo '<hr />';
		// dump('header');
		// dump($header);
		// echo '<hr />';


		$binHead = $this->readTableBody($file, $tableRecords['head']);
		$head = unpack('nmajorVersion/nminorVersion/NfontRevision/NcheckSumAdjustment/NmagicNumber/nflags/nunitsPerEm/Jcreated/Jmodified/nxMin/nyMin/nxMax/nyMax/nmacStyle/nlowestRecPPEM/nfontDirectionHint/nindexToLocFormat/nglyphDataFormat', $binHead);
// dd($head);

		$binCmap = $this->readTableBody($file, $tableRecords['cmap']);
		$cmapHeader = unpack('nvar/nnumTables', $binCmap);
		$cmapTableCount = $cmapHeader['numTables'];

		$cmaps = [];
		for ($i = 0; $i < $cmapTableCount; $i++) {
			$binEncordingRecord = substr($binCmap, 4 + $i * 8, 8);
			$encodingRecord = unpack('nplatformID/nencodingID/Noffset', $binEncordingRecord);
			$cmaps[] = $this->dumpCmapSubTable($encodingRecord, $binCmap);
		}
// dd($cmaps[0]);

        // foreach ($tableRecords as $key => $t) {
        //     $test = substr($f, $t['offset'], $t['length']);
        //     $mySum = $this->calculateCheckSum($test);
		// 	// dump( sprintf('calculated-sum=%08x , org-sum=%08x', $mySum, $t['sum']) );
        // }


		$binMaxp = $this->readTableBody($file, $tableRecords['maxp']);
		$maxList = unpack('Nver/nnumGlyphs/nmaxPoints', $binMaxp);



		$binLoca = $this->readTableBody($file, $tableRecords['loca']);
		$locaCount = $maxList['numGlyphs'] + 1;

		if (!$head['indexToLocFormat']) {
			$locaFormat = "n{$locaCount}";
		} else {
			$locaFormat = "N{$locaCount}";
		}
		$locaList = array_values(unpack($locaFormat, $binLoca));


		$glyf = $tableRecords['glyf'];
		$g = substr($file, $glyf['offset'], $glyf['length']);
		// $glyph



		/////////////////////////////////

		$charCodeList = [
			ord('M'),
			ord('A'),
			ord('Y'),
			ord('A'),
			//
			ord('-'),

			ord('m'),
			ord('a'),
			ord('y'),
			ord('a'),

			ord('-'),
			//
			ord('a'),
			ord('b'),
			ord('c'),
			ord('d'),
			ord('e'),
			ord('f'),
			ord('g'),
			ord('h'),
			ord('s'),
			ord('i'),
			ord('m'),
			ord('w'),
			ord('o'),

			ord('x'),
			ord('y'),
			ord('z'),

			ord('A'),
			ord('W'),
			ord('S'),
			ord('O'),
			ord('Q'),
		];


		$binGlyphsData = $this->readTableBody($file, $tableRecords['glyf']);

		$map = $cmaps[0];
		foreach ($charCodeList as $i => $charCode) {
			$glyphIndex = 0;
			$index = 0;
			foreach ($map as $m) {
				if ($m['endCount'] >= $charCode) {
					if ($m['startCount'] <= $charCode) {

						// TODO: set offset to glyf id array !

						$glyphIndex = $charCode + $m['idDelta'];

						break;
					}
				}
			}

// $glyphIndex = $i + 10;
// dd($locaList);

			$binHhea = $this->readTableBody($file, $tableRecords['hhea']);
			$horizontalHeaderTable = $this->dumpHorizontalHeaderTable($binHhea);


			$binHmtx = $this->readTableBody($file, $tableRecords['hmtx']);
			$HorizontalMetrixList = $this->dumpHorizontalMetrix($horizontalHeaderTable, $binHmtx);

			$hm = $HorizontalMetrixList[$glyphIndex];



			$glyphOffset = $locaList[$glyphIndex];
			if (!$head['indexToLocFormat']) {
				$glyphOffset *= 2;
			}

			$binGlyph = substr($binGlyphsData, $glyphOffset);
			$glyfData = $this->dumpGlyph($binGlyph);
// dump($g);

			$svg = $this->glyphToSvg($glyfData, $hm);
			echo $svg;

		}

		echo '<hr />';

// dd($glyphIndex);


echo 'hello !';die;

		return '';
    }

	protected function readTableBody($binFile, $tableInfo)
	{
		$binBody = substr($binFile, $tableInfo['offset'], $tableInfo['length']);

		// TODO: チェックサム

		return $binBody;
	}

	protected function dumpCmapSubTable($encodingRecord, $binCmap)
	{
		$binSub = substr($binCmap, $encodingRecord['offset'], 2);
		$subFormat = unpack('nformat', $binSub);
// dump('format = '.$subFormat['format']);

		if ($subFormat['format'] == 0x00) {
			$binSubTable = substr($binCmap, $encodingRecord['offset']);
			$subHeader = unpack('nformat/nlength/nlanguage', $binSubTable);
			$binSubTable = substr($binSubTable, 6);

			$binSubTableBody = array_values(unpack('C256charcode', $binSubTable));	// NOTE: table char-code -> glyf-index
			return $binSubTableBody;
		}

		if ($subFormat['format'] == 0x04) {
			$binSubHeader = substr($binCmap, $encodingRecord['offset'], 14);
			$subHeader = unpack('nformat/nlength/nlanguage/nsegCountX2/nsearchRange/nentrySelector/nrangeShift', $binSubHeader);
			$count = $subHeader['segCountX2'] / 2;
			// ArrayAccess

			$binSubTableBody = substr($binCmap, $encodingRecord['offset'] + 14);

			$endCountList = array_values(unpack("n{$count}", $binSubTableBody));
			$binSubTableBody = substr($binSubTableBody, $count * 2 + 2); // add reserved pad 2bytes

			$startCountList = array_values(unpack("n{$count}", $binSubTableBody));
			$binSubTableBody = substr($binSubTableBody, $count * 2);

			$idDeltaList = array_values(unpack("n{$count}", $binSubTableBody));
			$binSubTableBody = substr($binSubTableBody, $count * 2);
			foreach ($idDeltaList as &$idDelta) {
				if ($idDelta > 0x7fff) {
					$idDelta = -(0x8000 - ($idDelta & 0x7fff));
				}
			}
			unset($idDelta);

			$idRangeOffsetList = array_values(unpack("n{$count}", $binSubTableBody));
			// $binSubTableBody = substr($binSubTableBody, $count * 2);

			$subTableBody = [];
			for ($i = 0; $i < $count; $i++) {
				$subTableBody[] = [
					'startCount' => $startCountList[$i],
					'endCount' => $endCountList[$i],
					'idDelta' => $idDeltaList[$i],
					'idRangeOffsetList' => $idRangeOffsetList[$i],
				];
			}

			return $subTableBody;
		}

		return null;
	}

	protected function dumpGlyph($binGlyph)
	{
		$glyphHeader = unpack('nnumberOfContours/nxMin/nyMin/nxMax/nyMax', $binGlyph);
		$binGlyph = substr($binGlyph, 10);
		foreach ($glyphHeader as &$param) {
			if ($param >= 0x7fff) {
				$param = -(0x8000 - ($param & 0x7fff));
			}
		}
		unset($param);

		$endPtsOfContoursList = array_values(unpack("n{$glyphHeader['numberOfContours']}", $binGlyph));
		$binGlyph = substr($binGlyph, 2 * $glyphHeader['numberOfContours']);

// dump($glyphHeader['numberOfContours']);
if ($glyphHeader['numberOfContours'] < 0) {
	return null;
}

		$instructionLength = unpack("n{$glyphHeader['numberOfContours']}", $binGlyph)[1];
		$binGlyph = substr($binGlyph, 2);

		// $instructions = substr($binGlyph, 0, $instructionLength);
		$instructions = array_values(unpack("C{$instructionLength}", $binGlyph));
		$binGlyph = substr($binGlyph, $instructionLength);


		// TODO: 定数を定義
		$ON_CURVE_POINT = (0x01 << 0);
		$X_SHORT_VECTOR = (0x01 << 1);
		$Y_SHORT_VECTOR = (0x01 << 2);
		$REPEAT_FLAG = (0x01 << 3);
		$X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR = (0x01 << 4);
		$Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR = (0x01 << 5);
		$OVERLAP_SIMPLE = (0x01 << 6);

		$pointCount = max($endPtsOfContoursList) + 1;
		$flagsList = [];

		$index = 0;
		while (count($flagsList) < $pointCount) {
			// TODO: repeatがあるのでなおす
			$flags = unpack('C',substr($binGlyph, $index, 1))[1];
			$flagsList[] = $flags;
			if ($flags & $REPEAT_FLAG) {
				$index++;
				$repeatCount = unpack('C',substr($binGlyph, $index, 1))[1];
// dump('repeat '.$repeatCount.' times');
				for ($j = 0; $j < $repeatCount; $j++) {
					// if ($j > 0) {
					// 	$flags |= $ON_CURVE_POINT;
					// }
					$flagsList[] = $flags;
				}
			}
			$index++;
		}

		$binGlyph = substr($binGlyph, $index);	// NOTE: $pointCount進めるのが謎


		$xCoordinatesList = [];
		$x = 0;
		foreach ($flagsList as $index => $flags) {
			if ($flags & $X_SHORT_VECTOR) {
				$xCoordinate = unpack('C', $binGlyph)[1];
				$binGlyph = substr($binGlyph, 1);
				if (!($flags & $X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR)) {
					$xCoordinate = -$xCoordinate;
				}
			} else {
				if (!($flags & $X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR)) {
					$xCoordinate = unpack('n', $binGlyph)[1];
					$binGlyph = substr($binGlyph, 2);
					if ($xCoordinate > 0x7fff) {
						$xCoordinate = -(0x8000 - ($xCoordinate & 0x7fff));
					}
				} else {
					$xCoordinate = 0;
				}
			}

			$x += $xCoordinate;
			$xCoordinatesList[] = $x;
		}

		$yCoordinatesList = [];
		$y = 0;
		foreach ($flagsList as $index => $flags) {
			if ($flags & $Y_SHORT_VECTOR) {
				$yCoordinate = unpack('C', $binGlyph)[1];
				$binGlyph = substr($binGlyph, 1);
				if (!($flags & $Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR)) {
					$yCoordinate = -$yCoordinate;
				}
			} else {
				if (!($flags & $Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR)) {
					$yCoordinate = unpack('n', $binGlyph)[1];
					$binGlyph = substr($binGlyph, 2);
					if ($yCoordinate > 0x7fff) {
						$yCoordinate = -(0x8000 - ($yCoordinate & 0x7fff));
					}
				} else {
					$yCoordinate = 0;
				}
			}
			$y += $yCoordinate;
			$yCoordinatesList[] = $y;
		}


		// $endPtsOfContoursList   <= ソートする
		$glyphCoordinatesList = [];
		$contours = [];
		$endPoint = $endPtsOfContoursList[0];
		foreach ($flagsList as $index => $flags) {
			$contours[] = [
				'x' => $xCoordinatesList[$index],
				'y' => $yCoordinatesList[$index],
				'flags' => $flags,
			];

			// $glyphCoordinatesList[] = [
			// 	'x' => $xCoordinatesList[$index],
			// 	'y' => $yCoordinatesList[$index],
			// 	'flags' => $flags,
			// ];


			if ($index >= $endPoint) {
				$glyphCoordinatesList[] = $contours;
				$contours = [];
				$endPointIndex = count($glyphCoordinatesList);
				if ($endPointIndex >= count($endPtsOfContoursList)) {
					break;
				}
				$endPoint = $endPtsOfContoursList[$endPointIndex];
			}
		}
		return  [
			'header' => $glyphHeader,
			'endPtsOfContours' => $endPtsOfContoursList,
			'instructions' => $instructions,
			'coordinates' => $glyphCoordinatesList
		];
	}

	protected function glyphToSvg($glyph, $hmtx)
	{
if (!$glyph) {
	return '';
}
		$testCurves = [];
		$testPoints = [];

		$sizeBase = 10;

		$lsb = $hmtx['lsb'] / $sizeBase;
		$width = $hmtx['advanceWidth'] / $sizeBase;

		$ON_CURVE_POINT = (0x01 << 0);

		$h = 2800 / $sizeBase;
		$svg = '<svg width="'.($width + $lsb).'px" height="'.$h.'px">';

		$endPoints = $glyph['endPtsOfContours'];
		$coordinates = $glyph['coordinates'];

		$svg .= '<path d="';
		foreach ($coordinates as $indexEndPonts => $contours) {
			$isCurve = false;
			$curvePoints = [];
			$maxIndexContours = count($contours) - 1;
// dump($contours);
			foreach ($contours as $index => $c) {
// dump("contours-index: {$index}");
				$x = $c['x'] / $sizeBase;
				$y = -$c['y'] / $sizeBase;
				$x += $lsb;
				$y += (2000 / $sizeBase);

$testPoints[] = ['x' => $x, 'y' => $y];


				if ($isCurve) {
					if ($c['flags'] & $ON_CURVE_POINT) {

						$svg .= "Q ";
						foreach ($curvePoints as $point) {
							$svg .= "{$point['x']},{$point['y']} ";
						}
						$svg .= "{$x},{$y} ";

						$nextFlags = $contours[($index + 1) % ($maxIndexContours + 1)]['flags'];
// dump(compact('nextFlags'));
						if (!($nextFlags & $ON_CURVE_POINT)) {
							$isCurve = true;
							$curvePoints = [];
// dump('curve is continued !');
						} else {
// dump('curve is end..............	');
							$isCurve = false;
							$curvePoints = [];
						}

					} else if (count($curvePoints) >= 1) {
						$diffX = $x - $curvePoints[0]['x'];
						$diffY = $y - $curvePoints[0]['y'];

						$middleX = $curvePoints[0]['x'] + ($diffX / 2);
						$middleY = $curvePoints[0]['y'] + ($diffY / 2);
						$svg .= "Q {$curvePoints[0]['x']},{$curvePoints[0]['y']} {$middleX},{$middleY} ";

// dump('add curve [1] index='.$index);
						$curvePoints = [
							[
								'x' => $x,
								'y' => $y,
							]
						];
						$testCurves[] = [
							'x' => $x,
							'y' => $y,
						];

					} else {
// dump('add curve [2]');
						$curvePoints[] = [
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

						if (!($c['flags'] & 0x01)) {
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

// dump(compact(
// 	'sX',
// 	'eX',
// 	'sY',
// 	'eY',
// 	'middleX',
// 	'middleY'
// ));
// // $y += 50;
							$curvePoints = [
								// [
								// 	'x' => $x,
								// 	'y' => $y,
								// ],
							];
							// $testCurves[] = [
							// 	'x' => $x,
							// 	'y' => $y,
							// ];

							$x = $middleX;
							$y = $middleY;


							$isCurve = true;
						} else {
							$nextFlags = $contours[($index + 1) % ($maxIndexContours + 1)]['flags'];
							if (!($nextFlags & $ON_CURVE_POINT)) {
								$isCurve = true;	// @@カーブ開始
								$curvePoints = [
									// [
									// 	'x' => $x,
									// 	'y' => $y,
									// ]
								];
							}

						}
					} else {
						$cmd = 'L';
						$nextFlags = $contours[($index + 1) % ($maxIndexContours + 1)]['flags'];
						if (!($nextFlags & $ON_CURVE_POINT)) {
							$isCurve = true;	// @@カーブ開始
							$curvePoints = [
								// [
								// 	'x' => $x,
								// 	'y' => $y,
								// ]
							];
						}

					}
					$svg .= "{$cmd} {$x},{$y} ";
				}

				$index++;
			}

			// 閉じパス
			if ($isCurve) {
				if (true) {
					$startCoodinate = $contours[0];
					if ($contours[0]['flags'] & 0x01) {
						// $startCoodinate = $contours[0];
						$x = ($contours[0]['x'] / $sizeBase);
						$y = -($contours[0]['y'] / $sizeBase);
						$x += $lsb;
						$y += 2000 / $sizeBase;
// $x += 60;
// $curvePoints[0]['x'] += 60;
						$svg .= "Q {$curvePoints[0]['x']},{$curvePoints[0]['y']} {$x},{$y} ";

						// NOTE: ここ

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


							$svg .= "Q {$curvePoints[0]['x']},{$curvePoints[0]['y']} {$startPoint['x']},{$startPoint['y']} ";



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

				} else {
					foreach ($curvePoints as $cp) {
						$svg .= "L {$cp['x']},{$cp['y']} ";
					}
					$startCoodinate = $contours[0];
					$x = ($contours[0]['x'] / $sizeBase);
					$y = -($contours[0]['y'] / $sizeBase);
					$x += $lsb;
					$y += 2000 / $sizeBase;
					$svg .= "L {$x},{$y} ";

				}
			}
			$svg .= 'z ';
		}
		$svg .= '" fill="#e0e0e0" stroke="black" stroke-width="1" />';


		foreach ($testCurves as $i => $tc) {
			$color = 'blue';
			if ($i == 0) {
				$color = 'red';
			}
			if ($i == 1) {
				$color = 'white';
			}
			// $svg .= "<circle id='{$i}' cx='{$tc['x']}' cy='{$tc['y']}' r='3' fill='{$color}' stroke='black' stroke-width='1'/>";
		}

		foreach ($testPoints as $i => $tc) {
			$color = 'white';
			if ($i == 0) {
				$color = 'red';
			}
			if ($i == 1) {
				$color = 'blue';
			}
			// $svg .= "<circle id='{$i}' cx='{$tc['x']}' cy='{$tc['y']}' r='2' fill='{$color}' stroke='black' stroke-width='1'/>";
		}


		$svg .= '</svg>';

		return $svg;
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

	protected function dumpHorizontalHeaderTable($binHhea)
	{
		$horizontalHeaderTable = unpack('nmajorVersion/nminorVersion/nascender/ndescender/nlineGap/nadvanceWidthMax/nminLeftSideBearing/nminRightSideBearing/nxMaxExtent/ncaretSlopeRise/ncaretSlopeRun/ncaretOffset/n4reserve/nmetricDataFormat/nnumberOfHMetrics', $binHhea);
		return $horizontalHeaderTable;
	}


	protected function dumpHorizontalMetrix($horizontalHeaderTable, $binHmtx)
	{
		$hmtcCount = $horizontalHeaderTable['numberOfHMetrics'];

		$HorizontalMetrixList = [];
		for ($i = 0; $i < $hmtcCount; $i++) {
			$HorizontalMetrixList[] = unpack('nadvanceWidth/nlsb', $binHmtx);
			$binHmtx = substr($binHmtx, 4);
		}

		// TODO: leftSideBearing
		return $HorizontalMetrixList;
	}

}
