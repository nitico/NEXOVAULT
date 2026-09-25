<?php
namespace App\Http\Controllers;

use App\Models\{VaultSetting,AuditLog};
use App\Services\{VaultCrypto,TotpService,AuditService};
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function index(TotpService $t)
    {
        $enabled=VaultSetting::val('mfa_enabled')==='1';
        $pending=session('mfa_setup_secret');
        if ($pending && (int)session('mfa_setup_started',0) < time()-600) {
            session()->forget(['mfa_setup_secret','mfa_setup_started']);
            $pending=null;
        }
        return view('security.index',['mfaEnabled'=>$enabled,'setupSecret'=>$pending,'setupUri'=>$pending?$t->uri($pending):null,'logs'=>AuditLog::latest()->take(30)->get(),'timeout'=>config('nexovault.idle_timeout')]);
    }

    public function beginMfa(TotpService $t)
    {
        abort_if(VaultSetting::val('mfa_enabled')==='1',409,'MFA ya está activado.');
        session(['mfa_setup_secret'=>$t->secret(),'mfa_setup_started'=>time()]);
        return redirect()->route('security.index')->with('ok','Configuración iniciada. La clave temporal vence en 10 minutos.');
    }

    public function cancelMfaSetup()
    {
        session()->forget(['mfa_setup_secret','mfa_setup_started']);
        return redirect()->route('security.index')->with('ok','Configuración de MFA cancelada.');
    }

    public function confirmMfa(Request $r,VaultCrypto $c,TotpService $t)
    {
        $d=$r->validate(['code'=>'required|digits:6']);
        $s=session('mfa_setup_secret');
        abort_unless($s && (int)session('mfa_setup_started',0)>=time()-600,422,'La configuración expiró. Iníciala nuevamente.');
        if(!$t->verify($s,$d['code'])) return back()->withErrors(['mfa'=>'Código inválido.']);
        $k=$c->sessionKey();
        VaultSetting::put('mfa_secret_enc',$c->encrypt($s,$k));
        VaultSetting::put('mfa_enabled','1');
        [$codes,$hashes]=$this->newRecoveryCodes();
        VaultSetting::put('mfa_recovery_hashes',json_encode($hashes));
        session()->forget(['mfa_setup_secret','mfa_setup_started']);
        AuditService::log('MFA_ENABLED','Microsoft Authenticator/TOTP activado.');
        return view('security.recovery',['codes'=>$codes]);
    }

    public function regenerateRecovery(Request $r,VaultCrypto $c,TotpService $t)
    {
        $d=$r->validate(['current_password'=>'required','mfa_code'=>'required']);
        if(!$this->masterValid($d['current_password'],$c)) return back()->withErrors(['current_password'=>'Contraseña maestra incorrecta.']);
        if(!$this->mfaValid($d['mfa_code'],$c,$t,false)) return back()->withErrors(['mfa_code'=>'Código MFA incorrecto.']);
        [$codes,$hashes]=$this->newRecoveryCodes();
        VaultSetting::put('mfa_recovery_hashes',json_encode($hashes));
        AuditService::log('MFA_RECOVERY_REGENERATED','Códigos de recuperación regenerados.');
        return view('security.recovery',['codes'=>$codes]);
    }

    public function disableMfa(Request $r,VaultCrypto $c,TotpService $t)
    {
        $d=$r->validate(['current_password'=>'required','mfa_code'=>'required','confirm'=>'required|in:DESACTIVAR']);
        if(!$this->masterValid($d['current_password'],$c)) return back()->withErrors(['current_password'=>'Contraseña maestra incorrecta.']);
        if(!$this->mfaValid($d['mfa_code'],$c,$t,false)) return back()->withErrors(['mfa_code'=>'Código MFA incorrecto.']);
        foreach(['mfa_enabled','mfa_secret_enc','mfa_recovery_hashes'] as $k) VaultSetting::where('key',$k)->delete();
        AuditService::log('MFA_DISABLED','MFA desactivado tras reautenticación.');
        return redirect()->route('security.index')->with('ok','MFA desactivado.');
    }

    public function changeMaster(Request $r,VaultCrypto $c,TotpService $t)
    {
        $min=app()->environment('local')?8:12;
        $rules=['current_password'=>'required','password'=>['required','confirmed','min:'.$min]];
        if(VaultSetting::val('mfa_enabled')==='1') $rules['mfa_code']='required';
        $d=$r->validate($rules);
        $salt=base64_decode(VaultSetting::val('salt'));
        try{$old=$c->derive($d['current_password'],$salt);$dek=$c->unwrapKey(VaultSetting::val('wrapped_dek'),VaultSetting::val('wrap_nonce'),$old);}catch(\Throwable){return back()->withErrors(['current_password'=>'Contraseña actual incorrecta.']);}
        if(VaultSetting::val('mfa_enabled')==='1' && !$this->mfaValid($d['mfa_code'],$c,$t,false)) return back()->withErrors(['mfa_code'=>'Código MFA incorrecto.']);
        $newSalt=random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);$new=$c->derive($d['password'],$newSalt);$w=$c->wrapKey($dek,$new);
        VaultSetting::put('salt',base64_encode($newSalt));VaultSetting::put('wrapped_dek',$w['wrapped']);VaultSetting::put('wrap_nonce',$w['nonce']);
        AuditService::log('MASTER_PASSWORD_CHANGED','Contraseña maestra cambiada sin recifrar los registros.');
        return back()->with('ok','Contraseña maestra actualizada.');
    }

    private function masterValid(string $password,VaultCrypto $c):bool
    {
        try{$salt=base64_decode(VaultSetting::val('salt'));$kek=$c->derive($password,$salt);$c->unwrapKey(VaultSetting::val('wrapped_dek'),VaultSetting::val('wrap_nonce'),$kek);return true;}catch(\Throwable){return false;}
    }

    private function mfaValid(string $code,VaultCrypto $c,TotpService $t,bool $consumeRecovery=true):bool
    {
        try{$secret=$c->decrypt(VaultSetting::val('mfa_secret_enc'),$c->sessionKey());}catch(\Throwable){return false;}
        if($t->verify($secret,trim($code))) return true;
        if(!$consumeRecovery) return false;
        $hashes=json_decode(VaultSetting::val('mfa_recovery_hashes')?:'[]',true);
        $h=hash('sha256',strtoupper(trim($code)));$i=array_search($h,$hashes,true);
        if($i===false) return false;
        if($consumeRecovery){unset($hashes[$i]);VaultSetting::put('mfa_recovery_hashes',json_encode(array_values($hashes)));}
        return true;
    }

    private function newRecoveryCodes():array
    {
        $codes=[];$hashes=[];
        for($i=0;$i<8;$i++){$raw=strtoupper(bin2hex(random_bytes(5)));$x=substr($raw,0,5).'-'.substr($raw,5,5);$codes[]=$x;$hashes[]=hash('sha256',str_replace('-','',$x));}
        return [$codes,$hashes];
    }
}
