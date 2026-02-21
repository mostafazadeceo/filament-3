import { expect, test } from '@playwright/test';
import { e2eTenantSlug, signIn } from './helpers';

test.describe('Tenant Panel SMS Bulk E2E', () => {
  test('user can login to tenant panel and browse sms bulk resources', async ({ page }) => {
    await signIn(page, '/tenant/login');

    await expect(page).toHaveURL(new RegExp(`/tenant/${e2eTenantSlug}(?:/)?$`));
    await expect(page.locator('body')).toContainText(/مدیریت سازمان|Organization/i);

    await page.goto(`/tenant/${e2eTenantSlug}/provider-connections`);
    await expect(page).toHaveURL(new RegExp(`/tenant/${e2eTenantSlug}/provider-connections(?:\\?.*)?$`));
    await expect(page.locator('body')).toContainText(/اتصالات پیامک|Provider Connections/i);

    await page.goto(`/tenant/${e2eTenantSlug}/phonebooks`);
    await expect(page).toHaveURL(new RegExp(`/tenant/${e2eTenantSlug}/phonebooks(?:\\?.*)?$`));
    await expect(page.locator('body')).toContainText(/دفترچه تلفن|Phonebooks/i);

    await page.goto(`/tenant/${e2eTenantSlug}/campaigns`);
    await expect(page).toHaveURL(new RegExp(`/tenant/${e2eTenantSlug}/campaigns(?:\\?.*)?$`));
    await expect(page.locator('body')).toContainText(/کمپین|Campaign/i);
  });
});

