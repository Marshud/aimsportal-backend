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
        Schema::create('project_legacy_data', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('iati_xml_id')->default('iati-activities/iati-activity/legacy-data');
            $table->string('name');
            $table->string('value');
            $table->string('iati_equivalent');
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
        Schema::dropIfExists('project_legacy_data');
    }
};
