<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class Plan extends Model { protected $fillable=['project_id','title','type','status','weight','due_date','notes']; protected $casts=['due_date'=>'date','weight'=>'integer']; public function project(){return $this->belongsTo(Project::class);} protected static function booted(){static::saved(fn($m)=>$m->project?->recalculateProgress());static::deleted(fn($m)=>$m->project?->recalculateProgress());} }
