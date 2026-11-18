<div align="center">

# 📚 BookStore · Curated Literary Marketplace & E-Commerce Platform

### 🎓 Individual Final Exam Project · CSFTK Tunis
**Centre Sectoriel de Formation en Télécommunications de la Cité El Khadra (CSFTK Tunis)**  
*Département Informatique & Développement Web*

**Conceived, Architected & Developed by [Youssef Kaabechi](https://github.com/youceeef)**

[![Author](https://img.shields.io/badge/Author-Youssef%20Kaabechi-10B981?style=for-the-badge&logo=github&logoColor=white)](https://github.com/youceeef)
[![CSFTK Tunis](https://img.shields.io/badge/Exam%20Project-CSFTK%20Tunis-1E40AF?style=for-the-badge)](https://csftk.tn)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Stripe](https://img.shields.io/badge/Stripe-Payments-635BFF?style=for-the-badge&logo=stripe&logoColor=white)](https://stripe.com)
[![Tests](https://img.shields.io/badge/Tests-119%20Passing%20(100%25)-10B981?style=for-the-badge&logo=githubactions&logoColor=white)]()
[![Status](https://img.shields.io/badge/Production-Live%20on%20Railway-success?style=for-the-badge&logo=railway&logoColor=white)](https://bookstores.up.railway.app)

[🌐 Live Production Demo](https://bookstores.up.railway.app) &nbsp;|&nbsp; [📖 Evaluator's Guide](#-evaluators--jury-quick-guide) &nbsp;|&nbsp; [⚡ Feature Matrix](#-comprehensive-feature-matrix) &nbsp;|&nbsp; [📸 Visual Showcase](#-visual-showcase) &nbsp;|&nbsp; [🚀 Local Installation](#-installation--local-development)

</div>

---

## 🏛️ Project Identification & Academic Attribution

| Field | Details |
| :--- | :--- |
| **Project Title** | **BookStore · Curated Literary Marketplace & E-Commerce Platform** |
| **Author & Developer** | **[Youssef Kaabechi](https://github.com/youceeef)** (Sole Author & Full-Stack Engineer) |
| **Institution** | **CSFTK Tunis** (Centre Sectoriel de Formation en Télécommunications de la Cité El Khadra) |
| **Academic Context** | **Individual Practical Final Exam Project** (*Projet d'Examen Pratique Individuel*) |
| **Specialization** | Software Engineering, Enterprise Web Architecture & Distributed E-Commerce |
| **Live Environment** | Deployed on **Railway Cloud** with MySQL, SSL termination, and Resend HTTPS mail integration |
| **Production URL** | **[https://bookstores.up.railway.app](https://bookstores.up.railway.app)** |

> [!IMPORTANT]
> **Academic Declaration:**  
> This application was exclusively designed, developed, and deployed by **Youssef Kaabechi** as an **individual practical exam project** for **CSFTK Tunis**. It reflects complete individual ownership across all project tiers: relational database modeling, transaction locking, multi-channel authentication, third-party payment integration (Stripe), server-side PDF document generation, and test-driven architecture.

---

## 🧭 Evaluator's & Local Testing Guide

For examiners, jury members, or evaluators testing the platform in local development or sandbox environments, use the pre-configured default credentials below:

### 🔑 Local & Sandbox Evaluation Credentials

> [!NOTE]
> The credentials below apply to local development and sandbox environments. Live production deployments configure credentials securely via environment variables (`ADMIN_EMAIL`, `ADMIN_PASSWORD`) and disable shortcut inspectors.

| Role | Email | Password | Access Privileges |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@example.com` | `password` | Full Administrative Suite (`/admin`), inventory CRUD, fulfillment, and mailbox inspector |
| **Verified Customer** | `customer@example.com` | `password` | Verified reader account with order history, address records, and wishlist |

### 🧪 Guided Evaluation Scenarios

1. **Reader Discovery & Reading Sample ("Look Inside"):**
   - Open `/books`, click on any book (e.g., *Threads of Fate*).
   - Click **"Look Inside (Sample Excerpt)"** to launch the interactive typography reader modal. Adjust font sizes, toggle between Serif/Sans, and test full-screen reading mode.
2. **Persistent Wishlist & Guest Migration:**
   - In an incognito window as an unauthenticated guest, click the heart icon on any 2 books.
   - Go to `/login` and sign in as `customer@example.com` — notice that your guest wishlist items are immediately migrated and merged into the user database record.
3. **Cart & Promotional Coupon Engine:**
   - Add books to the cart and apply promotional coupon code `WELCOME10` (10% off) or `SAVE5` ($5 off). Subtotals, discounts, and tax calculate in real time.
4. **Stripe Checkout & Automated PDF Invoicing:**
   - Complete checkout using Stripe demo cards.
   - View your order history under `/orders`, click **"Download Tax Invoice (PDF)"** to inspect the server-side generated DomPDF receipt.
5. **Fulfillment Tracking Lifecycle:**
   - Sign in as `admin@example.com` and open `/admin/orders`.
   - Update an order from `Processing` to `Shipped`, assign carrier (`DHL Express`) and a tracking code.
   - Switch back to customer view to see the interactive status tracker update.
6. **60-Minute Signed Email Verification & Sandbox Simulator:**
   - Register a new account at `/register`. The account is immediately placed **On Hold** for 60 minutes.
   - To verify without waiting for external mail, click **"⚡ Activate Account Instantly"** on screen, or inspect incoming tokens at `/admin/mailbox`.

---

## 📸 Visual Showcase

*(All screenshots captured directly from the live production deployment)*

### 1. Curated Homepage & Editorial Catalog
> Clean layout adhering to editorial design principles: Fraunces serif headlines, stone palette, search, genre filter pills, and live availability indicators.

![Homepage](docs/homepage.png)

---

### 2. Book Detail, Excerpt Reader & Verified Buyer Reviews
> Detailed bibliographic metadata, verified buyer ratings, helpfulness vote counters, and the interactive reading modal.

<p align="center">
  <img src="docs/book-details.png" alt="Book Details" width="48%" />
  <img src="docs/reader_modal.png" alt="Look Inside Reader Modal" width="48%" />
</p>

---

### 3. Shopping Bag & Stripe Checkout
> High-clarity checkout flow featuring dynamic coupon redemption, pessimistic stock locking, and Stripe Elements integration.

<p align="center">
  <img src="docs/cart.png" alt="Shopping Bag" width="48%" />
  <img src="docs/checkout.png" alt="Stripe Checkout" width="48%" />
</p>

---

### 4. Reader Wishlist & Multi-Device Synchronization
> Persistent wishlist supporting both session-based guest caching and relational database storage for authenticated members.

![Wishlist](docs/wishlist.png)

---

### 5. Reader Onboarding & Profile Management
> Account security with real-time password strength verification, 60-minute signed activation links, avatar uploads, and shipping address management.

<p align="center">
  <img src="docs/register.png" alt="Reader Registration" width="48%" />
  <img src="docs/profile.png" alt="Reader Profile" width="48%" />
</p>

---

### 6. Administrative Operations Suite
> Full management control: sales analytics, inventory CRUD with image uploads, coupon parameters, and transactional mailbox inspection.

<p align="center">
  <img src="docs/admin_dashboard.png" alt="Admin Dashboard" width="48%" />
  <img src="docs/admin_books.png" alt="Admin Book Management" width="48%" />
</p>

<p align="center">
  <img src="docs/admin_categories.png" alt="Admin Categories" width="48%" />
  <img src="docs/admin_mailbox.png" alt="Transactional Mailbox Inspector" width="48%" />
</p>

---

## ✨ Comprehensive Feature Matrix

### 📖 Reader & E-Commerce Experience
- **Editorial Typography & Dark Mode:** Designed with bespoke typography (Fraunces serif + Inter sans) and a zero-flicker anti-FOUC dark mode script with local storage persistence.
- **Trilingual Support (EN / FR / AR RTL):** Native locale switcher supporting English, French, and Arabic with comprehensive right-to-left (RTL) stylesheet mirroring.
- **Interactive Sample Reading Modal ("Look Inside"):** Distraction-free chapter reader with adjustable font sizing, sans/serif font switches, progress tracking, and full-screen view.
- **Verified Reviews & Star Rating Engine:** 5-star rating aggregates, verified buyer badges linked to order history, helpfulness voting, and duplicate protection.
- **Persistent Wishlist:** Dual-layer storage (session storage for guests, MySQL relation for authenticated users) with seamless migration on login.
- **Rich User Profile:** Custom avatar upload with preview/delete, multi-line shipping and billing address manager, bio, and one-click session logout across other devices.
- **Server-Side PDF Invoices:** Instant branded invoice generation with barcode formatting, itemization, and tax calculation via DomPDF.
- **Visual Order Fulfillment Tracker:** Real-time visual timeline (`Processing` → `Shipped` with carrier and tracking code → `Delivered`).

### 🛡️ Enterprise Architecture & Security
- **Payment & Checkout State Machine:** Idempotent checkout charge (`order_charge_{id}`), pending order creation before charge, GET-safe return endpoint `checkout.return` for SCA / 3D-Secure.
- **Pessimistic Concurrency Locking (`lockForUpdate`):** Database-level transaction locking on inventory stock and promotional coupon limits to eliminate race conditions and double-spending during checkout spikes.
- **Stripe HMAC Webhook Signature Verification:** Dedicated middleware validating Stripe signatures and rejecting forged or unsigned requests with 403 in production.
- **Order Retention & Anonymization:** Account deletion anonymizes customer personal data while preserving immutable order and invoice snapshots; sold books are archived rather than cascade-deleted.
- **Idempotent Order Cancellation:** Restores stock reservations and coupon capacity inside transactions, with automatic Stripe refund processing.
- **60-Minute Signed Email Verification:** Strict account hold upon signup, HMAC signed URLs with 60-minute expiration, and an expired-link recovery engine.
- **Modern Resend HTTPS Mail Transport:** Dispatches transactional emails via HTTPS (port 443), permanently bypassing cloud-provider SMTP port firewall blocks (`25`, `465`, `587`).
- **Production Edge Security & Startup:** HTTP Strict Transport Security (HSTS), secure HTTPS-only session cookies, and zero-downtime startup via `deploy/start.sh` (Nginx + PHP-FPM with automated migrations via `AUTORUN_MIGRATIONS=true`).
- **Media Storage Isolation:** Unified media disk configured via `MEDIA_DISK` (local `public` or S3/cloud) with relative asset paths.
- **Transactional Mailbox Inspector (`/admin/mailbox`):** In-app inspection console capturing all dispatched activation and password reset tokens in local/sandbox environments (strictly blocked in production).

### 💼 Back-Office Administrative Suite
- **Analytics Dashboard:** Revenue totals, sales metrics, gross volume, and stock replenishment alerts.
- **Book Inventory CRUD:** Complete catalog management with file cover upload, sample excerpt markdown editing, pricing, category allocation, and stock tracking.
- **Category Hierarchy:** Genre categorization with SEO slug generation and active/inactive visibility toggles.
- **Coupon Engine:** Percentage and fixed discounts, minimum spend thresholds, usage limits, and expiration date guards.
- **Order Fulfillment Dispatch:** Inspect customer orders, update shipping statuses, select couriers (DHL, FedEx, UPS, Royal Mail), and log tracking numbers.

---

## 🏗️ Architecture & Technology Stack

```
BOOK_STORE/
├── app/
│   ├── Http/Controllers/       # Thin Controllers (Admin, Auth, Shop, Cart, Orders)
│   ├── Models/                 # Eloquent Entities with Relationships & Casts
│   ├── Notifications/          # Signed 60-min Email Verification & Password Reset
│   ├── Services/               # Cart, Wishlist, and Invoice Business Logic
│   └── Providers/              # EventServiceProvider & RouteServiceProvider
├── config/                     # Mail (Resend/SMTP), Services, Database, Auth
├── database/
│   ├── migrations/             # Schema definitions with foreign keys and indices
│   └── seeders/                # Idempotent demo seeders (Admin, Books, Orders, Coupons)
├── resources/
│   ├── css/ & js/              # Tailwind CSS configuration & Alpine.js components
│   └── views/                  # Blade templates (Modular layouts, components, admin)
├── routes/
│   ├── web.php                 # Customer, Shop, Cart, and Admin routes
│   └── auth.php                # Authentication & Verification routes
└── tests/
    └── Feature/                # 119 Feature & Unit tests (100% passing)
```

| Component | Technology | Rationale |
| :--- | :--- | :--- |
| **Backend Framework** | [Laravel 12.x](https://laravel.com) | Modern enterprise PHP framework with robust ORM, security, and routing |
| **Language** | [PHP 8.2+](https://php.net) | Modern typed PHP with typed properties, match expressions, and performance |
| **Frontend Styling** | [Tailwind CSS 3.x](https://tailwindcss.com) | High-performance utility styling without bloated CSS bundles |
| **Client Scripting** | [Alpine.js 3.x](https://alpinejs.dev) | Declarative, lightweight micro-reactivity without heavy SPA frameworks |
| **Database** | [MySQL 8.0+](https://mysql.com) | Relational integrity with transaction isolation and indexing |
| **Payments** | [Stripe Cashier](https://stripe.com) | PCI-compliant payments with 3D Secure verification |
| **PDF Engine** | [DomPDF](https://github.com/barryvdh/laravel-dompdf) | Clean HTML-to-PDF rendering for archival invoices |
| **Mail Delivery** | [Resend API](https://resend.com) | High-speed HTTPS API email transmission over Port 443 |

---

## 🚀 Installation & Local Development

### System Requirements
- **PHP** >= 8.2 (Extensions: `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `bcmath`, `curl`)
- **Composer** >= 2.0
- **Node.js** >= 18.0 & **npm**
- **MySQL** >= 8.0 (or SQLite for automated testing)

### Installation Steps

```bash
# 1. Clone the repository
git clone https://github.com/youceeef/BOOK_STORE_PROD.git
cd BOOK_STORE_PROD

# 2. Install backend dependencies
composer install

# 3. Install frontend dependencies
npm install

# 4. Set up environment configuration
cp .env.example .env
php artisan key:generate

# 5. Configure Database (.env)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bookstore
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Run migrations & seed demo dataset
php artisan migrate --seed

# 7. Create storage symlink for uploaded media
php artisan storage:link

# 8. Compile production assets
npm run build

# 9. Launch local development server
php artisan serve
```

Access the local instance at: **`http://127.0.0.1:8000`**

### 🚢 Production Deployment (Railway / Nixpacks)

The repository includes a production startup controller in `deploy/start.sh` executed by `nixpacks.toml` and `Procfile`:

- **Automated Safe Migrations:** Set `AUTORUN_MIGRATIONS=true` in the Railway environment to safely apply migrations (`php artisan migrate --force --no-interaction`) before the web server begins receiving traffic.
- **Media Disk Configuration:** Set `MEDIA_DISK=public` (for persistent volume storage) or `MEDIA_DISK=s3` with standard AWS credentials.
- **Production Web Server:** Runs optimized `php-fpm` and `nginx` with automatic cache warm-up (`config:cache`, `route:cache`, `view:cache`).
- **Webhooks:** Point your Stripe webhook endpoint to `https://your-domain.com/stripe/webhook` and define `STRIPE_WEBHOOK_SECRET` in environment variables.

---

## 🧪 Automated Testing & Code Quality

The application includes an extensive test suite verifying business logic, security constraints, and UI rendering:

```bash
# Execute the full automated test suite
php artisan test
```

```
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Feature\AdminDashboardTest
   PASS  Tests\Feature\AdminMailboxTest
   PASS  Tests\Feature\Auth\AuthenticationTest
   PASS  Tests\Feature\Auth\EmailVerificationTest
   PASS  Tests\Feature\Auth\PasswordConfirmationTest
   PASS  Tests\Feature\Auth\PasswordResetTest
   PASS  Tests\Feature\Auth\PasswordUpdateTest
   PASS  Tests\Feature\Auth\RegistrationTest
   PASS  Tests\Feature\CartInteractionTest
   PASS  Tests\Feature\CheckoutStateMachineTest
   PASS  Tests\Feature\CheckoutTest
   PASS  Tests\Feature\CommandPaletteTest
   PASS  Tests\Feature\ConcurrencyCheckoutTest
   PASS  Tests\Feature\InventoryAndCatalogTest
   PASS  Tests\Feature\InvoiceTest
   PASS  Tests\Feature\LocalizationAndThemeTest
   PASS  Tests\Feature\OrderDataRetentionTest
   PASS  Tests\Feature\OrderFulfillmentTest
   PASS  Tests\Feature\ProfileTest
   PASS  Tests\Feature\ReaderModalTest
   PASS  Tests\Feature\ReviewTest
   PASS  Tests\Feature\StripeWebhookSignatureTest
   PASS  Tests\Feature\WishlistTest

  Tests:    119 passed (434 assertions)
  Duration: 5.2s
```

---

## 🧑‍💻 Author & Academic Credits

- **Full-Stack Developer & Author:** **[Youssef Kaabechi](https://github.com/youceeef)**
- **Academic Context:** Individual Practical Final Exam Project (*Projet d'Examen Pratique Individuel*)
- **Institution:** **CSFTK Tunis** (Centre Sectoriel de Formation en Télécommunications de la Cité El Khadra)
- **Département:** Informatique & Développement Web
- **Year of Presentation:** 2026
- **Repository:** [https://github.com/youceeef/BOOK_STORE_PROD](https://github.com/youceeef/BOOK_STORE_PROD)
- **License:** Open-source software licensed under the [MIT License](LICENSE).
