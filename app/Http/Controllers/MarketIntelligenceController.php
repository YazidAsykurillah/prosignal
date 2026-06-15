<?php

namespace App\Http\Controllers;

use App\Contracts\AIProviderInterface;
use App\Jobs\GenerateMarketIntelligenceJob;
use App\Models\Project;
use App\Models\MarketIntelligence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MarketIntelligenceController extends Controller
{
    public function generate(Request $request, Project $project, AIProviderInterface $aiProvider)
    {
        // For now using simple authorization; better to use Gate::authorize('generateMarketIntelligence', $project);
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $generation = $project->aiGenerations()->create([
            'provider' => $aiProvider->getName(),
            'model' => $aiProvider->getModel(),
            'type' => 'market_intelligence',
            'status' => 'pending',
        ]);

        GenerateMarketIntelligenceJob::dispatch($project, $generation->id);

        return back()->with('success', 'Market Intelligence generation started. This may take a moment.');
    }

    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'industries' => 'array',
            'roles' => 'array',
            'company_sizes' => 'array',
            'opportunity_signals' => 'array',
            'discovery_keywords' => 'array',
        ]);

        $project->marketIntelligence()->updateOrCreate(
            ['project_id' => $project->id],
            $validated
        );

        activity()
            ->performedOn($project)
            ->log('Market Intelligence Updated');

        return back()->with('success', 'Market Intelligence updated manually.');
    }
}
