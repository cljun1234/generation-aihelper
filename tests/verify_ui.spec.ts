import { test, expect } from '@playwright/test';

test('Verify UI Layouts', async ({ page }) => {
  // Login first
  await page.goto('http://localhost:8000/login');
  await page.fill('input[name="email"]', 'test@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');

  // Dashboard
  await expect(page).toHaveURL('http://localhost:8000/');
  await page.screenshot({ path: 'screenshots/dashboard.png', fullPage: true });

  // Create Tool (Editor)
  await page.goto('http://localhost:8000/tools/create');
  await page.screenshot({ path: 'screenshots/editor.png', fullPage: true });

  // Fill tool details
  await page.fill('input[name="title"]', 'Test Tool UI');
  // Slug is auto-filled
  await page.fill('textarea[name="system_prompt"]', 'You are a helpful assistant.');

  // Add a field
  await page.click('text=Add Field');
  // Wait for field to appear
  await page.waitForSelector('.field-item');

  // Fill field details
  await page.fill('input[placeholder="e.g. Topic"]', 'My Topic'); // Label
  await page.fill('input[placeholder="topic"]', 'my_topic'); // Variable

  // Save
  await page.click('button[type="submit"]');

  // Verify redirect to dashboard
  await expect(page).toHaveURL('http://localhost:8000/tools');
  // Wait, if ToolController::store redirects to /tools, then it will be /tools.
  // My ToolController says `header('Location: /tools');`
  // So it will be /tools.
  // I should update ToolController to redirect to / if I want consistency, OR just expect /tools in test.
  // The user might want separate "Dashboard" vs "My Tools", but currently they are the same view.
  // I'll keep /tools for now.

  // Go to Public View
  await page.goto('http://localhost:8000/tool/test-tool-ui');
  await page.screenshot({ path: 'screenshots/public_tool.png', fullPage: true });
});
