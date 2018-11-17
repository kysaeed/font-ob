<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

use FontObscure\GlyphSvg;

class TffFile extends Model
{
    protected $fileFormat = [
        'offsetTable' => [
            'sfntVersion' => ['N', 1],
            'numTables' => ['n', 1],
            'searchRange' => ['n', 1],
            'entrySelector' => ['n', 1],
            'rangeShift' => ['n', 1],
        ],
        'tablesRecoed' => [
            'tag' => ['A', 4],
            'sum' => ['N', 1],
            'offset' => ['N', 1],
            'length' => ['N', 1],
        ],

        'head' => [
            'majorVersion' => ['n', 1],
            'minorVersion' => ['n', 1],
            'fontRevision' => ['N', 1],
            'checkSumAdjustment' => ['N', 1],
            'magicNumber' => ['N', 1],
            'flags' => ['n', 1],
            'unitsPerEm' => ['n', 1],
            'created' => ['J', 1],
            'modified' => ['J', 1],
            'xMin' => ['n', 1, true],
            'yMin' => ['n', 1, true],
            'xMax' => ['n', 1, true],
            'yMax' => ['n', 1, true],
            'macStyle' => ['n', 1],
            'lowestRecPPEM' => ['n', 1],
            'fontDirectionHint' => ['n', 1, true],
            'indexToLocFormat' => ['n', 1, true],
            'glyphDataFormat' => ['n', 1, true],
        ],

        'hhea' => [
            'majorVersion' => ['n', 1],
            'minorVersion' => ['n', 1],
            'ascender' => ['n', 1, true],
            'descender' => ['n', 1, true],
            'lineGap' => ['n', 1, true],
            'advanceWidthMax' => ['n', 1],
            'minLeftSideBearing' => ['n', 1, true],
            'minRightSideBearing' => ['n', 1, true],
            'xMaxExtent' => ['n', 1, true],
            'caretSlopeRise' => ['n', 1, true],
            'caretSlopeRun' => ['n', 1, true],
            'caretOffset' => ['n', 1, true],
            'reserve' => ['n', 4, true],
            'metricDataFormat' => ['n', 1, true],
            'numberOfHMetrics' => ['n', 1],
        ],

        'hmtx' => [
            'advanceWidth' => ['n', 1],
            'lsb' => ['n', 1],
        ],

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

        'glyf' => [
            'header' => [
                'numberOfContours' => ['n', 1, true],
                'xMin' => ['n', 1, true],
                'yMin' => ['n', 1, true],
                'xMax' => ['n', 1, true],
                'yMax' => ['n', 1, true],
            ],
        ],

    ];

    public $ttf = null;

    public function __construct($binTtfFile)
	{
        $this->ttf = $this->parseTtf($binTtfFile);
// dump( $this->getGlyphIndex(0x30) );

        // dd($this->ttf);
	}

    protected function createSvg($charCode)
    {
        $glyphIndex = $this->getGlyphIndex($charCode);


        // TODO:





    }

    protected function parseTtf($binTtfFile)
    {
        $ttf = [];
        $offsetTable = $this->parseOffsetTable($binTtfFile);

        $binTableRecords = substr($binTtfFile, 12, $offsetTable['numTables'] * 16);
        $tableRecords = $this->parseTableRecords($binTableRecords, $offsetTable);

        $binHead = $this->getTableBody($binTtfFile, $tableRecords['head']);
        $head = $this->parseHead($binHead);
        unset($binHead);

        $binMaxp = $this->getTableBody($binTtfFile, $tableRecords['maxp']);
		$maxList = unpack('Nver/nnumGlyphs/nmaxPoints', $binMaxp);
        unset($binMaxp);

        // $binCmap = $this->getTableBody($binTtfFile, $tableRecords['cmap']);
        $cmap = $this->parseCmap($tableRecords['cmap'], $binTtfFile);

        $binHhea = $this->getTableBody($binTtfFile, $tableRecords['hhea']);
        $hhea = $this->parseHorizontalHeaderTable($binHhea);
        unset($binHhea);

        $binHmtx = $this->getTableBody($binTtfFile, $tableRecords['hmtx']);
        $hmtx = $this->parseHorizontalMetrix($hhea, $binHmtx);
        unset($binHmtx);

        $binLoca = $this->getTableBody($binTtfFile, $tableRecords['loca']);
        $locationList = $this->parseLocation($binLoca, $head, $maxList);
        unset($binLoca);


        // $binGlyphsData = $this->getTableBody($binTtfFile, $tableRecords['glyf']);

        $glyphList = [];
        foreach ($locationList as $index => $offset) {
            if (!$head['indexToLocFormat']) {
                $offset *= 2;
            }
            $glyph = $this->parseGlyph($tableRecords['glyf']['offset'] + $offset, $binTtfFile);

            if (!empty($glyph)) {
                $g = new TtfGlyph([
                    'glyph_index' => $index,
            		'numberOfContours' => $glyph['header']['numberOfContours'],
            		'xMin' => $glyph['header']['xMin'],
            		'yMin' => $glyph['header']['yMin'],
            		'xMax' => $glyph['header']['xMax'],
            		'yMax' => $glyph['header']['yMax'],
            		'coordinates' => json_encode($glyph['coordinates']),
            		'instructions' => json_encode($glyph['instructions']),
                ]);
                $g->save();
            }

            // $glyphList[] = $glyph;
// echo "glyph-index={$index} : created !<br />\n";
        }
// dd('OK!');
        return [
            'offsetTable' => $offsetTable,
            'tableRecords' => $tableRecords,
            'head' => $head,
            'hhea' => $hhea,
            'maxp' => $maxList,
            'hmtx' => $hmtx,
            'loca' => $locationList,
            'cmap' => $cmap,
            'glyphList' => $glyphList,
        ];
    }

