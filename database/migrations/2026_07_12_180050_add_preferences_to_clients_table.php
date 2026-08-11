<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'preferred_language')) {
                $table->string('preferred_language', 10)->nullable()->after('google_id');
            }
            if (!Schema::hasColumn('clients', 'preferred_currency')) {
                $table->string('preferred_currency', 10)->nullable()->after('preferred_language');
            }
            if (!Schema::hasColumn('clients', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('preferred_currency');
            }
            if (!Schema::hasColumn('clients', 'avatar')) {
                $table->string('avatar')->nullable()->after('notification_preferences');
            }
            if (!Schema::hasColumn('clients', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('avatar');
            }
        });

        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['client_id', 'tour_id']);
            });
        } else {
            if (Schema::hasColumn('wishlists', 'user_id') && !Schema::hasColumn('wishlists', 'client_id')) {
                Schema::table('wishlists', function (Blueprint $table) {
                    $table->renameColumn('user_id', 'client_id');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $columns = ['preferred_language', 'preferred_currency', 'notification_preferences', 'avatar', 'last_login_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
