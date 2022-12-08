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
        Schema::create('project_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('iati_xml_id')->default('iati-activities/iati-activity/planned-disbursement');
            $table->string('ref')->nullable();
            $table->boolean('humanitarian')->default(false);
            $table->string('transaction_type_code');
            $table->date('transaction_date');
            $table->string('value_currency');
            $table->date('value_date');
            $table->double('value_amount');
            $table->string('disbursement_channel_code')->nullable();
            $table->string('recipient_country_code')->nullable();
            $table->string('recipient_region_code')->nullable();
            $table->string('recipient_region_vocabulary')->nullable();
            $table->string('flow_type_code')->nullable();
            $table->string('finance_type_code')->nullable();
            $table->string('tied_status_code')->nullable();
            $table->string('description_narrative')->nullable();
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
        Schema::dropIfExists('project_transactions');
    }
};
