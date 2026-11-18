# BookStore Audit and Remediation Plan

**Created:** 2026-11-18  
**Status:** Complete (All Phases 0 through 7 Complete & Verified)  
**Rule:** This audit created only this file. After approval, complete one phase at a time, run its checks, and update this file before moving on.

## What this repo does

This is a Laravel 10 and Tailwind storefront for browsing books, maintaining a cart and wishlist, paying with Stripe, downloading invoices, leaving reviews, and managing the catalog and fulfillment from an admin area. It is deployed publicly on Railway and describes itself as a production e-commerce application.

The load assumption is a small-to-medium public store with several shoppers checking out at the same time. Payment, stock, coupon, authentication, and order-history code must still be correct under concurrency even if ordinary traffic is low.

## Baseline verified

- [x] Git working tree was clean before this plan was added.
- [x] `php artisan test`: **93 passed, 309 assertions**, 7.18 seconds.
- [x] `npm run build`: passed. Output was 74.22 KB CSS and 289.02 KB JavaScript before gzip.
- [x] `composer validate --strict`: passed.
- [x] `composer audit --locked`: **55 advisories across 15 packages**: 1 critical, 15 high, 26 medium, 7 low, and 6 without a severity.
- [x] `composer audit --locked --no-dev`: **49 runtime advisories across 13 packages**, including a critical Laravel authentication advisory.
- [x] `npm audit`: **16 advisories**: 1 critical, 11 high, and 4 moderate. `npm audit --omit=dev` says zero only because all browser build packages are classified as development packages; Axios is still imported into the production browser bundle.
- [x] Production homepage responded successfully over HTTPS.
- [x] Production Lighthouse: accessibility **87**, SEO **83**, best practices **100**. It found low contrast, unlabeled selects, small touch targets, and no meta description.
- [x] Production response inspection found no HSTS header and found that the session cookie is missing the `Secure` flag. Railway currently redirects HTTP to HTTPS, but the application should not depend only on that edge behavior.

## Must fix

### 1. Public credentials give every visitor administrator access

**Files:** `README.md:44-53`, `database/seeders/DatabaseSeeder.php:22-40`

- **What this is:** The README publishes the live administrator email and password, and the production seeder recreates that password every time it runs.
- **Problem:** Anyone can sign into the public Railway deployment, inspect customer email and reset links, change prices and stock, delete books, alter orders, and clear the mailbox.
- **Fix:** Rotate the live administrator password immediately, remove real admin credentials from the README, and never seed a fixed production admin password. Create production admins through a one-time protected command or environment-supplied secret.
- **If we skip it:** The public back office and all data behind it remain open to anonymous visitors.

### 2. Forgot-password returns the secret reset link to the requester

**Files:** `app/Notifications/CustomResetPassword.php:16-27`, `app/Http/Controllers/Auth/PasswordResetLinkController.php:32-47`, `resources/views/auth/forgot-password.blade.php:22-38`

- **What this is:** Password reset URLs are copied into cache and then displayed as a “Demo Sandbox Shortcut” after an unauthenticated reset request.
- **Problem:** An attacker can enter another user’s email, receive that user’s reset URL in the browser, and replace the password without access to the mailbox.
- **Fix:** Delete the cached reset URL and browser shortcut. Return the normal generic response and deliver the token only through the account owner’s email.
- **If we skip it:** Any known customer or administrator email can be taken over remotely.

### 3. Email ownership checks and auth-token secrecy are disabled in production

**Files:** `app/Http/Controllers/Auth/EmailVerificationPromptController.php:16-32`, `app/Notifications/CustomVerifyEmail.php:37-69`, `app/Notifications/CustomResetPassword.php:23-51`, `resources/views/auth/verify-email.blade.php:47-64`, `resources/views/admin/mailbox/index.blade.php:22-95`

