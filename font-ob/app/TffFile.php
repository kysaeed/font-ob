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
// dump($tableRecords);

        $binHead = $this->getTableBody($binTtfFile, $tableRecords['head']);
        $head = $this->parseHead($binHead);

        $binMaxp = $this->getTableBody($binTtfFile, $tableRecords['maxp']);
		$maxList = unpack('Nver/nnumGlyphs/nmaxPoints', $binMaxp);

        $binCmap = $this->getTableBody($binTtfFile, $tableRecords['cmap']);
        $cmap = $this->parseCmap($binCmap);

        $binHhea = $this->getTableBody($binTtfFile, $tableRecords['hhea']);
        $hhea = $this->parseHorizontalHeaderTable($binHhea);

        $binHmtx = $this->getTableBody($binTtfFile, $tableRecords['hmtx']);
        $hmtx = $this->parseHorizontalMetrix($hhea, $binHmtx);

        $binLoca = $this->getTableBody($binTtfFile, $tableRecords['loca']);
        $locationList = $this->parseLocation($binLoca, $head, $maxList);

// dd($locationList);
// dd($tableRecords['glyf']);
// dd($locationList);
        $binGlyphsData = $this->getTableBody($binTtfFile, $tableRecords['glyf']);
        $glyphList = [];
        foreach ($locationList as $index => $offset) {
            if (!$head['indexToLocFormat']) {
                $offset *= 2;
            }
            $binGlyph = substr($binGlyphsData, $offset);
            $glyphList[$index] = $this->parseGlyph($binGlyph);
// dump($binGlyph);
        }

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
            $record = unpack($unpackFormat, $binTableRecords);
            $binTableRecords = substr($binTableRecords, 4 + 4 + 4 + 4);

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

    protected function unpackBinData($format, $binData)
    {
        $unpackFormat = '';
        foreach ($format as $name => $param) {
            if (!empty($unpackFormat)) {
                $unpackFormat .= '/';
            }
            $unpackFormat .= $param[0].$param[1].$name;
        }
        $unpacked = unpack($unpackFormat, $binData);

        foreach ($unpacked as $name => &$value) {
            $formatParam = $format[$name];
            if (array_key_exists(2, $formatParam)) {
                switch ($formatParam[0]) {
                    case 'n':
                        if ($value > 0x7FFF) {
                            $value = -(0x8000 - ($value & 0x7fff));
                        }
                        break;

                    case 'N':
                    if ($value > 0x7FFFFFFF) {
                        $value = -(0x80000000 - ($value & 0x7FFFFFFF));
                    }
                        break;
                }
            }
        }
        unset($value);

        return $unpacked;
    }

    protected function parseCmap($binCmap)
    {
        $formatCmap = $this->fileFormat['cmap'];

        $header = $this->unpackBinData($formatCmap['cmapHeader'], $binCmap);

        $encodingRecords = [];
        $count = $header['numTables'];
        for ($i = 0; $i < $count; $i++) {
            $recordInfo = $this->unpackBinData($formatCmap['encodingRecord'], substr($binCmap, 4 + ($i * 8)));
            $encodingRecords[] = $recordInfo;

            $subTables[] = $this->parseCmapSubTable($recordInfo, $binCmap);
        }
// dd($encodingRecord);


        return [
            'header' => $header,
            'encodingRecords' => $encodingRecords,
            'subTables' => $subTables,
        ];
    }



	protected function parseCmapSubTable($encodingRecord, $binCmap)
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
			$binSubTableBody = substr($binSubTableBody, $count * 2);

// dump($idRangeOffsetList);
			$offsetCount = count($idRangeOffsetList);
			foreach ($idRangeOffsetList as $index => &$offset) {
				if ($offset > 0) {
					$offset = ($offset / 2) - ($offsetCount - $index);
				} else {
					$offset = -1;
				}
			}
			unset($offset);

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
// dd($len);
			if ($len > 0) {
				$glyphIdArray = array_values(unpack("n{$len}", $binSubTableBody));
			} else {
				$glyphIdArray = [];
			}
// dd($glyphIdArray);
			return [
				'header' => $subHeader,
				'body' => $subTableBody,
				'glyphIdArray' => $glyphIdArray,
			];
		}

		return null;
	}

	protected function parseGlyph($binGlyph)
	{
// dump(strlen($binGlyph));

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

	protected function parseHorizontalHeaderTable($binHhea)
	{
		$horizontalHeaderTable = unpack('nmajorVersion/nminorVersion/nascender/ndescender/nlineGap/nadvanceWidthMax/nminLeftSideBearing/nminRightSideBearing/nxMaxExtent/ncaretSlopeRise/ncaretSlopeRun/ncaretOffset/n4reserve/nmetricDataFormat/nnumberOfHMetrics', $binHhea);
		return $horizontalHeaderTable;
	}


	protected function parseHorizontalMetrix($horizontalHeaderTable, $binHmtx)
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
