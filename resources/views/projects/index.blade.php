@extends('layouts.app')
@section('title','Proyectos')
@section('content')
<div class="toolbar">
    <div>
        <p>Estado y avance de todos tus desarrollos.</p>
        <div class="project-tabs">
            <a class="{{ $view === 'active' ? 'active' : '' }}" href="{{ route('projects.index',['view'=>'active']) }}">Activos <span>{{ $activeCount }}</span></a>
            <a class="{{ $view === 'all' ? 'active' : '' }}" href="{{ route('projects.index',['view'=>'all']) }}">Todos</a>
            <a class="{{ $view === 'archived' ? 'active' : '' }}" href="{{ route('projects.index',['view'=>'archived']) }}">Archivados <span>{{ $archivedCount }}</span></a>
        </div>
    </div>
    <a class="btn" href="{{route('projects.create')}}">+ Nuevo proyecto</a>
</div>

<div class="cards">
@forelse($projects as $p)
    <a class="card" href="{{route('projects.show',$p)}}">
        <div class="card-top"><span class="badge">{{$p->status}}</span>@if($p->archived)<span class="badge archived-badge">ARCHIVADO</span>@endif</div>
        <h3>{{$p->name}}</h3>
        <p>{{$p->description}}</p>
        <div class="progress"><i style="width:{{$p->progress}}%"></i></div>
        <strong>{{$p->progress}}%</strong> <small>· {{$p->auto_progress?'automático':'manual'}}</small>
    </a>
@empty
    <div class="panel empty wide">
        @if($view === 'archived')
            No hay proyectos archivados.
        @else
            Aún no hay proyectos en esta vista.
        @endif
    </div>
@endforelse
</div>
@endsection