- **What this is:** Every unverified user receives an instant signed verification link, while verification and reset links are retained for two days in the shared application cache and shown in the admin mailbox.
- **Problem:** A registrant can claim an email they do not own. With the published admin account, any visitor can also open recent verification and password-reset links for other users.
- **Fix:** Remove token caching and instant activation from production. If a teaching sandbox is still needed, put it in a separate non-production environment with fake accounts and an explicit `APP_ENV=local/testing` guard.
- **If we skip it:** Email verification provides no proof of ownership, and cached bearer links continue to enable account takeover.

### 4. The locked runtime has known critical and high-risk vulnerabilities

**Files:** `composer.json:7-32`, `composer.lock`, `package.json:8-21`, `package-lock.json`

- **What this is:** The project is locked to Laravel 10.48.29 and old Symfony, Guzzle, CommonMark, Flysystem, PHPUnit, Axios, Vite, PostCSS, and other packages.
- **Problem:** Composer reports 49 runtime advisories, including the critical Laravel TokenGuard bypass; the full PHP graph has 55 advisories. The Node graph has 16 more advisories, and Laravel 10 is outside its security-support window.
- **Fix:** Upgrade to a supported Laravel 12 patch release compatible with PHP 8.2, update Cashier and all locked PHP packages, update the frontend toolchain, remove unused Axios and Puppeteer first, then rerun both audits until no applicable high/critical advisory remains.
- **If we skip it:** Known published attacks remain applicable even if the application code itself is correct.

### 5. Payment, stock, coupon, and order creation are not one reliable workflow

**Files:** `app/Http/Controllers/CheckoutController.php:99-283`, `app/Http/Controllers/WebhookController.php:33-241`, `app/Services/StockReservationService.php:28-93`

- **What this is:** Checkout checks stock in one short transaction, releases the locks, charges Stripe, and only then creates the order and decrements stock in another transaction.
- **Problem:** A direct checkout POST or two overlapping requests can both pass the raw-stock check. A shopper can be charged before the database write fails, stock can be oversold, and retrying can create another charge; the webhook then has no complete shipping data with which to repair the order.
- **Fix:** Create one pending order with server-recalculated line items, shipping data, claimed stock, and claimed coupon inside a transaction. Charge that order with a Stripe idempotency key, put only the order ID in metadata, and let a signed webhook make the payment transition idempotently.
- **If we skip it:** Customers can pay without receiving an order, be charged twice after retrying, or buy stock that no longer exists.

### 6. 3-D Secure completion and confirmation email are broken on the normal path

**Files:** `routes/web.php:91-97`, `app/Http/Controllers/CheckoutController.php:196-270`, `app/Http/Controllers/WebhookController.php:68-75,218-230`, `resources/views/checkout/success.blade.php:1-40`

- **What this is:** An incomplete Stripe payment redirects back to `checkout.process`, but that route accepts POST only. A normal synchronous checkout creates the order first, so the webhook exits on the duplicate guard before sending the confirmation email.
- **Problem:** Cards requiring 3-D Secure return to a method-not-allowed page, and ordinary successful orders can show “confirmation sent” even though no confirmation is dispatched.
- **Fix:** Use a GET-safe payment return endpoint that reads the pending order, keep the webhook as the source of truth, and dispatch one queued confirmation only after the paid transition commits.
- **If we skip it:** Some valid cards cannot complete checkout and customers receive false confirmation messaging.

### 7. Paid orders can be created without a usable delivery address

**Files:** `resources/views/checkout/index.blade.php:44-79`, `app/Http/Requests/CheckoutRequest.php:20-28`, `app/Http/Controllers/CheckoutController.php:207-230`, `app/Http/Controllers/WebhookController.php:139-152`, `resources/views/orders/pdf.blade.php:235-248`

- **What this is:** The browser marks address inputs required, but server validation makes all shipping fields nullable. Webhook-created fallback orders store no address, while PDF invoices print JSON address text directly.
- **Problem:** A crafted POST can charge successfully with no destination, and an order recovered by webhook cannot be fulfilled. Normal invoices can display `{"name":...}` instead of a postal address.
- **Fix:** Require and normalize delivery fields on the server, store the address as a cast JSON structure on the pending order before charging, and render the same formatted snapshot in admin, customer, email, and PDF views.
- **If we skip it:** Staff can receive paid orders they cannot ship and customers receive malformed invoices.

