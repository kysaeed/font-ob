<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class TtfGlyph extends Model
{
	public function ttfFile()
	{
		return $this->belognsTo('FontObscure\TtfFile');
	}

	public static function createFromFileData()
	{
		return null;
	}

	protected $fillable = [
		'glyph_index',
		'numberOfContours',
		'xMin',
		'yMin',
		'xMax',
		'yMax',
		'coordinates',
		'instructions',
	];
}
