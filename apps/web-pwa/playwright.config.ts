import { defineConfig } from '@playwright/test';

const pwaPort = Number(process.env.PLAYWRIGHT_WEB_PWA_PORT ?? '3401');
const pwaBaseUrl = process.env.PLAYWRIGHT_BASE_URL || `http://127.0.0.1:${pwaPort}`;

export default defineConfig({
  testDir: './tests/e2e',
  webServer: {
    command: `npm run dev -- --hostname 127.0.0.1 --port ${pwaPort}`,
    url: pwaBaseUrl,
    reuseExistingServer: false,
    timeout: 180000
  },
  use: {
    baseURL: pwaBaseUrl,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'off',
    channel: 'chrome',
  },
  reporter: [['list']],
});
