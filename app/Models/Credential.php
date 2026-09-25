<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class Credential extends Model { protected $fillable=['project_id','label','username_enc','password_enc','url','category','notes_enc','favorite','password_changed_at','last_revealed_at']; protected $casts=['favorite'=>'boolean','password_changed_at'=>'datetime','last_revealed_at'=>'datetime']; public function project(){return $this->belongsTo(Project::class);} }
