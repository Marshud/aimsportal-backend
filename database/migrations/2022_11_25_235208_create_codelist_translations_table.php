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
        Schema::create('codelist_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('codelist_option_id')->constrained('codelist_options')->onDelete('cascade');
            $table->string('lang');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->unique(['codelist_option_id', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('codelist_translations');
    }
};
