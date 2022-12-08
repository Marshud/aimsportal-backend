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
        Schema::create('project_document_links', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->string('iati_xml_id')->default('iati-activities/iati-activity/document-link');
            $table->string('element_type')->nullable();
            $table->unsignedBigInteger('element_id')->nullable();
            $table->string('url')->nullable();
            $table->string('format');
            $table->date('document_date');
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
        Schema::dropIfExists('project_document_links');
    }
};
