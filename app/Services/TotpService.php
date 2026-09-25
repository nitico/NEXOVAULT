<?php
namespace App\Services;
class TotpService {
 private const ALPHABET='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
 public function secret(int $bytes=20):string{return $this->base32(random_bytes($bytes));}
 private function base32(string $data):string{$bits='';foreach(str_split($data) as $c)$bits.=str_pad(decbin(ord($c)),8,'0',STR_PAD_LEFT);$out='';foreach(str_split($bits,5) as $chunk){$chunk=str_pad($chunk,5,'0');$out.=self::ALPHABET[bindec($chunk)];}return $out;}
 private function decode(string $s):string{$s=strtoupper(preg_replace('/[^A-Z2-7]/','',$s));$bits='';foreach(str_split($s) as $c){$p=strpos(self::ALPHABET,$c);if($p===false)continue;$bits.=str_pad(decbin($p),5,'0',STR_PAD_LEFT);} $out='';foreach(str_split($bits,8) as $b)if(strlen($b)===8)$out.=chr(bindec($b));return $out;}
 public function code(string $secret,?int $time=null):string{$counter=intdiv($time??time(),30);$bin=pack('N*',0).pack('N*',$counter);$hash=hash_hmac('sha1',$bin,$this->decode($secret),true);$o=ord($hash[19])&15;$n=((ord($hash[$o])&127)<<24)|((ord($hash[$o+1])&255)<<16)|((ord($hash[$o+2])&255)<<8)|(ord($hash[$o+3])&255);return str_pad((string)($n%1000000),6,'0',STR_PAD_LEFT);}
 public function verify(string $secret,string $code):bool{if(!preg_match('/^\d{6}$/',$code))return false;foreach([-1,0,1] as $w)if(hash_equals($this->code($secret,time()+$w*30),$code))return true;return false;}
 public function uri(string $secret):string{return 'otpauth://totp/'.rawurlencode('NexoVault:Master').'?secret='.$secret.'&issuer='.rawurlencode('NexoVault').'&algorithm=SHA1&digits=6&period=30';}
}
