<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discovery extends Model
{
    protected $fillable = [
        'project_id',
        'company_id',
        'source_url',
        'keyword',
        'signal',
        'summary',
        'confidence_score',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
