@extends('layouts.app')
@section('title',$project->exists?'Editar proyecto':'Nuevo proyecto')
@section('content')
<form class="panel form" method="POST" action="{{$project->exists?route('projects.update',$project):route('projects.store')}}">
@csrf @if($project->exists)@method('PUT')@endif
<label>Nombre<input name="name" value="{{old('name',$project->name)}}" required></label>
<label>Estado<select name="status">@foreach(['IDEA','PLANIFICACION','DESARROLLO','QA','PAUSADO','PRODUCCION','MANTENIMIENTO','FINALIZADO'] as $s)<option @selected(old('status',$project->status)==$s)>{{$s}}</option>@endforeach</select></label>
<label>Prioridad<select name="priority">@foreach(['BAJA','MEDIA','ALTA','CRITICA'] as $s)<option @selected(old('priority',$project->priority??'MEDIA')==$s)>{{$s}}</option>@endforeach</select></label>
<label>Fecha de inicio<input type="date" name="started_at" value="{{old('started_at',optional($project->started_at)->format('Y-m-d'))}}"></label>

<div class="wide progress-mode-box">
    <div>
        <strong>Avance del proyecto</strong>
        <p class="muted">Lo más práctico es actualizar el porcentaje manualmente. Usa hitos automáticos solo si realmente los necesitas.</p>
    </div>
    <label class="toggle-line"><input type="hidden" name="auto_progress" value="0"><input id="auto_progress" type="checkbox" name="auto_progress" value="1" @checked(old('auto_progress',$project->auto_progress??false))> Calcular automáticamente usando hitos</label>
</div>
<label>Avance % <small id="progress-help">Actualízalo cuando quieras.</small><input id="progress" type="number" min="0" max="100" name="progress" value="{{old('progress',$project->progress??0)}}"></label>
<label><span>Modo actual</span><div id="progress-mode-label" class="readonly-box"></div></label>

<label>Cliente / organización<input name="client" value="{{old('client',$project->client)}}"></label>
<label>Tecnologías<input name="technology" value="{{old('technology',$project->technology)}}"></label>
<label>Entorno<input name="environment" value="{{old('environment',$project->environment)}}"></label>
<label>Ruta local<input name="local_path" value="{{old('local_path',$project->local_path)}}" placeholder="C:\xampp\htdocs\..."></label>
<label class="wide">Repositorio<input name="repository_url" value="{{old('repository_url',$project->repository_url)}}"></label>
<label class="wide">Descripción<textarea name="description">{{old('description',$project->description)}}</textarea></label>
<label class="wide">Próximo paso<textarea name="next_step">{{old('next_step',$project->next_step)}}</textarea></label>
<label class="wide">Notas técnicas<textarea name="notes">{{old('notes',$project->notes)}}</textarea></label>
<button class="btn">Guardar proyecto</button>
</form>
<script>
(() => {
    const auto = document.getElementById('auto_progress');
    const progress = document.getElementById('progress');
    const label = document.getElementById('progress-mode-label');
    const help = document.getElementById('progress-help');
    const sync = () => {
        if (auto.checked) {
            progress.readOnly = true;
            label.textContent = 'Automático · requiere al menos un hito';
            help.textContent = 'Se calculará con los hitos.';
        } else {
            progress.readOnly = false;
            label.textContent = 'Manual · recomendado';
            help.textContent = 'Actualízalo cuando quieras.';
        }
    };
    auto.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
