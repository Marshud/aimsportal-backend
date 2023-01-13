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
        Schema::create('project_transaction_sectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_transaction_id')->constrained('project_transactions')->onDelete('cascade');
            $table->string('vocabulary');
            $table->string('vocabulary_uri')->nullable();
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
        Schema::dropIfExists('project_transaction_sectors');
    }
};
