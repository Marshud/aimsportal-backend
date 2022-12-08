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
        Schema::create('codelist_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('codelist_id')->constrained('codelists')->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->string('description')->nullable();
            $table->string('lang')->default('en');
            $table->timestamps();
            $table->unique(['codelist_id', 'code', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('codelist_options');
    }
};
