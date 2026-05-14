<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSendLog extends Model
{
    protected $table = 'email_send_log';

    protected $fillable = [
        'to_address',
        'template',
        'context_hash',
        'message_id',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
