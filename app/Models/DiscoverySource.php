<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscoverySource extends Model
{
    protected $fillable = [
        'project_id',
        'keyword',
        'url',
        'title',
        'relevance_score',
        'analyzed',
        'crawled_at',
        'content',
    ];

    protected $casts = [
        'analyzed' => 'boolean',
        'crawled_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
