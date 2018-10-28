<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Storage;


class TestController extends Controller
{
	public function test(Request $request)
    {
        $file = Storage::disk('local')->get('strokes/font.ttf');

        $header = unpack('Nver/nnum/nrange/nselector/nshift', $file);
		$tableRecords = [];
        $readOffset = 12;
        for ($i = 0; $i < $header['num']; $i++) {
            $tag = substr($file, $readOffset, 4);
            $tableRecordData = substr($file, ($readOffset + 4), (16 - 4));
            $tableRecords[$tag] = unpack('Nsum/Noffset/Nlength', $tableRecordData);
            $readOffset += 16;
        }
		echo '<hr />';
		dump('header');
		dump($header);
		echo '<hr />';



		$cmapInfo = $tableRecords['cmap'];
		$binCmap = substr($file, $cmapInfo['offset'], $cmapInfo['length']);
		$cmapHeader = unpack('nvar/nnumTables', $binCmap);
		$cmapTableCount = $cmapHeader['numTables'];

		$cmaps = [];
		for ($i = 0; $i < $cmapTableCount; $i++) {
			$binEncordingRecord = substr($binCmap, 4 + $i * 8, 8);
			$encodingRecord = unpack('nplatformID/nencodingID/Noffset', $binEncordingRecord);
			$cmaps[] = $this->dumpCmapSubTable($encodingRecord, $binCmap);
		}


        // foreach ($tableRecords as $key => $t) {
        //     $test = substr($f, $t['offset'], $t['length']);
        //     $mySum = $this->calculateCheckSum($test);
		// 	// dump( sprintf('calculated-sum=%08x , org-sum=%08x', $mySum, $t['sum']) );
        // }


		$maxpInfo = $tableRecords['maxp'];
		$binMaxp = substr($file, $maxpInfo['offset'], $maxpInfo['length']);
		$maxList = unpack('Nver/nnumGlyphs/nmaxPoints', $binMaxp);



		$locaInfo = $tableRecords['loca'];
		$binLoca = substr($file, $locaInfo['offset'], $locaInfo['length']);
		$locaCount = $maxList['numGlyphs'] + 1;
		$locaList = array_values(unpack("n{$locaCount}", $binLoca));	//indexToLocFormatでshort or long


		$glyf = $tableRecords['glyf'];
		$g = substr($file, $glyf['offset'], $glyf['length']);
		// $glyph



		/////////////////////////////////
		$charCode = 0x0063;
		$glyphIndex = 0;

		$index = 0;
		$map = $cmaps[0];
		$count = count($map);
		foreach ($map as $m) {
			if ($m['endCount'] >= $charCode) {
				if ($m['startCount'] <= $charCode) {

					// TODO: set offset to glyf id array !


					$glyphIndex = $charCode + $m['idDelta'];

					break;
				}
			}
		}


		$glyfInfo = $tableRecords['glyf'];
		$binGlyphsData = substr($file, $glyfInfo['offset'], $glyfInfo['length']);


		$glyphOffset = $locaList[$glyphIndex] * 2;

		$binGlyph = substr($binGlyphsData, $glyphOffset);
		$this->dumpGlyph($binGlyph);

// dd($glyphIndex);
// dd($cmaps);

		// $c = substr($g, xxx)
// echo '<hr />';

echo 'hello !';die;

		return '';
    }

	protected function dumpCmapSubTable($encodingRecord, $binCmap)
	{
		$binSub = substr($binCmap, $encodingRecord['offset'], 2);
		$subFormat = unpack('nformat', $binSub);


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

		$instructionLength = unpack("n{$glyphHeader['numberOfContours']}", $binGlyph)[1];
		$binGlyph = substr($binGlyph, 2);

		$instructions = array_values(unpack("C{$instructionLength}", $binGlyph));
		$binGlyph = substr($binGlyph, $instructionLength);

		$pointCount = max($endPtsOfContoursList) + 1;
		$flagsList = array_values(unpack("C{$pointCount}", $binGlyph));
		$binGlyph = substr($binGlyph, $pointCount);

		// TODO: 定数を定義
		$ON_CURVE_POINT = (0x01 << 0);
		$X_SHORT_VECTOR = (0x01 << 1);
		$Y_SHORT_VECTOR = (0x01 << 2);
		$REPEAT_FLAG = (0x01 << 3);
		$X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR = (0x01 << 4);
		$Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR = (0x01 << 5);
		$OVERLAP_SIMPLE = (0x01 << 6);

		$xCoordinatesList = [];
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
			$xCoordinatesList[] = $xCoordinate;
		}

		$yCoordinatesList = [];
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
			$yCoordinatesList[] = $yCoordinate;
		}


		$glyphCoordinatesList = [];
		foreach ($flagsList as $index => $flags) {
			$glyphCoordinatesList[] = [
				'x' => $xCoordinatesList[$index],
				'y' => $yCoordinatesList[$index],
				'flags' => $flags,
			];
		}

dd($glyphCoordinatesList);
		return  $glyphCoordinatesList;
	}

}
