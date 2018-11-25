<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfCmap extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'cmapHeader' => [
			'version' => ['n', 1],
			'numTables' => ['n', 1],
		],

		'encodingRecord' => [
			'platformID' => ['n', 1],
			'encodingID' => ['n', 1],
			'offset' => ['N', 1],
		],

		'format' => [
			'format' => ['N', 1],
			0 => [
				'length' => ['n', 1],
				'language' => ['n', 1],
				'glyphIdArray' => ['C', 256],
			],
			4 => [
				'format' => ['n', 1],
				'length' => ['n', 1],
				'language' => ['n', 1],
				'segCountX2' => ['n', 1],
				'searchRange' => ['n', 1],
				'entrySelector' => ['n', 1],
				'rangeShift' => ['n', 1],
			],
		],
	];

	public static function createFromFile($binTtfFile, $offset)
	{
		$baseOffset = $offset;
        $formatCmap = self::FileFormat;

		$header = self::unpackBinData(self::FileFormat['cmapHeader'], $binTtfFile, $offset);
		$offset += 8;

		$encodingRecords = [];
        $count = $header['numTables'];
        for ($i = 0; $i < $count; $i++) {
            $offset = $baseOffset + 4 + ($i * 8);
            $recordInfo = self::unpackBinData($formatCmap['encodingRecord'], $binTtfFile, $offset);
            $encodingRecords[] = $recordInfo;
            $subTables[] = self::parseCmapSubTable($binTtfFile, $baseOffset, $recordInfo);
        }

        return new TtfCmap([
            'version' => $header['version'],
            'encoding_records' => $encodingRecords,
            'sub_tables' => $subTables,
        ]);
	}

	protected static function parseCmapSubTable($binTtfFile, $baseOffset, $encodingRecord)
	{
        $offset = $baseOffset + $encodingRecord['offset'];
		$subFormat = unpack("@{$offset}/nformat", $binTtfFile);
// dump($subFormat['format']);

		if ($subFormat['format'] == 0x00) {
            $offset = $baseOffset + $encodingRecord['offset'];
			$subHeader = unpack("@{$offset}/nformat/nlength/nlanguage", $binTtfFile);
            $offset += 6;

			$binSubTableBody = array_values(unpack("@{$offset}/C256charcode", $binTtfFile));	// NOTE: table char-code -> glyf-index
			return $binSubTableBody;
		}

		if ($subFormat['format'] == 0x04) {
            $offset = $baseOffset + $encodingRecord['offset'];
			$subHeader = unpack("@{$offset}/nformat/nlength/nlanguage/nsegCountX2/nsearchRange/nentrySelector/nrangeShift", $binTtfFile);
			$count = $subHeader['segCountX2'] / 2;
            $offset += 14;

			$endCountList = array_values(unpack("@{$offset}/n{$count}", $binTtfFile));
            $offset += ($count * 2);
            $offset += 2;

			$startCountList = array_values(unpack("@{$offset}/n{$count}", $binTtfFile));
            $offset += ($count * 2);

			$idDeltaList = array_values(unpack("@{$offset}/n{$count}", $binTtfFile));
            $offset += ($count * 2);
			foreach ($idDeltaList as &$idDelta) {
				if ($idDelta > 0x7fff) {
					$idDelta = -(0x8000 - ($idDelta & 0x7fff));
				}
			}
			unset($idDelta);

			$idRangeOffsetList = array_values(unpack("@{$offset}/n{$count}", $binTtfFile));
            $offset += ($count * 2);

			$rangeOffsetCount = count($idRangeOffsetList);
			foreach ($idRangeOffsetList as $index => &$rangeOffset) {
				if ($rangeOffset > 0) {
					$rangeOffset = ($rangeOffset / 2) - ($rangeOffsetCount - $index);
				} else {
					$rangeOffset = -1;
				}
			}
			unset($rangeOffset);

			$subTableBody = [];
			for ($i = 0; $i < $count; $i++) {
				$subTableBody[] = [
					'startCount' => $startCountList[$i],
					'endCount' => $endCountList[$i],
					'idDelta' => $idDeltaList[$i],
					'idRangeOffset' => $idRangeOffsetList[$i],
				];
			}

			$count = count($idRangeOffsetList);
			$len = ($subHeader['length'] - ($subHeader['segCountX2'] * 4 + 16)) / 2;
			if ($len > 0) {
				$glyphIdArray = array_values(unpack("@{$offset}/n{$len}", $binTtfFile));
			} else {
				$glyphIdArray = [];
			}

			return [
				'header' => $subHeader,
				'body' => $subTableBody,
				'glyphIdArray' => $glyphIdArray,
			];
		}

		return null;
	}

	protected $fillable = [
		'version',
		'encoding_records',
		'sub_tables',
	];
}
