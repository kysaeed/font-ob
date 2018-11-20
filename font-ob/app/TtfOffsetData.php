<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfOffsetData extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'sfntVersion' => ['N', 1],
		'numTables' => ['n', 1],
		'searchRange' => ['n', 1],
		'entrySelector' => ['n', 1],
		'rangeShift' => ['n', 1],
	];


	public static function createFromFile($binTtfFile, $offset)
	{
		$offsetData = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

		return new TtfOffsetData([
			'sfnt_version' => $offsetData['sfntVersion'],
			'num_tables' =>  $offsetData['numTables'],
			'search_range' =>  $offsetData['searchRange'],
			'entrySelector' => $offsetData['entrySelector'],
			'range_shift' =>  $offsetData['rangeShift'],
		]);
	}


	protected $fillable = [
		'sfnt_version',
		'num_tables',
		'search_range',
		'entrySelector',
		'range_shift',
	];
}
