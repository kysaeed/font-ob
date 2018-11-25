<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfMaxList extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'table_version_number' => ['N', 1],
		'num_glyphs' => ['n', 1],
		'max_points' => ['n', 1],

		'max_contours' => ['n', 1],
		'max_composite_points' => ['n', 1],
		'max_composite_contours' => ['n', 1],
		'max_zones' => ['n', 1],
	];

	public static function createFromFile($binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

		return new TtfMaxList([
			'table_version_number' => $data['table_version_number'],
			'num_glyphs' => $data['num_glyphs'],
			'max_points' => $data['max_points'],
		]);
	}

	protected $fillable = [
		'table_version_number',
		'num_glyphs',
		'max_points',
	];


}
