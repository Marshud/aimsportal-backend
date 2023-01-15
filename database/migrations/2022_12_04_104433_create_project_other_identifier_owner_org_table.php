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
        Schema::create('project_other_identifier_owner_org', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_other_identifier_id');
            $table->foreign('project_other_identifier_id', 'project_other_owner')->references('id')->on('project_other_identifier')->onDelete('cascade');
            $table->string('ref');
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
        Schema::dropIfExists('project_other_identifier_owner_org');
    }
};