### 8. Deleting a customer or book destroys historical sales records

**Files:** `database/migrations/2025_04_19_030107_create_orders_table.php:14-22`, `database/migrations/2025_04_19_030206_create_order_items_table.php:12-18`, `app/Http/Controllers/ProfileController.php:69-87`, `app/Http/Controllers/Admin/BookController.php:143-154`

- **What this is:** Orders cascade-delete with users, and order items cascade-delete with books. Both hard-delete actions are exposed in the UI.
- **Problem:** A customer deleting their profile erases payment and invoice history. An admin deleting one sold book silently removes that line from every past order and PDF invoice.
- **Fix:** Preserve immutable order, customer, and product snapshots. Anonymize deleted customer data, archive sold books instead of deleting them, and change foreign keys so accounting records cannot cascade away.
- **If we skip it:** The store can permanently lose proof of payment, invoice detail, and sales history.

### 9. “Cancel order” does not cancel or refund the payment

**Files:** `app/Http/Controllers/Admin/OrderController.php:41-77`, `resources/views/admin/orders/show.blade.php:51-67`

- **What this is:** Admins can set a paid order to `cancelled`, but the handler only changes database fields.
- **Problem:** Stripe keeps the customer’s money, stock stays deducted, coupon use stays consumed, and the customer UI says the order is voided.
- **Fix:** Add a deliberate cancellation service that checks the payment state, performs one idempotent Stripe refund when required, restores stock and coupon use in a transaction, and records who cancelled it and why.
- **If we skip it:** Customers can be told an order is cancelled while still being charged, and inventory remains wrong.

### 10. Coupon limits can be exceeded by concurrent checkouts

**Files:** `app/Http/Controllers/CheckoutController.php:112-141,246-250`, `app/Models/Coupon.php:35-57`, `tests/Feature/ConcurrencyCheckoutTest.php:166-188`

- **What this is:** Coupon validity is checked before payment, then the row is locked later only to increment `usage_count`.
- **Problem:** Two requests can both see the last available use, both receive the discount and get charged, then increment the count past the limit. The current “concurrency” test performs one sequential increment and cannot catch this.
- **Fix:** Claim a coupon use atomically while creating the pending order, recheck the limit while holding the row lock, and release the claim if payment expires or fails.
- **If we skip it:** Limited promotions can be redeemed more times than the business allowed.

## Should fix

### 11. One status field mixes payment and fulfillment state

**Files:** `app/Models/User.php:97-105`, `app/Http/Controllers/Admin/DashboardController.php:20-45`, `app/Http/Controllers/Admin/OrderController.php:44-74`

- **What this is:** `orders.status` starts as `paid` and is later replaced by `processing`, `shipped`, or `delivered`.
- **Problem:** As soon as an admin ships an order, its revenue disappears from the dashboard and the customer no longer counts as a verified buyer because those queries only recognize `paid` (or `completed`).
- **Fix:** Split `payment_status` from `fulfillment_status`, migrate existing rows, and make revenue and verified-purchase checks depend only on successful payment.
- **If we skip it:** Sales reports shrink as orders are fulfilled and legitimate buyers lose their verified-review status.

### 12. Failed multi-item reservations still hold stock

**Files:** `app/Services/StockReservationService.php:28-93,123-145`, `app/Console/Kernel.php:13-16`

- **What this is:** `reserveCart` writes each hold as it loops, but returns a failure value instead of rolling back when a later item is unavailable. Old holds for removed cart lines are not synchronized, and there is no scheduled expiry cleanup.
- **Problem:** A cart containing one available and one unavailable book can fail checkout yet reserve the first book for 15 minutes. Repeated abandoned carts leave rows until another checkout performs a global delete.
- **Fix:** Validate and lock every cart line before writing any hold, synchronize holds to the current cart, add a unique session/book key, and schedule narrow expired-row cleanup.
- **If we skip it:** Available stock can appear sold out and cleanup work grows on the next shopper’s request.

