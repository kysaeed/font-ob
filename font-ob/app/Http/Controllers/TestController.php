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
		for ($i = 0; $i < $cmapTableCount; $i++) {
			$binEncordingRecord = substr($binCmap, 4 + $i * 8, 8);
			$encodingRecord = unpack('nplatformID/nencodingID/Noffset', $binEncordingRecord);
			$a = $this->dumpCmapSubTable($encodingRecord, $binCmap);
		}



        // foreach ($tableRecords as $key => $t) {
        //     $test = substr($f, $t['offset'], $t['length']);
        //     $mySum = $this->calculateCheckSum($test);
		// 	// dump( sprintf('calculated-sum=%08x , org-sum=%08x', $mySum, $t['sum']) );
        // }


		$maxp = $tableRecords['maxp'];

		$binMaxp = substr($file, $maxp['offset'], $maxp['length']);

		$maxList = unpack('Nver/nnumGlyphs/nmaxPoints', $binMaxp);

		$loca = $tableRecords['loca'];
		$l = substr($file, $loca['offset'], $loca['length']);

		$locaCount = $maxList['numGlyphs'] + 1;

		$loca = unpack("n{$locaCount}", $l);	//indexToLocFormatでshort or long

		$glyf = $tableRecords['glyf'];
		$g = substr($file, $glyf['offset'], $glyf['length']);
		// $glyph


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


			$subTableBody = [];
			$subTableBody['endCount'] = array_values(unpack("n{$count}", $binSubTableBody));
			$binSubTableBody = substr($binSubTableBody, $count * 2 + 2); // add reserved pad 2bytes

			$subTableBody['startCount'] = array_values(unpack("n{$count}", $binSubTableBody));
			$binSubTableBody = substr($binSubTableBody, $count * 2);

			$subTableBody['idDelta'] = array_values(unpack("n{$count}", $binSubTableBody));
			$binSubTableBody = substr($binSubTableBody, $count * 2);
			foreach ($subTableBody['idDelta'] as &$idDelta) {
				if ($idDelta > 0x7fff) {
					$idDelta = -(0x8000 - ($idDelta & 0x7fff));
				}
			}
			unset($idDelta);

			$subTableBody['idRangeOffset'] = array_values(unpack("n{$count}", $binSubTableBody));
			$binSubTableBody = substr($binSubTableBody, $count * 2);

			return $subTableBody;
		}

		return null;
	}

}
