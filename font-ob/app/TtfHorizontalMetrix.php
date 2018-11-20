<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfHorizontalMetrix extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'advanceWidth' => ['n', 1],
		'lsb' => ['n', 1],
	];

	public static function createFromFile($header, $binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

// dd($data);
		return new TtfHorizontalMetrix([
			'advance_width' => $data['advanceWidth'],
			'lsb' => $data['lsb'],
		]);
	}

	protected $fillable = [
		'advance_width',
		'lsb',
	];
}
