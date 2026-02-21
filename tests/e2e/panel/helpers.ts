import { expect, Page } from '@playwright/test';

export const e2eUserEmail = process.env.PLAYWRIGHT_E2E_EMAIL ?? 'e2e-mega@example.test';
export const e2eUserPassword = process.env.PLAYWRIGHT_E2E_PASSWORD ?? 'E2E#Secret1234';
export const e2eTenantSlug = process.env.PLAYWRIGHT_E2E_TENANT_SLUG ?? 'e2e-tenant';

export async function signIn(page: Page, loginPath: string): Promise<void> {
  await page.goto(loginPath);
  await expect(page.locator('#form\\.email')).toBeVisible();
  await page.locator('#form\\.email').fill(e2eUserEmail);
  await page.locator('#form\\.password').fill(e2eUserPassword);
  await page.locator('button[type="submit"]').click();
}

