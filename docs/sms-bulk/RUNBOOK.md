# SMS Bulk Runbook

## Operational Checks
1. Provider connection status + last credit snapshot
2. Queue depth for SMS jobs
3. Campaign statuses (`queued/sending/failed`)
4. Suppression and opt-out trends

## Common Failures
- `provider auth failed`: rotate token in provider connection
- `rate limit`: reduce chunk size / worker concurrency
- `quota exceeded`: adjust quota policy or split campaign
- `pending approval`: approve campaign before submit

## Safe Recovery
- Pause campaign
- Sync reports
- Resume only failed recipients by re-queueing campaign chunks

## Privacy
- No surveillance telemetry is collected
- Only operational logs + delivery statuses are stored

## Browser E2E (Playwright)
- Runner: Playwright (`Chromium`) with isolated Laravel runtime
- Command (panel + web-pwa): `npm run test:e2e:all`
- Command (panel only): `npm run test:e2e:panel`
- Isolated runtime script: `scripts/playwright-panel-server.sh`
- Default isolated DB: `/tmp/filament3_playwright.sqlite`
- Default E2E user: `e2e-mega@example.test` (mega super admin)
