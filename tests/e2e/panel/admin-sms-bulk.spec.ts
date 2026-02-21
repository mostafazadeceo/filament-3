import { expect, test } from '@playwright/test';
import { signIn } from './helpers';

test.describe('Admin Panel SMS Bulk E2E', () => {
  test('mega super admin can login and open sms bulk pages', async ({ page }) => {
    await signIn(page, '/admin/login');

    await expect(page).toHaveURL(/\/admin(?:\/)?$/);
    await expect(page.locator('body')).toContainText(/مگا سوپر ادمین|Super Admin/i);

    await page.goto('/admin/provider-connections');
    await expect(page).toHaveURL(/\/admin\/provider-connections(?:\?.*)?$/);
    await expect(page.locator('body')).toContainText(/اتصالات پیامک|Provider Connections/i);

    await page.goto('/admin/campaigns');
    await expect(page).toHaveURL(/\/admin\/campaigns(?:\?.*)?$/);
    await expect(page.locator('body')).toContainText(/کمپین|Campaign/i);

    await page.goto('/admin/suppression-lists');
    await expect(page).toHaveURL(/\/admin\/suppression-lists(?:\?.*)?$/);
    await expect(page.locator('body')).toContainText(/سرکوب|Suppression/i);
  });
});

