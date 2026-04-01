<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Drop any existing FK before altering column type to avoid MySQL 3780
        $existingFks = DB::select(<<<SQL
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'customers'
              AND COLUMN_NAME = 'account_manager_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        SQL);

        foreach ($existingFks as $fk) {
            $name = $fk->CONSTRAINT_NAME ?? $fk['CONSTRAINT_NAME'] ?? null;
            if ($name) {
                try {
                    DB::statement("ALTER TABLE customers DROP FOREIGN KEY `$name`");
                } catch (\Throwable $e) {
                    // ignore if already removed
                }
            }
        }

        // 2) Add new columns if missing
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'address')) {
                $table->string('address')->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('customers', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'account_manager_id')) {
                $table->unsignedBigInteger('account_manager_id')->nullable()->after('id');
            }
        });

        // 3) Ensure account_manager_id is BIGINT UNSIGNED to match account_managers.id
        if (Schema::hasColumn('customers', 'account_manager_id')) {
            DB::statement('ALTER TABLE customers MODIFY account_manager_id BIGINT UNSIGNED NULL');
        }

        // 4) Re-add FK to account_managers(id) if not present
        $fkAfterAlter = DB::select(<<<SQL
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'customers'
              AND COLUMN_NAME = 'account_manager_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        SQL);

        if (empty($fkAfterAlter)) {
            DB::statement('ALTER TABLE customers ADD CONSTRAINT customers_account_manager_id_foreign FOREIGN KEY (account_manager_id) REFERENCES account_managers(id)');
        }

        // Drop deprecated location columns if present
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'city_id')) {
                $table->dropColumn('city_id');
            }
            if (Schema::hasColumn('customers', 'suburb_id')) {
                $table->dropColumn('suburb_id');
            }
            if (Schema::hasColumn('customers', 'pop_id')) {
                $table->dropColumn('pop_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Recreate dropped columns
            if (!Schema::hasColumn('customers', 'city_id')) {
                $table->unsignedInteger('city_id')->nullable()->after('account_manager_id');
            }
            if (!Schema::hasColumn('customers', 'suburb_id')) {
                $table->unsignedInteger('suburb_id')->nullable()->after('city_id');
            }
            if (!Schema::hasColumn('customers', 'pop_id')) {
                $table->unsignedInteger('pop_id')->nullable()->after('suburb_id');
            }

            // Drop new columns
            if (Schema::hasColumn('customers', 'contact_number')) {
                $table->dropColumn('contact_number');
            }
            if (Schema::hasColumn('customers', 'address')) {
                $table->dropColumn('address');
            }
        });

        // Drop any FK on account_manager_id and revert column type
        $existingFks = DB::select(<<<SQL
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'customers'
              AND COLUMN_NAME = 'account_manager_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        SQL);
        foreach ($existingFks as $fk) {
            $name = $fk->CONSTRAINT_NAME ?? $fk['CONSTRAINT_NAME'] ?? null;
            if ($name) {
                try {
                    DB::statement("ALTER TABLE customers DROP FOREIGN KEY `$name`");
                } catch (\Throwable $e) {}
            }
        }

        if (Schema::hasColumn('customers', 'account_manager_id')) {
            DB::statement('ALTER TABLE customers MODIFY account_manager_id INT UNSIGNED NULL');
        }

        // Re-add foreign key pointing back to users
        DB::statement('ALTER TABLE customers ADD CONSTRAINT customers_account_manager_id_foreign FOREIGN KEY (account_manager_id) REFERENCES users(id)');
    }
};