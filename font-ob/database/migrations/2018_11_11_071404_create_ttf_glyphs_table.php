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
            $table->integer('glyph_index')->index();
            $table->integer('numberOfContours');
            $table->integer('xMin');
            $table->integer('yMin');
            $table->integer('xMax');
            $table->integer('yMax');
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
