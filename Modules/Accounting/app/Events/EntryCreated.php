<?php

namespace Modules\Accounting\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Accounting\App\Models\JournalEntries;

class EntryCreated
{
    use Dispatchable, SerializesModels;

    public $entry;

    /**
     * Create a new event instance.
     */
    public function __construct(JournalEntries $entry)
    {
        info('coming inside events');
        $this->entry = $entry;
    }
}
