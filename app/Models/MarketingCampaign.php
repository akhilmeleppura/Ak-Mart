<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MarketingCampaign extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'audience_filters' => 'array',
            'scheduled_at' => 'datetime',
            'stats' => 'array',
        ];
    }

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('marketing_campaigns')) {
            Schema::create('marketing_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('channel')->default('email'); // email, whatsapp, omnichannel
                $table->string('audience_type')->default('all'); // all, vip, inactive, high_value, abandoned_cart
                $table->json('audience_filters')->nullable();
                $table->string('subject')->nullable();
                $table->text('message_content');
                $table->string('status')->default('draft'); // draft, scheduled, running, completed, cancelled
                $table->timestamp('scheduled_at')->nullable();
                $table->integer('recipients_count')->default(0);
                $table->integer('sent_count')->default(0);
                $table->integer('delivered_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->json('stats')->nullable();
                $table->timestamps();
            });
        }
    }
}
