<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfTableRecord extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'tag' => ['A', 4],
		'sum' => ['N', 1],
		'offset' => ['N', 1],
		'length' => ['N', 1],
	];

	public static function createFromFile($binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

		return new TtfTableRecord([
			'tag' => $data['tag'],
			'sum' => $data['sum'],
			'offset' => $data['offset'],
			'length' => $data['length'],
		]);
	}


	protected $fillable = [
		'tag',
		'sum',
		'offset',
		'length',
	];
}
