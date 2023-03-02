<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('county_id')->constrained('counties')->onDelete('cascade');
            $table->string('name');
            $table->decimal('latitude',9,6)->nullable();
            $table->decimal('longitude',9,6)->nullable();
            $table->string('reference')->nullable();
            $table->longText('svg_points')->nullable();
            $table->string('wikidataid')->nullable();
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
        Schema::dropIfExists('payams');
    }
};
