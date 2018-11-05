<?php

namespace FontObscure\Http\Controllers;

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

			$gs = new \FontObscure\GlyphSvg($glyfData, $hm);

			$svg = $gs->getSvg();
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
