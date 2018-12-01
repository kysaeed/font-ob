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

		'max_twilight_points' => ['n', 1],
		'max_storage' => ['n', 1],
		'max_function_defs' => ['n', 1],
		'max_instruction_defs' => ['n', 1],
		'max_stack_elements' => ['n', 1],
		'max_size_of_instructions' => ['n', 1],
		'max_component_elements' => ['n', 1],
		'max_component_depth' => ['n', 1],
	];

	public static function createFromFile($binTtfFile, $offset)
	{
		$data = self::unpackBinData(self::FileFormat, $binTtfFile, $offset);
		return new TtfMaxList($data);
	}

	public function toBinary()
	{
		return self::packAttributes(self::FileFormat, $this->getAttributes());
	}

	protected $fillable = [
		'table_version_number',
		'num_glyphs',
		'max_points',
		'max_contours',
		'max_composite_points',
		'max_composite_contours',
		'max_zones',
		'max_twilight_points',
		'max_storage',
		'max_function_defs',
		'max_instruction_defs',
		'max_stack_elements',
		'max_size_of_instructions',
		'max_component_elements',
		'max_component_depth',
	];


}
