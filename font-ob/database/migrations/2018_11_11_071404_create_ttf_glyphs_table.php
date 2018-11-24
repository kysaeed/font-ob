<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTtfGlyphsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ttf_glyphs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ttf_file_id')->unsgned()->index();
            $table->integer('glyph_index')->index();
            $table->integer('x_min');
            $table->integer('y_min');
            $table->integer('x_max');
            $table->integer('y_max');
            $table->json('coordinates');
            $table->json('instructions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ttf_glyphs');
    }
}
