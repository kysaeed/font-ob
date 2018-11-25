<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfHead extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'major_version' => ['n', 1],
		'minor_version' => ['n', 1],
		'font_revision' => ['N', 1],
		'check_sum_adjustment' => ['N', 1],
		'magic_number' => ['N', 1],
		'flags' => ['n', 1],
		'units_per_em' => ['n', 1],
		'created' => ['J', 1],
		'modified' => ['J', 1],
		'x_min' => ['n', 1, true],
		'y_min' => ['n', 1, true],
		'x_max' => ['n', 1, true],
		'y_max' => ['n', 1, true],
		'mac_style' => ['n', 1],
		'lowest_rec_ppem' => ['n', 1],
		'font_direction_hint' => ['n', 1, true],
		'index_to_loc_format' => ['n', 1, true],
		'glyph_data_format' => ['n', 1, true],
	];

	public static function createFromFile($binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);
		return new TtfHead($data);
	}

	public function toBinary()
	{
		return self::packAttributes(self::FileFormat, $this->getAttributes());
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
		'font_direction_hint',
		'index_to_loc_format',
		'glyph_data_format',	// TODO: フォーマットの種別を確認!!
	];
}