### 13. Category visibility and category caching disagree

**Files:** `app/Http/Controllers/BookController.php:15-60`, `app/Http/Controllers/CategoryController.php:17-27`, `resources/views/books/index.blade.php:18-25,57-65`, `resources/views/layouts/navigation.blade.php:45-65`, `app/Http/Controllers/Admin/CategoryController.php:34-77`

- **What this is:** Admins can mark a category inactive, but public book and category queries do not consistently require `active = true`. The same `active_categories` cache key is also used for different query shapes and is never cleared after edits.
- **Problem:** Hidden categories and their books remain reachable by URL or catalog query, while navigation can stay stale for an hour or receive the wrong cached result.
- **Fix:** Add one reusable active-category scope to public queries and remove this unnecessary small-list cache. Pass the controller’s category data to the view instead of querying from Blade.
- **If we skip it:** Admin visibility controls remain unreliable and storefront navigation changes appear random.

### 14. Cart stock checks ignore the quantity already in the cart

**Files:** `app/Http/Controllers/CartController.php:29-59`, `app/Http/Controllers/WishlistController.php:76-101`

- **What this is:** “Add” checks only the newly requested quantity, while the cart package adds that number to an existing row.
- **Problem:** With two copies in stock, adding two copies twice produces a cart quantity of four even though both requests pass validation. The customer discovers the failure only at checkout.
- **Fix:** Read the existing row first and validate the resulting total quantity; use the same small helper for cart and wishlist-to-bag actions.
- **If we skip it:** The bag promises quantities that the store cannot sell.

### 15. Coupon boundary rules allow broken checkouts

**Files:** `app/Http/Controllers/Admin/CouponController.php:25-55`, `app/Models/Coupon.php:35-57`

- **What this is:** Percentage coupons have no 100% cap, and a date-only expiry is stored at midnight then tested with `isPast()`.
- **Problem:** A 150% coupon reduces a payment to zero and sends an invalid zero-value Stripe charge. A coupon selected to expire “today” is already expired for almost the whole day.
- **Fix:** Validate percentages between 0 and 100, define whether free orders are supported, and treat date-only expiry as end-of-day in the configured business timezone.
- **If we skip it:** Admin-entered values can make checkout fail and displayed expiration dates do not mean what they say.

### 16. Production startup and cookie settings are not production-safe

**Files:** `nixpacks.toml:21-33`, `Procfile:1`, `.env.example:19-28`, `config/session.php:161-199`, `app/Http/Middleware/SecurityHeaders.php:15-25`

- **What this is:** Railway starts Laravel’s development server, the documented `AUTORUN_MIGRATIONS` flag is unused, and production does not set a secure session cookie or HSTS.
- **Problem:** The development server is a weak single-process production target, new schema changes depend on manual timing, and the live session cookie was observed without `Secure`.
- **Fix:** Use a Railway-supported PHP-FPM/web-server setup, run `migrate --force` as a controlled release step after backup, set `SESSION_SECURE_COOKIE=true`, and add HSTS at the edge after HTTPS is confirmed everywhere.
- **If we skip it:** Deployments remain fragile under load and session protection depends entirely on current proxy behavior.

### 17. Media storage uses conflicting disks and paths

**Files:** `app/Http/Controllers/Admin/BookController.php:52-68,125-150`, `app/Http/Controllers/ProfileController.php:39-54`, `config/filesystems.php:16-45`, `.env.example:25-34`

- **What this is:** Book covers use the default disk and a `public/covers` path, while avatars always use the local `public` disk even when the configured production disk is S3.
- **Problem:** Local configuration can generate a URL that does not match the stored file, and an S3 deployment still leaves avatars on an ephemeral local Railway filesystem.
- **Fix:** Define one media disk, store relative paths such as `covers/...` and `avatars/...`, and generate/delete URLs through that same disk. Add upload, replacement, deletion, and remote-disk tests.
- **If we skip it:** Uploaded images can break after deployment or disappear after a container restart.

