import { defineConfig, devices } from '@playwright/test';

const host = process.env.PLAYWRIGHT_PANEL_HOST ?? '127.0.0.1';
const port = Number(process.env.PLAYWRIGHT_PANEL_PORT ?? '8099');
const baseURL = process.env.PLAYWRIGHT_PANEL_BASE_URL ?? `http://${host}:${port}`;

export default defineConfig({
  testDir: './panel',
  timeout: 120_000,
  expect: {
    timeout: 15_000,
  },
  fullyParallel: false,
  workers: 1,
  retries: 1,
  reporter: [
    ['list'],
    ['html', { open: 'never', outputFolder: 'playwright-report/panel' }],
  ],
  use: {
    baseURL,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'off',
  },
  webServer: {
    command: 'bash ./scripts/playwright-panel-server.sh',
    cwd: process.cwd(),
    url: `${baseURL}/admin/login`,
    reuseExistingServer: !process.env.CI,
    timeout: 180_000,
  },
  projects: [
    {
      name: 'chrome',
      use: {
        ...devices['Desktop Chrome'],
        channel: 'chrome',
      },
    },
  ],
});
