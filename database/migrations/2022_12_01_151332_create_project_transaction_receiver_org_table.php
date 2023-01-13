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
        Schema::create('project_transaction_receiver_org', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_transaction_id')->constrained('project_transactions')->onDelete('cascade');
            $table->string('ref');
            $table->string('receiver_activity_id');
            $table->string('type');
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
        Schema::dropIfExists('project_transaction_receiver_org');
    }
};