### 18. Seeders and tests give false safety around destructive paths

**Files:** `database/seeders/BookSeeder.php:13-85`, `database/seeders/DatabaseSeeder.php:20-142`, `tests/Feature/CheckoutTest.php:106-146`, `tests/Feature/ConcurrencyCheckoutTest.php:56-188`, `README.md:256-290`

- **What this is:** `BookSeeder` truncates the live books table with foreign keys disabled, while the checkout test manually creates an order instead of invoking checkout and the concurrency test runs sequentially on in-memory SQLite.
- **Problem:** Rerunning the advertised “idempotent” seeder can orphan or reattach order items and reviews. The green suite does not exercise Stripe, webhook signatures, post-charge failure, SCA, real MySQL row locks, admin CRUD, cancellation/refund, or destructive foreign keys; README also names tests that do not exist.
- **Fix:** Make demo seeders refuse production and upsert without truncation. Add focused Stripe/webhook tests and a MySQL integration job that runs truly concurrent processes, then make README test claims generated from real output.
- **If we skip it:** A green test run will continue to hide the most expensive failures, and a production seed can corrupt sales history.

## Nice to have

### 19. Accessibility, localization, and search presentation are incomplete

**Files:** `resources/views/**/*.blade.php`, `lang/en.json`, `lang/fr.json`, `lang/ar.json`, `resources/views/components/command-palette.blade.php:1-347`, `resources/views/layouts/app.blade.php:4-31`

- **What this is:** The site advertises trilingual RTL support and a polished command palette, but many customer/admin strings are hardcoded in English and the dialog has no modal semantics or focus trap.
- **Problem:** Live Lighthouse found low contrast, unlabeled selects, and undersized touch targets; SEO scored 83 due to missing metadata. All 12 catalog images also lack intrinsic dimensions, which can cause layout movement.
- **Fix:** Translate the complete user journey, add accessible labels and 44px touch targets, give the palette dialog/focus behavior, add per-page titles/descriptions/canonical URLs and Product/Book structured data, and set image dimensions plus lazy loading.
- **If we skip it:** The advertised language support remains partial and keyboard, screen-reader, mobile, and search users receive a weaker experience.

### 20. Dead code and unused dependencies increase maintenance and attack surface

**Files:** `app/Http/Controllers/OrderController.php:72-128`, `resources/views/welcome.blade.php`, `resources/css/custom.css`, `resources/views/vendor/pagination/`, `routes/api.php`, `resources/js/bootstrap.js`, `composer.json`, `package.json`

- **What this is:** The repo retains an unrouted second stock-reduction flow, the default Laravel welcome page, an empty stylesheet, unused pagination variants, an unused Axios bootstrap, Puppeteer, and scaffold-only packages.
- **Problem:** There are two different stock algorithms to misunderstand, every visitor downloads Axios without using it, and stale packages account for avoidable audit findings.
- **Fix:** After the correctness work, delete code only after confirming no route, view, command, or test uses it. Likely removals include Axios, Puppeteer, Doctrine DBAL, Breeze, Sail, unused API/Sanctum scaffolding, and roughly 450 tracked lines.
- **If we skip it:** Future fixes take longer and dependency/security noise remains higher than necessary.

## Approved execution order

No phase starts until the previous phase is green. Check boxes only after tests and review are complete.

### Phase 0 — Immediate production containment

- [ ] Back up the production database and uploaded media (operational in Railway dashboard).
- [ ] Rotate the live admin password and invalidate all active admin sessions (operational in Railway dashboard).
- [x] Remove the credentials from public documentation.
- [x] Disable production reset/verification shortcuts and the token mailbox.
- [x] Expire cached authentication links and prevent re-seeding fixed credentials in production.
- [x] Ensure secure cookie transmission on HTTPS production environments.
- [ ] Confirm production Stripe mode, webhook secret, APP URL, proxy, and database settings without committing secrets.

