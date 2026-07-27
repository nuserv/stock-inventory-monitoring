<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchEmailAnnouncement extends Model
{
    protected $guarded = [];

    protected $dates = [
        'sent_at',
    ];

    public function batches()
    {
        return $this->hasMany(BranchEmailAnnouncementBatch::class, 'announcement_id');
    }
}
