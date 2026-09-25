<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private function data(Request $r): array
    {
        $d = $r->validate([
            'name' => 'required|max:150',
            'description' => 'nullable',
            'status' => 'required|in:IDEA,PLANIFICACION,DESARROLLO,QA,PAUSADO,PRODUCCION,MANTENIMIENTO,FINALIZADO',
            'priority' => 'required|in:BAJA,MEDIA,ALTA,CRITICA',
            'progress' => 'nullable|integer|min:0|max:100',
            'auto_progress' => 'nullable|boolean',
            'client' => 'nullable|max:150',
            'technology' => 'nullable|max:255',
            'environment' => 'nullable|max:150',
            'local_path' => 'nullable|max:500',
            'repository_url' => 'nullable|max:2048',
            'started_at' => 'nullable|date',
            'next_step' => 'nullable',
            'notes' => 'nullable',
        ]);

        $d['auto_progress'] = $r->boolean('auto_progress');
        $d['progress'] = $d['progress'] ?? 0;
        return $d;
    }

    public function index(Request $r)
    {
        $view = $r->string('view')->toString() ?: 'active';
        $q = Project::query();

        if ($view === 'archived') {
            $q->where('archived', true);
        } elseif ($view !== 'all') {
            $view = 'active';
            $q->where('archived', false);
        }

        if ($r->filled('q')) {
            $term = $r->q;
            $q->where(fn ($x) => $x->where('name', 'like', '%'.$term.'%')->orWhere('client', 'like', '%'.$term.'%'));
        }

        return view('projects.index', [
            'projects' => $q->orderByDesc('updated_at')->get(),
            'view' => $view,
            'activeCount' => Project::where('archived', false)->count(),
            'archivedCount' => Project::where('archived', true)->count(),
        ]);
    }

    public function create()
    {
        return view('projects.form', ['project' => new Project(['priority' => 'MEDIA', 'auto_progress' => false])]);
    }

    public function store(Request $r)
    {
        $p = Project::create($this->data($r));
        AuditService::log('PROJECT_CREATED', $p->name, $p);

        $message = $p->auto_progress
            ? 'Proyecto creado. Agrega al menos un hito para calcular el avance automático, o cambia a avance manual.'
            : 'Proyecto creado.';

        return redirect()->route('projects.show', $p)->with('ok', $message);
    }

    public function show(Project $project)
    {
        $project->load(['credentials', 'urls', 'plans' => fn ($q) => $q->orderBy('status')->orderBy('due_date')]);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('projects.form', compact('project'));
    }

    public function update(Request $r, Project $project)
    {
        $before = $project->progress;
        $project->update($this->data($r));
        $project->recalculateProgress();

        if (!$project->auto_progress && $project->progress !== $before) {
            $project->progressLogs()->create(['progress' => $project->progress, 'source' => 'MANUAL']);
        }

        AuditService::log('PROJECT_UPDATED', $project->name, $project);
        return redirect()->route('projects.show', $project)->with('ok', 'Proyecto actualizado.');
    }

    public function archive(Project $project)
    {
        $project->update(['archived' => !$project->archived]);
        AuditService::log('PROJECT_ARCHIVE_TOGGLED', $project->name, $project);

        if ($project->archived) {
            return redirect()->route('projects.index', ['view' => 'archived'])
                ->with('ok', 'Proyecto archivado. Puedes restaurarlo en cualquier momento desde Archivados.');
        }

        return redirect()->route('projects.show', $project)->with('ok', 'Proyecto restaurado.');
    }
}
