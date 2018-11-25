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
use FontObscure\TtfCmap;

use FontObscure\GlyphSvg;

class TtfFile extends Model
{
    use TraitTtfFileElement;

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
        $this->name = 'aaaa';
        // $this->save();

        $ttf = [];
        $offsetData = TtfOffsetData::createFromFile($binTtfFile, 0);
        $tableRecords = $this->parseTableRecords($binTtfFile, $offsetData, 12);

        $head = TtfHead::createFromFile($binTtfFile, $tableRecords['head']->offset);
        $maxList = TtfMaxList::createFromFile($binTtfFile, $tableRecords['maxp']->offset);
        $cmap = TtfCmap::createFromFile($binTtfFile, $tableRecords['cmap']->offset);
        $horizontalHeader = TtfHorizontalHeaderData::createFromFile($binTtfFile, $tableRecords['hhea']->offset);
        $hmtx = $this->parseHorizontalMetrix($horizontalHeader, $binTtfFile, $tableRecords['hmtx']->offset);
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

	protected function parseHorizontalMetrix($horizontalHeaderTable, $binHmtx, $offset)
	{
		$hmtcCount = $horizontalHeaderTable->number_of_hmetrics;

		$horizontalMetrixList = [];
		for ($i = 0; $i < $hmtcCount; $i++) {
			$horizontalMetrixList[] = TtfHorizontalMetrix::createFromFile($horizontalHeaderTable, $binHmtx, $offset);
            $offset += 4;
		}

		return $horizontalMetrixList;
	}

	public function getGlyphIndex($charCode)
	{
        $cmap = $this->ttf['cmap']['sub_tables'][0];
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
