<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorKyc extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'business_name', 'business_type',
        'pan_number', 'gst_number', 'bank_account_number',
        'bank_ifsc_code', 'bank_name', 'document_type',
        'document_front_path', 'document_back_path', 'selfie_path',
        'status', 'rejection_reason', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(\Modules\General\App\Models\Branch::class, 'branch_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
