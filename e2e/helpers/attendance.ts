import { Page, expect } from '@playwright/test';

export async function startSession(qrPage: Page, classSlug: string) {
  await qrPage.goto('/dashboard');
  await qrPage.getByTestId(`start-attendance-${classSlug}`).click();
  await expect(qrPage.getByTestId('qr-payload')).toBeVisible();
}

export async function scanUrl(qrPage: Page) {
  const url = await qrPage.getByTestId('qr-payload').getAttribute('data-url');
  expect(url, 'QR harus membawa data-url tautan pindai').toBeTruthy();
  return url!;
}

/** Simulasi memindai: kamera bawaan HP membuka tautan di QR (PRD 3.5.1 langkah 3). */
export async function scanAs(page: Page, url: string) {
  await page.goto(url);
  return page.getByTestId('scan-result');
}

export async function openMonitor(qrPage: Page, monitorPage: Page) {
  const href = await qrPage.getByTestId('link-pantauan').getAttribute('href');
  await monitorPage.goto(href!);
}