    protected function parseLocation($binLocation, $head, $maxList)
    {
        $locaCount = $maxList['numGlyphs']; // 46個？
		if (!$head['indexToLocFormat']) {
			$locaFormat = "n{$locaCount}";
		} else {
			$locaFormat = "N{$locaCount}";
		}
		$locaList = array_values(unpack($locaFormat, $binLocation));

        return $locaList;
    }

    protected function parseOffsetTable($binTtfFile)
    {
        $unpackFormat = '';
        foreach ($this->fileFormat['offsetTable'] as $name => $param) {
            if (!empty($unpackFormat)) {
                $unpackFormat .= '/';
            }
            $unpackFormat .= $param[0].$param[1].$name;
        }
        $offsetTable = unpack($unpackFormat, $binTtfFile);

        if ($offsetTable['sfntVersion'] != 0x00010000) {
            throw new \Exception("file format error.....", 1);
        }

        return $offsetTable;
    }

    protected function parseTableRecords($binTableRecords, $offsetTable)
    {
        $recordCount = $offsetTable['numTables'];

        $tableRecords = [];

        $unpackFormat = '';
        foreach ($this->fileFormat['tablesRecoed'] as $name => $param) {
            if (!empty($unpackFormat)) {
                $unpackFormat .= '/';
            }
            $unpackFormat .= $param[0].$param[1].$name;
        }

        for ($i = 0; $i < $recordCount; $i++) {
            $offset = $i * 16;
            $record = unpack("@{$offset}/".$unpackFormat, $binTableRecords);

            $tag = $record['tag'];
            $tableRecords[$tag] = [
                'sum' => $record['sum'],
                'offset' => $record['offset'],
                'length' => $record['length'],
            ];
        }
        return $tableRecords;
    }

    protected function getTableBody($binTtfFile, $tableInfo)
	{
		$binBody = substr($binTtfFile, $tableInfo['offset'], $tableInfo['length']);

		// TODO: チェックサム

		return $binBody;
	}

    protected function parseHead($binHead)
    {
        $head = $this->unpackBinData($this->fileFormat['head'], $binHead);
        return $head;
    }

    protected function unpackBinData($format, $binData, $offset = 0)
    {
        $sizeList = [
            'n' => 2,
            'N' => 4,
            'J' => 8,
        ];

        $unpacked = [];
        foreach ($format as $name => $param) {
            $u = array_values(unpack("@{$offset}/{$param[0]}{$param[1]}", $binData));
            if (array_key_exists(2, $param)) {
                if ($param[2]) {
                    switch ($param[0]) {
                        case 'n':
                            foreach ($u as &$value) {
                                if ($value > 0x7FFF) {
                                    $value = -(0x8000 - ($value & 0x7fff));
                                }
                            }
                            unset($value);
                            break;

                        case 'N':
                            foreach ($u as &$value) {
                                if ($value > 0x7FFFFFFF) {
                                    $value = -(0x80000000 - ($value & 0x7FFFFFFF));
                                }
                            }
                            unset($value);
                            break;
                    }
                }
            }
            if (count($u) == 1) {
                $u = $u[0];
            }
            $unpacked[$name] = $u;
            $offset += $sizeList[$param[0]] * $param[1];
        }
        return $unpacked;
    }

