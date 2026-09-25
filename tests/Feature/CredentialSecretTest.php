<?php
namespace Tests\Feature;

use App\Models\Credential;
use App\Services\VaultCrypto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class CredentialSecretTest extends TestCase
{
    use RefreshDatabase;

    public function test_secret_lifecycle(): void
    {
        $this->assertTrue(config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:');
        config(['logging.default' => 'null', 'app.debug' => false]);
        $master = bin2hex(random_bytes(24));
        $this->post('/vault/setup', ['password'=>$master, 'password_confirmation'=>$master])->assertRedirect('/dashboard');
        $values = ['Nexo#Prueba2026!$', '  <>&"\'\\+/=%  ', str_repeat('Ab#9', 2048), "Español🔐漢字e\u{0301}", '0'];
        foreach ($values as $value) { $this->travel(61)->seconds();
            $this->post('/credentials', ['label'=>'Synthetic QA', 'username'=>'synthetic-user', 'password'=>$value])->assertRedirect('/credentials');
            $credential = Credential::latest('id')->firstOrFail();
            $cipher = $credential->password_enc;
            $this->assertTrue($cipher !== $value, 'Storage must be encrypted.');
            $this->assertTrue(app(VaultCrypto::class)->decrypt($cipher, app(VaultCrypto::class)->sessionKey()) === $value, 'Stored secret must round-trip.');
            $this->checkReveal($credential, $value);
            $this->post('/vault/lock')->assertRedirect('/vault');
            $this->post('/credentials/'.$credential->id.'/reveal')->assertRedirect('/vault');
            $this->post('/vault/unlock', ['password'=>$master])->assertRedirect('/dashboard');
            $this->checkReveal($credential, $value);
            $this->put('/credentials/'.$credential->id, ['label'=>'Synthetic edit', 'password'=>''])->assertRedirect('/credentials');
            $this->assertTrue($credential->fresh()->password_enc === $cipher, 'Empty edit must preserve ciphertext.');
            $this->checkReveal($credential, $value);
            $replacement = $value === '0' ? '0' : $value.'🔑';
            $this->put('/credentials/'.$credential->id, ['label'=>'Synthetic replace', 'password'=>$replacement])->assertRedirect('/credentials');
            $this->assertTrue($credential->fresh()->password_enc !== $cipher, 'Replacement must use fresh encryption.');
            $this->checkReveal($credential, $replacement);
            $this->travel(61)->seconds();
            $this->post('/vault/lock')->assertRedirect('/vault');
            $this->post('/vault/unlock', ['password'=>$master])->assertRedirect('/dashboard');
            $this->checkReveal($credential, $replacement);
        }
    }

    private function checkReveal(Credential $credential, string $expected): void
    {
        $response = $this->post('/credentials/'.$credential->id.'/reveal');
        $this->assertTrue($response->status() === 200, 'Reveal must succeed.');
        $this->assertTrue($response->json('password') === $expected, 'Reveal must match byte for byte.');
    }
}
