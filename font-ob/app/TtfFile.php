<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

use FontObscure\TtfOffsetData;
use FontObscure\TtfTableRecord;
use FontObscure\TtfHead;
use FontObscure\TtfMaxList;
use FontObscure\TtfHorizontalHeaderData;
use FontObscure\TtfHorizontalMetrix;
use FontObscure\TtfIndexToLocation;

use FontObscure\GlyphSvg;

class TtfFile extends Model
{
    use TraitTtfFileElement;

    protected $fileFormat = [
        'cmap' => [
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

        ],
    ];

    public $ttf = null;

    public static function createFromFile($name, $binTtfFile)
    {
        $t = self::create([
            'name' => $name,
        ]);

        $t->ttf = $t->parseTtf($binTtfFile);

        return $t;
    }


    public function ttfGlyphs()
    {
        return $this->hasMany('FontObscure\TtfGlyph');
    }

    public function cmapSubData()
    {
        return $this->hasMany('FontObscure\CmapSubData');
    }

    protected function createSvg($charCode)
    {
        $glyphIndex = $this->getGlyphIndex($charCode);


        // TODO:


    }

    protected function calculateCheckSum($binData, $offset, $length)
    {
        // $length = (($length + 3) & ~3) / 4;
        $length = (int)(($length + 3) / 4);

        $sum = 0;
        for ($i = 0; $i < $length; $i++) {
            $p = unpack("@{$offset}/N", $binData)[1];
            $sum = ($sum + $p) & 0xffffffff;
            $offset += 4;
        }
        return $sum;
    }

    protected function parseTtf($binTtfFile)
    {
        // $this->name = 'aaaa';
        // $this->save();

        $ttf = [];
        $offsetData = TtfOffsetData::createFromFile($binTtfFile, 0);
        $tableRecords = $this->parseTableRecords($binTtfFile, $offsetData, 12);

        $head = TtfHead::createFromFile($binTtfFile, $tableRecords['head']->offset);
        $maxList = TtfMaxList::createFromFile($binTtfFile, $tableRecords['maxp']->offset);
        $cmap = $this->parseCmap($binTtfFile, $tableRecords['cmap']);

        $horizontalHeader = TtfHorizontalHeaderData::createFromFile($binTtfFile, $tableRecords['hhea']->offset);

        $hmtx = $this->parseHorizontalMetrix($horizontalHeader, $binTtfFile, $tableRecords['hmtx']);

        $indexToLocation = TtfIndexToLocation::createFromFile($head, $maxList, $binTtfFile, $tableRecords['loca']->offset);


        $glyphList = [];
        foreach ($indexToLocation->offsets as $index => $offset) {
            if (!$head->index_to_loc_format) {
                $offset *= 2;
            }

            $g = TtfGlyph::createFromFile($binTtfFile, $tableRecords['glyf']['offset'] + $offset, $index);
            if ($g) {
                $this->TtfGlyphs()->save($g);
            }

// echo "glyph-index={$index} : created !<br />\n";
        }
// dd('OK!');
        return [
            'offsetData' => $offsetData,
            'tableRecords' => $tableRecords,
            'maxList' => $maxList,
            'horizontalHeader' => $horizontalHeader,
            'hmtx' => $hmtx,
            'indexToLocation' => $indexToLocation,
            'cmap' => $cmap,
            'glyphList' => $glyphList,
        ];
    }

    protected function parseTableRecords($binTtfFile, $offsetData, $offset)
    {
        $recordCount = $offsetData->num_tables;

        $tableRecords = [];
        for ($i = 0; $i < $recordCount; $i++) {

            $record = TtfTableRecord::createFromFile($binTtfFile, $offset);
            $tableRecords[$record->tag] = $record;

            $offset += 16;
        }
        return $tableRecords;
    }

    protected function parseHead($binHead, $info)
    {
        $head = self::unpackBinData($this->fileFormat['head'], $binHead, $info['offset']);
        return $head;
    }

    protected function parseLocation($binTtfFile, $head, $maxList, $info)
    {
        $locaFormat = "@{$info['offset']}/";
        $locaCount = $maxList->num_glyphs; // 46個？
		if (!$head->index_to_loc_format) {
			$locaFormat .= "n{$locaCount}";
		} else {
			$locaFormat .= "N{$locaCount}";
		}
		$locaList = array_values(unpack($locaFormat, $binTtfFile));

        return $locaList;
    }

