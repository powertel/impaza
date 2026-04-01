<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE faults MODIFY COLUMN phoneNumber VARCHAR(64) NULL AFTER customer_id");
        DB::statement("ALTER TABLE users MODIFY COLUMN phonenumber VARCHAR(64) NULL AFTER email");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE faults MODIFY COLUMN phoneNumber VARCHAR(64) NULL");
        DB::statement("ALTER TABLE users MODIFY COLUMN phonenumber VARCHAR(64) NULL");
    }
};