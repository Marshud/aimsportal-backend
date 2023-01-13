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
        Schema::create('project_planned_disbursement_receiver_org', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_planned_disbursement_id');
            $table->foreign('project_planned_disbursement_id', 'project_disbursement_receiver')->references('id')->on('project_planned_disbursements')->onDelete('cascade');
            $table->string('ref');
            $table->string('type');
            $table->string('receiver_activity_id')->nullable();
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
        Schema::dropIfExists('project_planned_disbursement_receiver_org');
    }
};
