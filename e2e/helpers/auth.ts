import { Page, expect } from '@playwright/test';
import { ROUTES } from '../fixtures/routes';

type Cred = { id: string; password: string };

export async function login(page: Page, who: Cred) {
  await page.goto(ROUTES.login);
  await page.getByLabel(/email|nis|nip/i).fill(who.id);
  await page.getByRole('textbox', { name: /password|kata sandi/i }).fill(who.password);
  await page.getByRole('button', { name: /masuk|login/i }).click();
  await expect(page).not.toHaveURL(/\/login|\/masuk/);
}

/** Ganti akun di perangkat yang sama (localStorage/device_id tetap, cookie sesi dihapus). */
export async function switchUser(page: Page, who: Cred) {
  await page.context().clearCookies();
  await login(page, who);
}

export async function expectForbidden(page: Page, url: string) {
  const res = await page.goto(url);
  const denied = res?.status() === 403 || (await page.getByTestId('forbidden').isVisible().catch(() => false));
  expect(denied, `harus ditolak (403): ${url}`).toBeTruthy();
}
