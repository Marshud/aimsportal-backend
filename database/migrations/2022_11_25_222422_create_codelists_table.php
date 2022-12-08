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
        Schema::create('codelists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('version')->default('2.0.3');
            $table->string('iati_xml_id')->nullable();
            $table->string('name_narrative')->nullable();
            $table->string('description')->nullable();
            $table->string('lang')->default('en');
            $table->boolean('is_namespaced')->default(false);
            $table->string('slug_name')->nullable();
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
        Schema::dropIfExists('codelists');
    }
};
