<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lte_site_survey_remarks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lte_site_survey_id');
            $table->unsignedInteger('user_id');

            $table->text('remark');
            $table->string('file_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('original_name')->nullable();

            $table->timestamps();

            $table->index(['lte_site_survey_id', 'created_at']);
            $table->foreign('lte_site_survey_id')->references('id')->on('lte_site_surveys')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lte_site_survey_remarks');
    }
};

