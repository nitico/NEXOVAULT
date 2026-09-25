<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class VaultSetting extends Model { protected $fillable=['key','value']; public static function val($key){return static::where('key',$key)->value('value');} public static function put($key,$value){return static::updateOrCreate(['key'=>$key],['value'=>$value]);} }
