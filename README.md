# Alphonsus — WordPress theme

A custom, page-builder-free WooCommerce theme for **Alphonsus Library**: children's story books, Swedish language learning and wellness titles. It follows the *Warm Editorial Bookstore* design system in [`docs/DESIGN.md`](docs/DESIGN.md) (Noto Serif and Plus Jakarta Sans, sapphire and amber, paper surfaces).

Every page is a PHP template. Text, lists and images are edited in **Appearance → Customize**, so no page builder is needed.

## Requirements

| | Version |
|---|---|
| WordPress | 6.5+ |
| PHP | 7.4+ |
| WooCommerce | 8.0+ |
| Optional: PSM Multi-Currency for WooCommerce | for the currency picker |

Paystack (or any WooCommerce payment gateway) handles checkout, including foundation donations.

## Installation

1. Copy this folder to `wp-content/themes/alphonsus` (or install the zip from **Appearance → Themes → Add New → Upload**).
2. Activate the theme. On activation it:
   - creates the **About Us, Foundation, Contact, Swedish School, Wellness, Merch, Wishlist, Sign In and Create Account** pages if they're missing, and assigns their templates;
   - registers the product attributes used by the shop filters: `level`, `language`, `age-range` and `format`;
   - creates a hidden **Foundation donation** product;
   - flushes permalinks for the *Reading Journey* account page.

   To run these steps again: `wp eval 'alph_install();'`
3. Set a static front page in **Settings → Reading**. The homepage is rendered by `front-page.php`.
4. Assign the menus in **Appearance → Menus**:
   - Main navigation
   - Footer: Categories, Community, Support
5. Fill in the Customizer:
   - **Alphonsus Homepage & Site**: contact details, socials and every homepage section.
   - **Alphonsus Pages**: Book Shop, Foundation, About, Contact, Swedish School, Wellness and Merch.

## Content rule

Descriptive copy ships with sensible defaults. Anything that is a **factual claim** defaults to empty, and its section stays hidden until it is filled in with true information. That covers:
- statistics, ratings and testimonials;
- named staff, partners or champions;
- accreditations, addresses and opening hours;
- programme results and fund allocation.

Product star ratings only appear when a product has real WooCommerce reviews.

## Features

| Area | What it does | Where |
|---|---|---|
| Homepage | Hero with optional offer countdown, category pills, product rows by category or pinned IDs, Swedish promo, hardcover pre-order form, news and foundation card, newsletter | `front-page.php`, `template-parts/home/` |
| Book Shop | Sidebar filters (keyword, categories, CEFR level, language, age, format, price band, in stock), active-filter chips, 3-column grid, editorial section | `woocommerce/archive-product.php`, `inc/shop.php` |
| Product page | Gallery card, category chips, author line, live stock, price card, wishlist, delivery tiles, accordion (description, specifications, reviews, delivery & returns), related books | `woocommerce/content-single-product.php` |
| Foundation | Donation tiers plus custom amount, paid through WooCommerce checkout; optional pillars, impact hubs with map, fund allocation donut, partners, champions; volunteer / book-donation form | `page-templates/foundation.php`, `inc/donations.php` |
| About, Contact, Swedish School, Wellness, Merch | Split heroes, cards, FAQ accordions, timeline, gallery, course tracks and timetable, enrolment request form, bundle spotlight, merch filter pills | `page-templates/` |
| My Account | Welcome header with stats, icon menu, dashboard with reading summary, order tracking steps, enrolment requests, addresses and recommendations | `woocommerce/myaccount/`, `inc/account.php` |
| Reading Journey | Reading timer, offline log, daily goal ring, streaks, XP and levels, weekly chart, daily quests, book progress sliders, subject-mix donut, achievements, CSV export. Stored per customer in user meta | `inc/reading.php`, `template-parts/account/reading-journey.php` |
| Forms | Pre-order, contact, newsletter, Swedish School enrolment and foundation volunteer. Each is emailed and saved to **Form entries** in the admin (filter and CSV export included). Nonce, honeypot, per-IP rate limit and allow-listed choices | `inc/forms.php` |
| Sign in & registration | `/login/` and `/register/` pages plus restyled My Account sign-in, forgot-password and reset-password screens: brand panel, tabs, show/hide password, strength meter, first name and newsletter opt-in. Forms post to WooCommerce's handlers, so security plugins keep working. Needs *Allow customers to create an account on the My account page* turned on | `inc/auth.php`, `template-parts/auth/`, `woocommerce/myaccount/form-*.php` |
| Wishlist | Heart toggle on every card; cookie for guests, synced to the account for logged-in customers. Use the `[alphonsus_wishlist]` shortcode on the wishlist page | `inc/wishlist.php` |
| Search | Header search with category select and live suggestions (REST) | `inc/search.php` |
| Currency picker | Modal listing the Multi-Currency plugin's enabled currencies and rates; sets the plugin's cookie | `inc/currency.php` |
| Language | Google Translate, loaded only when a visitor opens the language menu | `assets/js/main.js` |
| Demo mode | Off by default. Customizer → Demo mode: each visitor gets a timed preview with a "Demo" bar and countdown, then the site shows a lock screen for the set number of hours. The timer survives page loads, cookie clearing and (optionally) private windows on the same network. Logged-in staff are never timed. Switching it off and on restarts every timer | `inc/demo.php`, `template-parts/demo-locked.php` |

Shortcodes: `[alphonsus_contact_form]`, `[alphonsus_preorder_form]`, `[alphonsus_wishlist]`.

Block styles for page content are **Card** (group/column), **Checklist** (list) and **Orange** (button).

## Structure

```
functions.php            Bootstrap
inc/                     Settings schema, Customizer, WooCommerce, forms, shop filters,
                         donations, currency, account, reading tracker, install
page-templates/          Foundation, About, Contact, Swedish School, Wellness, Merch
template-parts/          Homepage sections, forms, account screens, newsletter
woocommerce/             Shop archive, product page, product card, My Account overrides
assets/css/main.css      All front-end styles (design tokens at the top)
assets/js/main.js        All interactions; no jQuery or build step
assets/fonts/            Self-hosted Noto Serif and Plus Jakarta Sans (OFL)
```

There is no build step. Edit the files directly; asset URLs are cache-busted by file modification time.

## Notes

- Default image IDs in `inc/defaults-pages.php` refer to the original site's media library. On a fresh site, choose images in the Customizer.
- Donations are one-time gifts in the store currency (minimum ₦500) and are recorded as WooCommerce orders.

## Licence

GPL-2.0-or-later. Bundled fonts are under the SIL Open Font License.
