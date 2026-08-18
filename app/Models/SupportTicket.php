<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }
}