    protected function parseCmap($binCmap, $info)
    {
        $baseOffset = $info['offset'];
        $formatCmap = $this->fileFormat['cmap'];

        $offset = $baseOffset;
        $header = self::unpackBinData($formatCmap['cmapHeader'], $binCmap, $offset);
        $offset += 8;

        $encodingRecords = [];
        $count = $header['numTables'];
        for ($i = 0; $i < $count; $i++) {
            $offset = $info['offset'] + 4 + ($i * 8);
            $recordInfo = self::unpackBinData($formatCmap['encodingRecord'], $binCmap, $offset);
            $encodingRecords[] = $recordInfo;

            $subTables[] = $this->parseCmapSubTable($baseOffset, $recordInfo, $binCmap);
        }

        return [
            'header' => $header,
            'encodingRecords' => $encodingRecords,
            'subTables' => $subTables,
        ];
    }

	protected function parseCmapSubTable($baseOffset, $encodingRecord, $binCmap)
	{
        $offset = $baseOffset + $encodingRecord['offset'];
		$subFormat = unpack("@{$offset}/nformat", $binCmap);

		if ($subFormat['format'] == 0x00) {
            $offset = $baseOffset + $encodingRecord['offset'];
			$subHeader = unpack("@{$offset}/nformat/nlength/nlanguage", $binCmap);
            $offset += 6;

			$binSubTableBody = array_values(unpack("@{$offset}/C256charcode", $binCmap));	// NOTE: table char-code -> glyf-index
			return $binSubTableBody;
		}

		if ($subFormat['format'] == 0x04) {
            $offset = $baseOffset + $encodingRecord['offset'];
			$subHeader = unpack("@{$offset}/nformat/nlength/nlanguage/nsegCountX2/nsearchRange/nentrySelector/nrangeShift", $binCmap);
			$count = $subHeader['segCountX2'] / 2;
            $offset += 14;

			$endCountList = array_values(unpack("@{$offset}/n{$count}", $binCmap));
            $offset += ($count * 2);
            $offset += 2;

			$startCountList = array_values(unpack("@{$offset}/n{$count}", $binCmap));
            $offset += ($count * 2);

			$idDeltaList = array_values(unpack("@{$offset}/n{$count}", $binCmap));
            $offset += ($count * 2);
			foreach ($idDeltaList as &$idDelta) {
				if ($idDelta > 0x7fff) {
					$idDelta = -(0x8000 - ($idDelta & 0x7fff));
				}
			}
			unset($idDelta);

			$idRangeOffsetList = array_values(unpack("@{$offset}/n{$count}", $binCmap));
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
				$glyphIdArray = array_values(unpack("@{$offset}/n{$len}", $binCmap));
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

	protected function parseHorizontalMetrix($horizontalHeaderTable, $binHmtx, $info)
	{
		$hmtcCount = $horizontalHeaderTable->number_of_hmetrics;

		$horizontalMetrixList = [];
        $offset = $info['offset'];
		for ($i = 0; $i < $hmtcCount; $i++) {
			$horizontalMetrixList[] = TtfHorizontalMetrix::createFromFile($horizontalHeaderTable, $binHmtx, $offset);
            // self::unpackBinData($this->fileFormat['hmtx'], $binHmtx, $offset);
            $offset += 4;
		}
		// TODO: leftSideBearing
		return $horizontalMetrixList;
	}

	public function getGlyphIndex($charCode)
	{
        $cmap = $this->ttf['cmap']['subTables'][0];
		$header = $cmap['header'];

		if ($header['format'] != 4) {
			dd('未対応');
		}

		if ($header['format'] == 4) {
			$map = $cmap['body'];
			$glyphIdArray = $cmap['glyphIdArray'];

			$glyphIndex = 0;
			$index = 0;
			foreach ($map as $index => $m) {
				if ($m['endCount'] >= $charCode) {
					if ($m['startCount'] <= $charCode) {
						if ($m['idRangeOffset'] > -1) {
							// TODO: + $m['idDelta']が必要か確認
							$glyphIdArrayIndex = $m['idRangeOffset'] + ($charCode - $m['startCount']);
							return $glyphIdArray[$glyphIdArrayIndex];
						} else {
							$glyphIndex = ($charCode) + $m['idDelta'];
							return $glyphIndex;
						}
					}
				}
			}
			return -1;
		}

		return -1;
	}

    protected $fillable = [
        'name',
    ];
}
