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
        Schema::create('project_fss_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_fss_id')->constrained('project_forward_spending_surveys')->onDelete('cascade');
            $table->integer('year');
            $table->string('currency');
            $table->date('value_date');
            $table->string('value');
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
        Schema::dropIfExists('project_fss_forecasts');
    }
};
