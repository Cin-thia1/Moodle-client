<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $notification_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 */
class NotificationRead extends Model
{
    protected $table = 'notification_reads';

    protected $fillable = [
        'user_id',
        'notification_key',
    ];

    /**
     * Get the user that read the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