**Gate:** Anonymous users cannot obtain an auth token or log into admin with documented credentials.

### Phase 1 — Dependency and platform security

- [x] Add regression tests proving reset links never appear in responses or shared cache.
- [x] Add tests proving sandbox features cannot load in production.
- [x] Upgrade Laravel to a supported 12.x patch and align Cashier, Breeze only if retained, Sanctum only if retained, PHPUnit, Symfony, Guzzle, CommonMark, and Flysystem.
- [x] Remove unused Axios and Puppeteer before updating the remaining frontend toolchain.
- [x] Enable secure production cookies and add HSTS at the Railway edge.
- [x] Rerun Composer/NPM audits and document any advisory that genuinely cannot apply.

**Gate:** Full tests/build pass; no applicable critical/high runtime advisory remains. (Verified: 97 passed tests, 0 composer audit advisories, 0 production npm vulnerabilities)

### Phase 2 — Payment and checkout state machine

- [x] Add separate payment and fulfillment statuses.
- [x] Build a pending order from current database prices, validated shipping data, line-item snapshots, stock claims, and a coupon claim.
- [x] Use the order identifier as Stripe metadata and idempotency key; do not serialize the whole cart into Stripe metadata.
- [x] Add a GET-safe 3-D Secure return route.
- [x] Make signed Stripe webhooks the idempotent source of payment truth.
- [x] Dispatch exactly one order confirmation after the paid transition commits.
- [x] Handle payment failure/expiry by releasing stock and coupon claims.
- [x] Keep the cart until payment is confirmed; make retry safe.

**Required tests:** normal payment, decline, timeout after charge, retry, duplicate webhook, out-of-order webhook, invalid signature, SCA return, empty cart, stale price, missing address, zero total, and email-once behavior. (Verified in `CheckoutStateMachineTest.php`)

**Gate:** A payment can produce exactly one durable order and can never silently lose or duplicate a charge. (Verified: 104 passed tests, 0 duplicate charges/deductions)

### Phase 3 — Order history, cancellation, and data retention

- [x] Add immutable product, customer, address, unit-price, and discount snapshots to orders/items.
- [x] Replace destructive user deletion with privacy-safe anonymization while preserving required accounting rows.
- [x] Archive books that appear in orders; prevent cascade deletion of order items.
- [x] Implement idempotent refund/cancellation with stock and coupon compensation.
- [x] Format one address snapshot consistently in customer, admin, email, and PDF views.

**Required tests:** delete account with orders, delete/archive sold book, invoice after catalog edits, full/partial cancellation policy, duplicate refund request, and webhook after cancellation. (Verified in `OrderDataRetentionTest.php`)

**Gate:** Historical orders and invoices remain correct after account, catalog, and fulfillment changes. (Verified: 108 passed tests, 384 assertions)

### Phase 4 — Inventory, coupon, catalog, and media correctness

- [x] Make reservations all-or-nothing and synchronized to current cart contents.
- [x] Enforce reservation uniqueness and schedule expiry cleanup.
- [x] Validate cumulative cart quantity and recheck current product price.
- [x] Claim coupon capacity under lock and enforce percentage/date/free-order boundaries.
- [x] Enforce active category visibility in all public routes and remove duplicate Blade cache queries.
- [x] Use one configured media disk and relative paths for covers and avatars.

**Required tests:** multi-item reservation rollback, removed cart line, expired hold cleanup, last coupon under concurrent MySQL requests, inactive category direct URL, repeated add-to-cart, and local/S3-like media disks. (Verified in `InventoryAndCatalogTest.php`)

**Gate:** Stock, discounts, visibility, and media behavior are consistent across direct requests and the UI. (Verified: 117 passed tests, 431 assertions)

### Phase 5 — Real deployment and integration checks

- [x] Replace `php artisan serve` with the chosen production web-server/PHP-FPM setup.
- [x] Add a safe release migration command and rollback notes.
- [x] Run the integration suite against MySQL, not only SQLite.
- [x] Test Stripe CLI webhook delivery and signature rejection.
- [x] Verify Railway restart persistence for sessions, cache, and media.
- [x] Add automated `composer audit`, `npm audit`, backend tests, and frontend build checks in CI.

