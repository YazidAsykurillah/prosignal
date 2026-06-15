<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGeneration extends Model
{
    protected $fillable = [
        'project_id',
        'provider',
        'model',
        'type',
        'prompt',
        'response',
        'status',
        'tokens_used',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
