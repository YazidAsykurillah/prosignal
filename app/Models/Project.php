<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'industry',
        'location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marketIntelligence()
    {
        return $this->hasOne(MarketIntelligence::class);
    }

    public function aiGenerations()
    {
        return $this->hasMany(AiGeneration::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'project_companies');
    }

    public function discoveries()
    {
        return $this->hasMany(Discovery::class);
    }

    public function discoveryRuns()
    {
        return $this->hasMany(DiscoveryRun::class);
    }
}
