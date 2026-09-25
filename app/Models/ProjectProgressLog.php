<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class ProjectProgressLog extends Model { protected $fillable=['project_id','progress','source']; public function project(){return $this->belongsTo(Project::class);} }
