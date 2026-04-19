<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'event_id', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // لمين هالإشعار
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // مرتبط بأي فعالية
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // الإشعارات غير المقروءة
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // تحديد كمقروء
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
```
