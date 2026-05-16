<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lte_site_survey_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lte_site_survey_id')->constrained('lte_site_surveys')->onDelete('cascade');
            $table->string('label');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->string('original_name')->nullable();
            $table->timestamps();

            $table->index(['lte_site_survey_id', 'label']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lte_site_survey_photos');
    }
};

