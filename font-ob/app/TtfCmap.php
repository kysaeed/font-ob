<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfCmap extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'cmap_header' => [
			'version' => ['n', 1],
			'num_tables' => ['n', 1],
		],

		'encoding_record' => [
			'platform_id' => ['n', 1],
			'encoding_id' => ['n', 1],
			'offset' => ['N', 1],
		],

		'format' => [
			'format' => ['N', 1],
			0 => [
				'length' => ['n', 1],
				'language' => ['n', 1],
				'glyph_id_array' => ['C', 256],
			],
			4 => [
				'format' => ['n', 1],
				'length' => ['n', 1],
				'language' => ['n', 1],
				'seg_count_x2' => ['n', 1],
				'search_range' => ['n', 1],
				'entry_selector' => ['n', 1],
				'range_shift' => ['n', 1],
			],
		],
	];

	public static function createFromFile($binTtfFile, $offset)
	{
		$baseOffset = $offset;
        $formatCmap = self::FileFormat;

		$header = self::unpackBinData(self::FileFormat['cmap_header'], $binTtfFile, $offset);
		$offset += 8;

		$encodingRecords = [];
        $count = $header['num_tables'];
        for ($i = 0; $i < $count; $i++) {
            $offset = $baseOffset + 4 + ($i * 8);
            $recordInfo = self::unpackBinData($formatCmap['encoding_record'], $binTtfFile, $offset);
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
			$subHeader = unpack("@{$offset}/nformat/nlength/nlanguage/nseg_count_x2/nsearch_range/nentry_selector/nrange_shift", $binTtfFile);
			$count = $subHeader['seg_count_x2'] / 2;
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
			$len = ($subHeader['length'] - ($subHeader['seg_count_x2'] * 4 + 16)) / 2;
			if ($len > 0) {
				$glyphIdArray = array_values(unpack("@{$offset}/n{$len}", $binTtfFile));
			} else {
				$glyphIdArray = [];
			}

			return [
				'header' => $subHeader,
				'body' => $subTableBody,
				'glyph_id_array' => $glyphIdArray,
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
