<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'channel')) {
                $table->string('channel', 30)->default('direct')->after('locale');
            }
            if (!Schema::hasColumn('bookings', 'utm_source')) {
                $table->string('utm_source')->nullable()->after('channel');
            }
            if (!Schema::hasColumn('bookings', 'utm_medium')) {
                $table->string('utm_medium')->nullable()->after('utm_source');
            }
            if (!Schema::hasColumn('bookings', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable()->after('utm_medium');
            }
            if (!Schema::hasColumn('bookings', 'referral_code')) {
                $table->string('referral_code', 30)->nullable()->after('utm_campaign');
            }
            if (!Schema::hasColumn('bookings', 'gift_card_id')) {
                $table->foreignId('gift_card_id')->nullable()->after('referral_code')
                    ->constrained('gift_cards')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'gift_card_amount')) {
                $table->decimal('gift_card_amount', 10, 2)->default(0)->after('gift_card_id');
            }
            if (!Schema::hasColumn('bookings', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->nullable()->after('gift_card_amount');
            }
            if (!Schema::hasColumn('bookings', 'payment_type')) {
                $table->string('payment_type', 20)->default('full')->after('deposit_amount');
            }
            if (!Schema::hasColumn('bookings', 'review_requested_at')) {
                $table->timestamp('review_requested_at')->nullable()->after('payment_type');
            }
            if (!Schema::hasColumn('bookings', 'last_reminder_sent_at')) {
                $table->timestamp('last_reminder_sent_at')->nullable()->after('review_requested_at');
            }
            if (!Schema::hasColumn('bookings', 'reminder_type')) {
                $table->string('reminder_type', 10)->nullable()->after('last_reminder_sent_at');
            }

            $table->index('channel');
            $table->index(['utm_source', 'utm_medium']);
        });

        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'referral_code')) {
                $table->string('referral_code', 20)->nullable()->unique()->after('preferred_currency');
            }
        });

        if (!Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('name')->nullable();
                $table->string('locale', 5)->default('fr');
                $table->string('source', 50)->default('footer');
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamp('subscribed_at');
                $table->timestamp('unsubscribed_at')->nullable();
                $table->string('unsubscribe_token', 64)->unique();
                $table->timestamps();

                $table->index(['email', 'unsubscribed_at']);
            });
        }

        if (!Schema::hasTable('abandoned_carts')) {
            Schema::create('abandoned_carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
                $table->string('email');
                $table->string('customer_name')->nullable();
                $table->date('travel_date')->nullable();
                $table->unsignedTinyInteger('adults')->default(1);
                $table->unsignedTinyInteger('children')->default(0);
                $table->decimal('total_amount', 10, 2)->nullable();
                $table->string('currency', 3)->default('EUR');
                $table->json('cart_data')->nullable();
                $table->boolean('marketing_opt_in')->default(false);
                $table->timestamp('recovery_email_sent_at')->nullable();
                $table->timestamp('converted_at')->nullable();
                $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();

                $table->index(['email', 'recovery_email_sent_at']);
                $table->index('converted_at');
            });
        }

        if (!Schema::hasTable('referral_usages')) {
            Schema::create('referral_usages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('referrer_client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('referred_client_id')->nullable()->constrained('clients')->nullOnDelete();
                $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
                $table->string('code', 20);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('reward_amount', 10, 2)->default(0);
                $table->string('status', 20)->default('pending');
                $table->timestamp('rewarded_at')->nullable();
                $table->timestamps();

                $table->index(['code', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_usages');
        Schema::dropIfExists('abandoned_carts');
        Schema::dropIfExists('newsletter_subscribers');

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $columns = [
                'channel', 'utm_source', 'utm_medium', 'utm_campaign', 'referral_code',
                'gift_card_id', 'gift_card_amount', 'deposit_amount', 'payment_type',
                'review_requested_at', 'last_reminder_sent_at', 'reminder_type',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
