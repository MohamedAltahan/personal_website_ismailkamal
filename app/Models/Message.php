<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Contact-form submission (legacy table "email_inboxes"). */
class Message extends Model
{
    protected $table = 'email_inboxes';

    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'locale', 'ip', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
