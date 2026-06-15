<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\DiscoveryRun;
use App\Jobs\ProcessProjectDiscovery;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DiscoveryController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Project $project)
    {
        // Check if user can run discovery and owns the project
        // Assuming ProjectPolicy has an 'update' method for ownership
        $this->authorize('update', $project);
        
        if (!auth()->user()->can('discovery.run') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You do not have permission to run company discovery.');
        }

        $discoveryRun = DiscoveryRun::create([
            'project_id' => $project->id,
            'status' => 'pending',
        ]);

        ProcessProjectDiscovery::dispatch($project, $discoveryRun);

        return back()->with('success', 'Company discovery started successfully. It will process in the background.');
    }
}
