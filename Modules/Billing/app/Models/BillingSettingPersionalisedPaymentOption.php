<?php

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\General\App\Models\Company;
use Modules\General\App\Models\Branch;
use App\Models\Payments\PaymentOption;
use App\Models\User;

class BillingSettingPersionalisedPaymentOption extends Model
{
    use HasFactory;

    protected $table = 'billing_setting_persionalised_payment_options';

    protected $fillable = [
        'user_id',
        'company_id',
        'branch_id',
        'payment_options_id',
    ];

    /**
     * Automatically cast the JSON column to an array.
     */
    protected $casts = [
        'payment_options_id' => 'array',
    ];

    /**
     * Relationships
     */

    // 🔗 User relation
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔗 Company relation (optional – only if you have a Company model)
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    // 🔗 Branch relation (optional – only if you have a Branch model)
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // 🔗 Payment Option relation (for convenience)
    // You can use this if you want to fetch multiple related PaymentOption models
    public function paymentOptions()
    {
        return PaymentOption::whereIn('id', $this->payment_options_id ?? [])->get();
    }
}