    protected function parseCmap($info, $binCmap)
    {
        $baseOffset = $info['offset'];
        $formatCmap = $this->fileFormat['cmap'];

        $offset = $baseOffset;
        $header = $this->unpackBinData($formatCmap['cmapHeader'], $binCmap, $offset);
        $offset += 8;

        $encodingRecords = [];
        $count = $header['numTables'];
        for ($i = 0; $i < $count; $i++) {
            $offset = $info['offset'] + 4 + ($i * 8);
            $recordInfo = $this->unpackBinData($formatCmap['encodingRecord'], $binCmap, $offset);
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

	protected function parseGlyph($offset, $binGlyph)
	{
        $format = $this->fileFormat['glyf'];
        $glyphHeader = $this->unpackBinData($format['header'], $binGlyph, $offset);
        $offset += 10;
		foreach ($glyphHeader as &$param) {
			if ($param >= 0x7fff) {
				$param = -(0x8000 - ($param & 0x7fff));
			}
		}
		unset($param);

// dump($glyphHeader['numberOfContours']);
        if ($glyphHeader['numberOfContours'] < 0) {
        	return null;
        }

		$endPtsOfContoursList = array_values(unpack("@{$offset}/n{$glyphHeader['numberOfContours']}", $binGlyph));
        $offset += (2 * $glyphHeader['numberOfContours']);

		$instructionLength = unpack("@{$offset}/n{$glyphHeader['numberOfContours']}", $binGlyph)[1];
        $offset += 2;

		$instructions = array_values(unpack("@{$offset}/C{$instructionLength}", $binGlyph));
        $offset += $instructionLength;


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
			$flags = unpack("@{$offset}/C", $binGlyph)[1];
            $offset++;
			$flagsList[] = $flags;
			if ($flags & $REPEAT_FLAG) {
				$repeatCount = unpack("@{$offset}/C", $binGlyph)[1];
                $offset++;
				for ($j = 0; $j < $repeatCount; $j++) {
					$flagsList[] = $flags;
				}
			}
		}

		$xCoordinatesList = [];
		$x = 0;
		foreach ($flagsList as $index => $flags) {
			if ($flags & $X_SHORT_VECTOR) {
				$xCoordinate = unpack("@{$offset}/C", $binGlyph)[1];
                $offset++;
				if (!($flags & $X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR)) {
					$xCoordinate = -$xCoordinate;
				}
			} else {
				if (!($flags & $X_IS_SAME_OR_POSITIVE_X_SHORT_VECTOR)) {
					$xCoordinate = unpack("@{$offset}/n", $binGlyph)[1];
                    $offset += 2;
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
				$yCoordinate = unpack("@{$offset}/C", $binGlyph)[1];
                $offset++;
				if (!($flags & $Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR)) {
					$yCoordinate = -$yCoordinate;
				}
			} else {
				if (!($flags & $Y_IS_SAME_OR_POSITIVE_Y_SHORT_VECTOR)) {
					$yCoordinate = unpack("@{$offset}/n", $binGlyph)[1];
                    $offset += 2;
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

	protected function parseHorizontalHeaderTable($binHhea)
	{
        $horizontalHeaderTable = $this->unpackBinData($this->fileFormat['hhea'], $binHhea);
		// $horizontalHeaderTable = unpack('nmajorVersion/nminorVersion/nascender/ndescender/nlineGap/nadvanceWidthMax/nminLeftSideBearing/nminRightSideBearing/nxMaxExtent/ncaretSlopeRise/ncaretSlopeRun/ncaretOffset/n4reserve/nmetricDataFormat/nnumberOfHMetrics', $binHhea);

		return $horizontalHeaderTable;
	}


	protected function parseHorizontalMetrix($horizontalHeaderTable, $binHmtx)
	{
		$hmtcCount = $horizontalHeaderTable['numberOfHMetrics'];

		$horizontalMetrixList = [];
        $offset = 0;
		for ($i = 0; $i < $hmtcCount; $i++) {
			$horizontalMetrixList[] = $this->unpackBinData($this->fileFormat['hmtx'], $binHmtx, $offset);
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
							$glyphIndex = ($charCode) + $m['idDelta'] /* - 33 */;
							return $glyphIndex;
						}
					}
				}
			}
			return -1;
		}

		return -1;
	}
}
