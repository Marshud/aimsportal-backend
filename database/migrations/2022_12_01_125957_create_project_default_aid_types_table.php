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
        Schema::create('project_default_aid_types', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('iati_xml_id')->default('iati-activities/iati-activity/default-aid-type');
            $table->string('vocabulary');
            $table->string('code');
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
        Schema::dropIfExists('project_default_aid_types');
    }
};
