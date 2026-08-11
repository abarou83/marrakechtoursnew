<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'reference')) {
                $table->string('reference')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('bookings', 'pricing_id')) {
                $table->foreignId('pricing_id')->nullable()->after('tour_id')->constrained('tour_pricings')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'travel_date')) {
                $table->date('travel_date')->nullable()->after('preferred_date');
            }
            if (!Schema::hasColumn('bookings', 'accommodation_total')) {
                $table->decimal('accommodation_total', 10, 2)->default(0)->after('addons_total');
            }
            if (!Schema::hasColumn('bookings', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('accommodation_total');
            }
            if (!Schema::hasColumn('bookings', 'total_ht')) {
                $table->decimal('total_ht', 10, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('bookings', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('total_ht');
            }
            if (!Schema::hasColumn('bookings', 'total_ttc')) {
                $table->decimal('total_ttc', 10, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('bookings', 'currency')) {
                $table->string('currency', 3)->default('EUR')->after('total_ttc');
            }
            if (!Schema::hasColumn('bookings', 'exchange_rate')) {
                $table->decimal('exchange_rate', 10, 6)->default(1)->after('currency');
            }
            if (! Schema::hasColumn('bookings', 'promo_code_id')) {
                if (Schema::hasTable('promo_codes')) {
                    $table->foreignId('promo_code_id')->nullable()->after('exchange_rate')->constrained('promo_codes')->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('promo_code_id')->nullable()->after('exchange_rate');
                }
            }
            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('status');
            }
            if (!Schema::hasColumn('bookings', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('bookings', 'payment_provider')) {
                $table->string('payment_provider')->nullable()->after('payment_intent_id');
            }
            if (!Schema::hasColumn('bookings', 'country_code')) {
                $table->string('country_code', 2)->nullable()->after('payment_provider');
            }
            if (!Schema::hasColumn('bookings', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('guest_phone');
            }
            if (!Schema::hasColumn('bookings', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('bookings', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('bookings', 'special_requests')) {
                $table->text('special_requests')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('bookings', 'price_breakdown')) {
                $table->json('price_breakdown')->nullable()->after('special_requests');
            }
            if (!Schema::hasColumn('bookings', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('price_breakdown');
            }
            if (!Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('confirmed_at');
            }
            if (!Schema::hasColumn('bookings', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
            if (!Schema::hasColumn('bookings', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('cancellation_reason');
            }
            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('refunded_at');
            }
            if (!Schema::hasColumn('bookings', 'voucher_path')) {
                $table->string('voucher_path')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('bookings', 'locale')) {
                $table->string('locale', 5)->default('fr')->after('voucher_path');
            }

            $table->index('reference');
            $table->index(['status', 'payment_status']);
            $table->index('travel_date');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['reference']);
            $table->dropIndex(['status', 'payment_status']);
            $table->dropIndex(['travel_date']);

            $columns = [
                'reference', 'pricing_id', 'travel_date', 'accommodation_total', 'discount_amount',
                'total_ht', 'tax_amount', 'total_ttc', 'currency', 'exchange_rate', 'promo_code_id',
                'payment_status', 'payment_intent_id', 'payment_provider', 'country_code',
                'customer_name', 'customer_email', 'customer_phone', 'special_requests',
                'price_breakdown', 'confirmed_at', 'cancelled_at', 'cancellation_reason',
                'refunded_at', 'refund_amount', 'voucher_path', 'locale'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
