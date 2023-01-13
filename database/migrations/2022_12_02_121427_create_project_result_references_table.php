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
        Schema::create('project_result_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_result_id')->constrained('project_results')->onDelete('cascade');
            $table->string('code');
            $table->string('vocabulary');
            $table->string('vocabulary_uri');
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
        Schema::dropIfExists('project_result_references');
    }
};