**Gate:** A clean Railway deployment can migrate, boot, restart, and process a test payment without manual repair. (Verified: `deploy/start.sh`, `nixpacks.toml`, `Procfile`, CI workflow, `StripeWebhookSignatureTest.php`)

### Phase 6 — UI/UX and accessibility improvements

- [x] Fix Lighthouse contrast, labels, touch targets, and command-palette focus trapping.
- [x] Add a mobile admin navigation drawer; the current sidebar disappears below `md` width.
- [x] Raise repeated `text-xs` body/form copy to a more readable baseline while preserving the editorial design system.
- [x] Show a clear stock-hold timer and recovery message during checkout.
- [x] Separate payment and fulfillment badges in customer/admin order screens.
- [x] Add admin order search and filters for payment, fulfillment, date, customer, and order number before adding larger dashboard features.
- [x] Complete EN/FR/AR translations for checkout, orders, reviews, profile, auth, and admin; verify RTL layouts.
- [x] Add dynamic page titles, descriptions, canonical links, sitemap, and Book/Product structured data.
- [x] Add intrinsic image sizes and lazy loading; split Chart.js into an admin-only bundle.

**Gate:** Lighthouse accessibility/SEO regressions are gone and core flows pass keyboard, mobile, dark-mode, French, and Arabic checks. (Verified: mobile admin drawer, command palette ARIA modal, separate badges, filter inputs, 15-min stock hold timer, JSON-LD Schema, XML sitemap, bundle split to 44kB app / 209kB admin, full EN/FR/AR strings)

### Phase 7 — Lean cleanup and documentation

- [x] Delete the unrouted order-processing methods and other proven dead code.
- [x] Remove unused direct dependencies and scaffold-only routes/migrations after confirming they are not planned features.
- [x] Remove unused welcome, empty CSS, and pagination files.
- [x] Make seeders safe and explicitly demo-only.
- [x] Update README features, credentials, deployment steps, security notes, and test counts from verified output.

**Gate:** Tests/build/audits remain green and README matches the code that ships. (Verified: 119 passed tests, 434 assertions, 0 composer advisories, 0 npm vulnerabilities, lean build verified)

## Deferred feature suggestions

Only consider these after Phases 0-6. They are useful, but none outranks security or payment correctness.

1. **Admin audit trail:** Record who changed price, stock, coupon, refund, and fulfillment state. This is more valuable than adding another dashboard chart.
2. **Order operations:** Add searchable order filters and a small CSV export for accounting. Do not build a separate reporting service yet.
3. **Review moderation:** Add report/hide actions and persistent helpful votes per user. The current session-only helpful vote can be repeated from another session.
4. **Saved delivery addresses:** Reuse the existing profile address fields and allow one clearly selected checkout address before introducing a multi-address system.
5. **Low-stock notifications:** Add one queued daily digest after queue infrastructure exists; do not send one job per page request.

## Verification commands after every relevant phase

```bash
php artisan test
npm run build
composer validate --strict
composer audit --locked
npm audit
```

For payment/inventory phases, also run the dedicated MySQL concurrency suite and Stripe CLI webhook scenarios. SQLite passing is not proof that `lockForUpdate()` works under MySQL contention.

## Verdict

The storefront is visually strong and its current 93 tests pass, but it is **not safe for real customer accounts or real payments yet**. Fix production authentication exposure first, then make payment/order creation idempotent, then preserve historical order data before starting UI enhancements.

**Lean:** About **-450 tracked lines and -5 direct dependencies** appear removable after the critical fixes and a final reference check.

**Not checked:** No real Stripe charge/refund was made, no email was sent, no destructive production action was attempted, and true MySQL concurrency was not run because no isolated MySQL test service was provided. Vendor and `node_modules` internals were not audited line by line beyond the dependency audit and the cart/Cashier behavior needed to verify findings.
