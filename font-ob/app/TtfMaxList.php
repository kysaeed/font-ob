<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfMaxList extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'tableVersionNumber' => ['N', 1],
		'numGlyphs' => ['n', 1],
		'maxPoints' => ['n', 1],

		'maxContours' => ['n', 1],
		'maxCompositePoints' => ['n', 1],
		'maxCompositeContours' => ['n', 1],
		'maxZones' => ['n', 1],
	];

	public static function createFromFile($binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

		return new TtfMaxList([
			'table_version_number' => $data['tableVersionNumber'],
			'num_glyphs' => $data['numGlyphs'],
			'max_points' => $data['maxPoints'],
		]);
	}

	protected $fillable = [
		'table_version_number',
		'num_glyphs',
		'max_points',
	];


}
