<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfHorizontalHeaderData extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'major_version' => ['n', 1],
		'minor_version' => ['n', 1],
		'ascender' => ['n', 1, true],
		'descender' => ['n', 1, true],
		'line_gap' => ['n', 1, true],
		'advance_width_max' => ['n', 1],
		'min_left_side_bearing' => ['n', 1, true],
		'min_right_side_bearing' => ['n', 1, true],
		'x_max_extent' => ['n', 1, true],
		'caret_slope_rise' => ['n', 1, true],
		'caret_slope_run' => ['n', 1, true],
		'caret_offset' => ['n', 1, true],
		'reserve' => ['n', 4, true],
		'metric_data_format' => ['n', 1, true],
		'number_of_hmetrics' => ['n', 1],
	];


	public static function createFromFile($binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);
		return new TtfHorizontalHeaderData($data);
	}


	protected $fillable = [
		'major_version',
		'minor_version',
		'ascender',
		'descender',
		'line_gap',
		'advance_width_max',
		'min_left_side_bearing',
		'min_right_side_bearing',
		'x_max_extent',
		'caret_slope_rise',
		'caret_slope_run',
		'caret_offset',
		'metric_data_format',
		'number_of_hmetrics',
	];

}
