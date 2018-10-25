<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Storage;


class TestController extends Controller
{
	public function test(Request $request)
    {
        $file = Storage::disk('local')->get('strokes/font.ttf');

        $h = unpack('Nver/nnum/nrange/nselector/nshift', $file);

echo '<hr />';
dump('header');
dump($h);
echo '<hr />';

        $tableRecords = [];
        $readOffset = 12;
        for ($i = 0; $i < $h['num']; $i++) {
            $tag = substr($file, $readOffset, 4);
            $tableRecordData = substr($file, ($readOffset + 4), (16 - 4));

            $t = unpack('Nsum/Noffset/Nlength', $tableRecordData);

            $tableRecords[$tag] = $t;
            $readOffset += 16;
        }

        dump($tableRecords);


//         foreach ($tableRecords as $key => $t) {
//             $test = substr($f, $t['offset'], $t['length']);
//             $mySum = $this->calculateCheckSum($test);
// dump( sprintf('calculated-sum=%08x , org-sum=%08x', $mySum, $t['sum']) );
//         }


		$maxp = $tableRecords['maxp'];

		$m = substr($file, $maxp['offset'], $maxp['length']);




		$maxList = unpack('Nver/nnumGlyphs/nmaxPoints', $m);
// dd($maxList);


		$loca = $tableRecords['loca'];
		$l = substr($file, $loca['offset'], $loca['length']);

		$locaCount = $maxList['numGlyphs'] + 1;

		$loca = unpack("n{$locaCount}", $l);	//indexToLocFormatでshort or long

		$glyf = $tableRecords['glyf'];
		$g = substr($file, $glyf['offset'], $glyf['length']);
		// $glyph


		// $c = substr($g, xxx)
echo '<hr />';

		$cmapInfo = $tableRecords['cmap'];

		$binCmap = substr($file, $cmapInfo['offset'], $cmapInfo['length']);
		$cmapHeader = unpack('nvar/nnumTables', $binCmap);

		$nt = $cmapHeader['numTables'];
		for ($i = 0; $i < $nt; $i++) {
echo "<hr />cmap:{$i}<hr />";
			$binEncordingRecord = substr($binCmap, 4 + $i * 8, 8);
			$encodingRecord = unpack('nplatformID/nencodingID/Noffset', $binEncordingRecord);

			$this->dumpCmapSubTable($encodingRecord, $binCmap);
		}

		return '';
    }

	protected function dumpCmapSubTable($encodingRecord, $binCmap)
	{
dump($encodingRecord);
		$binSub = substr($binCmap, $encodingRecord['offset'], 2);
		$subFormat = unpack('nformat', $binSub);

dump('format = '.$subFormat['format']);


		if ($subFormat['format'] == 0x04) {
			$binSubHeader = substr($binCmap, $encodingRecord['offset'], 14);
			$subHeader = unpack('nformat/nlength/nlanguage/nsegCountX2/nsearchRange/nentrySelector/nrangeShift', $binSubHeader);
dump($subHeader);
			$count = $subHeader['segCountX2'] / 2;
			// ArrayAccess

			$binSubTableBody = substr($binCmap, $encodingRecord['offset'] + 14);
			$subTableBody = unpack("n{$count}endCount/nreservedPad/n{$count}startCount/n{$count}idDelta/n{$count}idRangeOffset", $binSubTableBody);	// TODO: idDelta is signed !
dump($subTableBody);
		}

		return null;
	}

}
