<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketIntelligence extends Model
{
    protected $fillable = [
        'project_id',
        'industries',
        'roles',
        'company_sizes',
        'opportunity_signals',
        'discovery_keywords',
        'raw_ai_response',
    ];

    protected $casts = [
        'industries' => 'array',
        'roles' => 'array',
        'company_sizes' => 'array',
        'opportunity_signals' => 'array',
        'discovery_keywords' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
