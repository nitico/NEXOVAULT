<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{
 Schema::table('projects',function(Blueprint $t){$t->string('priority',20)->default('MEDIA')->after('status');$t->string('local_path')->nullable()->after('environment');$t->text('repository_url')->nullable()->after('local_path');$t->date('started_at')->nullable()->after('repository_url');$t->boolean('auto_progress')->default(true)->after('progress');$t->boolean('archived')->default(false)->after('auto_progress');});
 Schema::create('audit_logs',function(Blueprint $t){$t->id();$t->string('event',80);$t->string('entity_type',80)->nullable();$t->unsignedBigInteger('entity_id')->nullable();$t->text('description')->nullable();$t->string('ip',64)->nullable();$t->text('user_agent')->nullable();$t->timestamps();$t->index(['entity_type','entity_id']);});
 } public function down():void{Schema::dropIfExists('audit_logs');Schema::table('projects',function(Blueprint $t){$t->dropColumn(['priority','local_path','repository_url','started_at','auto_progress','archived']);});} };
