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
        Schema::table('project_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('state_id', 'project_locations')) {
                $table->foreignId('state_id')->nullable();
                $table->foreignId('county_id')->nullable();
                $table->foreignId('payam_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_locations', function (Blueprint $table) {
            //
        });
    }
};
