# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 06-akses-idor.spec.ts >> Akses & IDOR >> siswa tidak dapat membuka siswa lain
- Location: tests\06-akses-idor.spec.ts:15:3

# Error details

```
Error: harus ditolak (403): /siswa/2026002

expect(received).toBeTruthy()

Received: false
```

# Page snapshot

```yaml
- main [ref=f2e2]:
  - generic [ref=f2e4]:
    - heading "404" [level=1] [ref=f2e5]
    - generic [ref=f2e6]: Not Found
```

# Test source

```ts
  1  | import { Page, expect } from '@playwright/test';
  2  | import { ROUTES } from '../fixtures/routes';
  3  | 
  4  | type Cred = { id: string; password: string };
  5  | 
  6  | export async function login(page: Page, who: Cred) {
  7  |   await page.goto(ROUTES.login);
  8  |   await page.getByLabel(/email|nis|nip/i).fill(who.id);
  9  |   await page.getByRole('textbox', { name: /password|kata sandi/i }).fill(who.password);
  10 |   await page.getByRole('button', { name: /masuk|login/i }).click();
  11 |   await expect(page).not.toHaveURL(/\/login|\/masuk/);
  12 | }
  13 | 
  14 | /** Ganti akun di perangkat yang sama (localStorage/device_id tetap, cookie sesi dihapus). */
  15 | export async function switchUser(page: Page, who: Cred) {
  16 |   await page.context().clearCookies();
  17 |   await login(page, who);
  18 | }
  19 | 
  20 | export async function expectForbidden(page: Page, url: string) {
  21 |   const res = await page.goto(url);
  22 |   const denied = res?.status() === 403 || (await page.getByTestId('forbidden').isVisible().catch(() => false));
> 23 |   expect(denied, `harus ditolak (403): ${url}`).toBeTruthy();
     |                                                 ^ Error: harus ditolak (403): /siswa/2026002
  24 | }
  25 | 
```