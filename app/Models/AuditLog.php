<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class AuditLog extends Model { protected $fillable=['event','entity_type','entity_id','description','ip','user_agent']; }
