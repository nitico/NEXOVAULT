<?php
namespace App\Http\Controllers;
use App\Models\{Project,Credential,ResourceUrl,Plan}; use Illuminate\Http\Request;
class SearchController extends Controller {
 public function __invoke(Request $r){$q=trim((string)$r->get('q'));$like='%'.$q.'%';$empty=$q==='';return view('search.index',['q'=>$q,'projects'=>$empty?collect():Project::where(fn($x)=>$x->where('name','like',$like)->orWhere('description','like',$like)->orWhere('client','like',$like)->orWhere('technology','like',$like))->limit(20)->get(),'credentials'=>$empty?collect():Credential::with('project')->where(fn($x)=>$x->where('label','like',$like)->orWhere('category','like',$like)->orWhere('url','like',$like))->limit(20)->get(),'urls'=>$empty?collect():ResourceUrl::with('project')->where(fn($x)=>$x->where('label','like',$like)->orWhere('url','like',$like)->orWhere('type','like',$like))->limit(20)->get(),'plans'=>$empty?collect():Plan::with('project')->where(fn($x)=>$x->where('title','like',$like)->orWhere('notes','like',$like))->limit(20)->get()]);}
}
