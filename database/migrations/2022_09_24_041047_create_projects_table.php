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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('iati_identifier')->nullable();
            $table->foreignId('organisation_id')->nullable()->constrained('organisations'); //reporting organisation
            $table->string('activity_status')->nullable();
            $table->string('activity_scope')->nullable();
            $table->string('status')->default('active');
            $table->string('country_budget_items_vocabulary')->nullable();
            $table->string('colaboration_type_code')->nullable();
            $table->string('default_flow_type_code')->nullable();
            $table->string('default_finance_type_code')->nullable();
            $table->string('default_tied_status')->nullable();
            $table->double('capital_spend_percentage')->default(0);
            $table->boolean('conditions_attached')->default(false);
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
        Schema::dropIfExists('projects');
    }
};
