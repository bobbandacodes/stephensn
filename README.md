# Prophet Stephen SN — CMS Website

Custom PHP + MySQL site for **Prophet Stephen SN ('The Paragon')**, pastor of **Higher Life Church**.

Bright, theme-able, fully editable from the admin console — no code edits needed for day-to-day content.

## Features

- **3 themes** (admin-switchable): Light Green (default), Dark Gold, Royal Purple
- **Editable everywhere** — every hero image, photo, headline, and body paragraph on the public site is editable from `/admin/settings.php`
- **Blog** with featured images and drafts/publish toggle
- **Events** & **Sermons** with cover images and registration links
- **Photo gallery** with categories (Prophet Stephen, Church, Events, Other)
- **Contact form** stored to DB with admin inbox
- **CSRF tokens**, prepared statements, hashed passwords, MIME-validated uploads

## Setup (XAMPP)

1. Start Apache + MySQL.
2. Open phpMyAdmin (`http://localhost/phpmyadmin`) → import [sql/schema.sql](sql/schema.sql).
3. Visit `http://localhost/stephensn/setup.php` once.
   This seeds the default settings and creates the admin:
   - **Username:** `admin`
   - **Password:** `temporary`
4. **Delete `setup.php`** immediately after.
5. Log in: `http://localhost/stephensn/admin/login.php`
6. Change your password under **Change Password**.

## What to do first in the admin

1. **Site Settings** → upload hero photo, about photo, ministry photo, give photo. Edit the hero/about/ministry copy.
2. **Theme** → pick the colour scheme.
3. **Gallery** → bulk-upload church and event photos, tag them by category.
4. **Blog Posts** → write the first post.
5. **Events** & **Sermons** → add upcoming dates and recorded messages.

All changes appear on the public site immediately.

## File structure

```
stephensn/
├── index.php                  Home (hero + about + ministry + events + posts + sermons + gallery)
├── about.php, ministry.php
├── blog.php, post.php         Blog list + single post
├── events.php, event.php
├── media.php
├── gallery.php                Filterable gallery
├── give.php
├── contact.php
│
├── admin/
│   ├── dashboard.php          Stats + quick actions
│   ├── settings.php           CMS — text + image slots
│   ├── theme.php              Theme picker
│   ├── posts.php              Blog CRUD
│   ├── events.php             Events CRUD
│   ├── sermons.php            Sermons CRUD
│   ├── gallery.php            Bulk photo upload + manage
│   ├── messages.php           Inbox
│   ├── password.php
│   └── login.php / logout.php
│
├── includes/
│   ├── config.php             DB credentials, base URL, paths
│   ├── db.php                 PDO + helpers (e, csrf, slugify, format_date)
│   ├── settings.php           setting() / setting_set() / theme system
│   ├── upload.php             handle_image_upload()
│   ├── auth.php               Session + login
│   ├── header.php / footer.php
│   └── _layout.php (admin)
│
├── assets/
│   ├── css/style.css          3 themes via CSS variables
│   ├── js/main.js             Mobile nav + lightbox
│   ├── img/
│   └── uploads/               All admin-uploaded photos
│
├── sql/schema.sql
└── setup.php                  DELETE after first run
```

## Themes

Themes are pure CSS variable swaps driven by `data-theme="..."` on `<html>`. Switch instantly from `/admin/theme.php`. To add a new theme:

1. Add a `[data-theme="my-theme"]` block in `assets/css/style.css` overriding the `--bg`, `--accent`, etc. variables.
2. Add to `theme_choices()` and the allowed list in `active_theme()` inside `includes/settings.php`.
3. Add a new card to `admin/theme.php`.

## Going to production

- Replace `BASE_URL` in `includes/config.php` to match your real path / domain.
- Set proper DB credentials in `includes/config.php`.
- Set `session.cookie_secure = 1` once on HTTPS.
- Wire real giving (M-Pesa Daraja, Stripe, Flutterwave) into `give.php`.
- Move uploads to a CDN if traffic grows.
