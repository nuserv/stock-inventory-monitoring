<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchEmailAnnouncementBatch extends Model
{
    protected $guarded = [];

    protected $casts = [
        'recipients' => 'array',
    ];

    protected $dates = [
        'sent_at',
    ];

    public function announcement()
    {
        return $this->belongsTo(BranchEmailAnnouncement::class, 'announcement_id');
    }
}
