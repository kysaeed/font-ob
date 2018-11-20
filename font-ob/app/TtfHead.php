<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfHead extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
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
	];

	public static function createFromFile($binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

		return new TtfHead([
			'major_version'=> $data['majorVersion'],
			'minor_version'=> $data['minorVersion'],
			'font_revision'=> $data['fontRevision'],
			'check_sum_adjustment'=> $data['checkSumAdjustment'],
			'magic_number'=> $data['magicNumber'],
			'flags'=> $data['flags'],
			'units_per_em'=> $data['unitsPerEm'],
			'created'=> $data['created'],
			'modified'=> $data['modified'],
			'x_min'=> $data['xMin'],
			'y_min'=> $data['yMin'],
			'x_max'=> $data['xMax'],
			'y_max'=> $data['yMax'],
			'mac_style'=> $data['macStyle'],
			'lowest_rec_ppem'=> $data['lowestRecPPEM'],
			'font_direction_hint'=> $data['fontDirectionHint'],
			'index_to_loc_format'=> $data['indexToLocFormat'],
			'glyph_data_format'=> $data['glyphDataFormat'],	// TODO: 要確認!!
		]);
	}

	protected $fillable = [
		'major_version',
		'minor_version',
		'font_revision',
		'check_sum_adjustment',
		'magic_number',
		'flags',
		'units_per_em',
		'created',
		'modified',
		'x_min',
		'y_min',
		'x_max',
		'y_max',
		'mac_style',
		'lowest_rec_ppem',
		'font_directionHint',
		'index_to_loc_format',
		'glyph_data_format',	// TODO: 要確認!!
	];
}
