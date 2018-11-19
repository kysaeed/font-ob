<?php

namespace FontObscure;

use Illuminate\Database\Eloquent\Model;

class CmapSubData extends Model
{
    public function ttfFile()
    {
        // TODO:
    }

    protected $fillable = [
		'platform',
		'encoding',
	];
}
