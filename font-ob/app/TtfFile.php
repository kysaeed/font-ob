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
    protected $elements = null;

    public static function createFromFile($name, $binTtfFile)
    {
        $t = self::create([
            'name' => $name,
        ]);

        $t->elements = $t->parseTtf($binTtfFile);

        $t->ttf = $t->elements;

        return $t;
    }


    public function ttfGlyphs()
    {
        return $this->hasMany('FontObscure\TtfGlyph');
    }

    public function ttfCmap()
    {
        return $this->hasMany('FontObscure\TtfCmap');
    }

    public function ttfHorizontalMetrix()
    {
        return $this->hasMany('FontObscure\TtfHorizontalMetrix');
    }

    public function createSvg($charCode)
    {
        $glyphIndex = $this->getGlyphIndex($charCode);

        if ($glyphIndex < 0) {
            return null;
        }

        $g = $this->ttfGlyphs()->where('glyph_index', $glyphIndex)->first();

        $glyfData = [
            'header' => [
                 'xMin' => $g->xMin,
                 'yMin' => $g->yMin,
                 'xMax' => $g->xMax,
                 'yMax' => $g->yMax,
            ],
            'coordinates' => $g->coordinates,
            'instructions' => $g->instructions,
        ];

        $hm = $this->ttfHorizontalMetrix[$glyphIndex];
        $gs = new GlyphSvg($glyfData, $hm);

        return $gs;
        // $svg = $gs->getSvg();
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

        foreach ($indexToLocation->offsets as $index => $offset) {
            if (!$head->index_to_loc_format) {
                $offset *= 2;
            }

            $glyph = TtfGlyph::createFromFile($binTtfFile, $tableRecords['glyf']['offset'] + $offset, $index);
// dd($g->coordinates);
            if ($glyph) {
                $this->ttfGlyphs()->save($glyph);
            }
// $g->toBinary();
// dd('OK');
        }

// dd('OK!');
        $this->ttfCmap = $cmap;
        $this->ttfHorizontalMetrix = $hmtx;

        return [
            'offsetData' => $offsetData,
            'tableRecords' => $tableRecords,
            'maxList' => $maxList,
            'horizontalHeader' => $horizontalHeader,
            'indexToLocation' => $indexToLocation,
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
        $cmap = $this->ttfCmap['sub_tables'][0];
		$header = $cmap['header'];

		if ($header['format'] != 4) {
			dd('未対応');
		}

		if ($header['format'] == 4) {
			$map = $cmap['body'];
			$glyphIdArray = $cmap['glyph_id_array'];

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
        'offset_data',
        'table_records',
        'max_list',
        'horizontal_header',
        'index_to_location',
    ];
}
