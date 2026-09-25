<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class ResourceUrl extends Model { protected $fillable=['project_id','label','url','type','notes']; public function project(){return $this->belongsTo(Project::class);} }
