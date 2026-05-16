<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lte_site_surveys', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('status')->default('submitted');
            $table->date('survey_date')->nullable();
            $table->string('survey_performed_by')->nullable();

            $table->string('site_name')->nullable();
            $table->string('jc_number')->nullable();
            $table->string('coordinates')->nullable();
            $table->text('physical_address')->nullable();
            $table->string('province_region')->nullable();

            $table->json('payload');
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['site_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lte_site_surveys');
    }
};
