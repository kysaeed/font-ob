<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfHorizontalHeaderData extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'majorVersion' => ['n', 1],
		'minorVersion' => ['n', 1],
		'ascender' => ['n', 1, true],
		'descender' => ['n', 1, true],
		'lineGap' => ['n', 1, true],
		'advanceWidthMax' => ['n', 1],
		'minLeftSideBearing' => ['n', 1, true],
		'minRightSideBearing' => ['n', 1, true],
		'xMaxExtent' => ['n', 1, true],
		'caretSlopeRise' => ['n', 1, true],
		'caretSlopeRun' => ['n', 1, true],
		'caretOffset' => ['n', 1, true],
		'reserve' => ['n', 4, true],
		'metricDataFormat' => ['n', 1, true],
		'numberOfHMetrics' => ['n', 1],
	];


	public static function createFromFile($binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

		return new TtfHorizontalHeaderData([
			'major_version' => $data['majorVersion'],
			'minor_version' => $data['minorVersion'],
			'ascender' => $data['ascender'],
			'descender' => $data['descender'],
			'line_gap' => $data['lineGap'],
			'advance_width_max' => $data['advanceWidthMax'],
			'min_left_side_bearing' => $data['minLeftSideBearing'],
			'min_right_side_bearing' => $data['minRightSideBearing'],
			'x_max_extent' => $data['xMaxExtent'],
			'caret_slope_rise' => $data['caretSlopeRise'],
			'caret_slope_run' => $data['caretSlopeRun'],
			'caret_offset' => $data['caretOffset'],
			'metric_data_format' => $data['metricDataFormat'],
			'number_of_hmetrics' => $data['numberOfHMetrics'],
		]);

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
