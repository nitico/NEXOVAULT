import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const script = readFileSync(new URL('../../public/credential-secrets.js', import.meta.url), 'utf8');
test('reveal and clipboard preserve exact synthetic secrets, hide and reject failures', async () => {
    for (const password of ['Nexo#Prueba2026!$', '  <>&"\'\\+/=%  ', 'Ab#9'.repeat(2048), 'Español🔐漢字e\u0301', '0']) {
        let copied, timer, ok = true;
        const secret = { textContent: 'masked' };
        const reveal = { dataset: { url: '/synthetic/reveal' }, closest: () => ({ querySelector: () => secret }) };
        const copy = { dataset: reveal.dataset };
        runInNewContext(script, {
            document: { querySelector: () => ({ content: 'synthetic-token' }), querySelectorAll: selector => selector.startsWith('.reveal') ? [reveal] : [copy] },
            window: { addEventListener() {} },
            navigator: { clipboard: { writeText: async value => { copied = value; } } },
            fetch: async () => ({ ok, json: async () => ({ username: 'synthetic-user', password }) }),
            setTimeout: callback => { timer = callback; return 1; }, clearTimeout() {}
        });
        await reveal.onclick();
        assert.ok(secret.textContent === password, 'Reveal must match exactly');
        await copy.onclick();
        assert.ok(copied === password, 'Clipboard must match exactly');
        await reveal.onclick();
        assert.ok(secret.textContent === '••••••••••••', 'Manual hide');
        await reveal.onclick(); timer();
        assert.ok(secret.textContent === '••••••••••••', 'Automatic hide');
        copied = undefined; ok = false;
        await copy.onclick(); await reveal.onclick();
        assert.ok(copied === undefined, 'Failed request must not copy');
        assert.ok(secret.textContent === '••••••••••••', 'Failed request must not reveal');
    }
});

test('generator only changes the form value on explicit invocation', () => {
    const form = readFileSync(new URL('../../resources/views/credentials/form.blade.php', import.meta.url), 'utf8');
    const inline = form.match(/<script>([\s\S]*?)<\/script>/)[1];
    const input = { value: 'synthetic-original' };
    const context = { document: { getElementById: () => input }, crypto: { getRandomValues: array => array.fill(1) } };
    runInNewContext(inline, context);
    assert.ok(input.value === 'synthetic-original', 'Loading must preserve input');
    context.generateVaultPassword();
    assert.ok(input.value !== 'synthetic-original' && input.value.length === 24, 'Explicit generation replaces input');
    assert.ok(form.includes('type="button"') && form.includes('onclick="generateVaultPassword()"'), 'Generator must be explicit');
});
