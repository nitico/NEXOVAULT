<?php
namespace App\Services;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;
class VaultCrypto {
 public function derive(string $password,string $salt): string { return sodium_crypto_pwhash(SODIUM_CRYPTO_SECRETBOX_KEYBYTES,$password,$salt,SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE,SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13); }
 public function wrapKey(string $dek,string $kek): array { $nonce=random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES); return ['wrapped'=>base64_encode(sodium_crypto_secretbox($dek,$nonce,$kek)),'nonce'=>base64_encode($nonce)]; }
 public function unwrapKey(string $wrapped,string $nonce,string $kek): string { $plain=sodium_crypto_secretbox_open(base64_decode($wrapped),base64_decode($nonce),$kek); if($plain===false) throw new RuntimeException('Contraseña maestra incorrecta.'); return $plain; }
 public function encrypt(string $value,string $dek): string { $nonce=random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES); return base64_encode($nonce.sodium_crypto_secretbox($value,$nonce,$dek)); }
 public function decrypt(?string $value,string $dek): string { if(!$value)return ''; $raw=base64_decode($value,true); if($raw===false||strlen($raw)<=SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) throw new RuntimeException('Dato cifrado inválido.'); $nonce=substr($raw,0,SODIUM_CRYPTO_SECRETBOX_NONCEBYTES); $plain=sodium_crypto_secretbox_open(substr($raw,SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),$nonce,$dek); if($plain===false) throw new RuntimeException('No fue posible descifrar el dato.'); return $plain; }
 public function putSessionKey(string $dek): void { session(['vault_key'=>Crypt::encryptString(base64_encode($dek)),'vault_unlocked_at'=>now()->timestamp]); }
 public function sessionKey(): string { return base64_decode(Crypt::decryptString(session('vault_key'))); }
}
