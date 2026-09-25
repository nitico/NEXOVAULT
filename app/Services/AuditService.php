<?php
namespace App\Services; use App\Models\AuditLog;
class AuditService { public static function log(string $event,?string $description=null,$entity=null):void{ AuditLog::create(['event'=>$event,'entity_type'=>$entity?class_basename($entity):null,'entity_id'=>$entity?->id,'description'=>$description,'ip'=>request()?->ip(),'user_agent'=>substr((string)request()?->userAgent(),0,1000)]); } }
