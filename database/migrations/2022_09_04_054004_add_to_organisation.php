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
        Schema::table('organisations', function (Blueprint $table) {
            if (!Schema::hasColumn('organisations','category_id')) {
                $table->bigInteger('category_id');
                $table->string('acronym');
                $table->string('contact_person_name');
                $table->string('contact_person_email');
                $table->string('address');
                $table->boolean('approved')->default(false);
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
        Schema::table('organisations', function (Blueprint $table) {
            //
        });
    }
};
