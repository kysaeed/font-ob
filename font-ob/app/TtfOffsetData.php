<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfOffsetData extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'sfnt_version' => ['N', 1],
		'num_tables' => ['n', 1],
		'search_range' => ['n', 1],
		'entry_selector' => ['n', 1],
		'range_shift' => ['n', 1],
	];


	public static function createFromFile($binTtfFile, $offset)
	{
		$offsetData = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

		return new TtfOffsetData($offsetData);
	}

	public function toBinary()
	{
		return self::packAttributes(self::FileFormat, $this->getAttributes());
	}

	protected $fillable = [
		'sfnt_version',
		'num_tables',
		'search_range',
		'entry_selector',
		'range_shift',
	];
}
