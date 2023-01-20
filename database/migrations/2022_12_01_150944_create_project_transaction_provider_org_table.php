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
        Schema::create('project_transaction_provider_org', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->nullable();
            $table->foreignId('project_transaction_id')->constrained('project_transactions')->onDelete('cascade');
            $table->string('ref')->nullable();
            $table->string('provider_activity_id')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('project_transaction_provider_org');
    }
};
