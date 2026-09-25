<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{
  Schema::table('credentials',function(Blueprint $t){$t->timestamp('password_changed_at')->nullable()->after('favorite');$t->timestamp('last_revealed_at')->nullable()->after('password_changed_at');});
  Schema::create('project_progress_logs',function(Blueprint $t){$t->id();$t->foreignId('project_id')->constrained()->cascadeOnDelete();$t->unsignedTinyInteger('progress');$t->string('source',20)->default('AUTO');$t->timestamps();$t->index(['project_id','created_at']);});
 }
 public function down():void{Schema::dropIfExists('project_progress_logs');Schema::table('credentials',fn(Blueprint $t)=>$t->dropColumn(['password_changed_at','last_revealed_at']));}
};
