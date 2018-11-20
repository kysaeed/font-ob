<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfIndexToLocation extends Model
{
	use TraitTtfFileElement;

	const FileFormat = [
		'offset' => [['n', 'N'], 'numGlyphs'],
	];

	public static function createFromFile($head, $maxList, $binTtfFile, $offset)
	{
		// $data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);

		$locaCount = $maxList->num_glyphs; // 46個？
		if (!$head->index_to_loc_format) {
			$dataType = 'n';
		} else {
			$dataType = 'N';
		}
		$offsets = array_values(unpack("@{$offset}/{$dataType}{$locaCount}", $binTtfFile));
		return new TtfIndexToLocation([
			'offsets' => $offsets,
		]);
	}

	protected $fillable = [
		'offsets',
	];
}
