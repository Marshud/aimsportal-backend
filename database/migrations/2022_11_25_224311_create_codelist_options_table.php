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
            $table->string('code');
            $table->text('name');
            $table->text('description')->nullable();
            $table->string('related_codelist')->nullable();
            $table->timestamps();
            $table->unique(['codelist_id', 'code']);
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
