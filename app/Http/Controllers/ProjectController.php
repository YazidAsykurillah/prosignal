<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Project::class);

        $projects = auth()->user()->projects()->latest()->paginate(10);

        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Project::class);

        return Inertia::render('Projects/Create');
    }

    public function store(StoreProjectRequest $request)
    {
        Gate::authorize('create', Project::class);

        auth()->user()->projects()->create($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load([
            'marketIntelligence',
            'aiGenerations' => function ($query) {
                $query->where('type', 'market_intelligence')->latest()->take(1);
            },
            'discoveries.company',
            'discoveryRuns' => function ($query) {
                $query->latest()->take(1);
            }
        ]);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'latestGeneration' => $project->aiGenerations->first(),
            'latestDiscoveryRun' => $project->discoveryRuns->first(),
        ]);
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => $project
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        Gate::authorize('update', $project);

        $project->update($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
