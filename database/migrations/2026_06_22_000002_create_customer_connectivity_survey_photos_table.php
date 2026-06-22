<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('customer_connectivity_survey_photos')) {
            Schema::drop('customer_connectivity_survey_photos');
        }

        Schema::create('customer_connectivity_survey_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_connectivity_survey_id');
            $table->foreign('customer_connectivity_survey_id', 'ccs_photos_survey_fk')
                ->references('id')
                ->on('customer_connectivity_surveys')
                ->onDelete('cascade');
            $table->string('label');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->string('original_name')->nullable();
            $table->timestamps();

            $table->index(['customer_connectivity_survey_id', 'label'], 'ccs_photos_survey_label_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_connectivity_survey_photos');
    }
};
