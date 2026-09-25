<?php
namespace Tests\Unit;

use App\Services\VaultCrypto;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class VaultCryptoTest extends TestCase
{
    public function test_authenticated_encryption_rejects_tampering_and_wrong_keys(): void
    {
        $crypto = new VaultCrypto;
        $key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        $value = bin2hex(random_bytes(40));
        $cipher = $crypto->encrypt($value, $key);
        $this->assertTrue($crypto->decrypt($cipher, $key) === $value);
        $this->assertTrue($crypto->encrypt($value, $key) !== $cipher);
        $raw = base64_decode($cipher);
        $raw[strlen($raw)-1] = chr(ord($raw[strlen($raw)-1]) ^ 1);
        foreach ([[base64_encode($raw), $key], [$cipher, random_bytes(32)]] as [$payload, $candidate]) {
            $rejected = false;
            try { $crypto->decrypt($payload, $candidate); } catch (RuntimeException) { $rejected = true; }
            $this->assertTrue($rejected, 'Unauthenticated ciphertext must be rejected.');
        }
    }
}
