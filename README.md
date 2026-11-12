# TFA8 — GTA V Laravel POS

**Build required:** this source package does not contain `vendor/`. It is not the ZIP to upload directly to InfinityFree. Build it using either option below; the build creates `dist/TFA8-InfinityFree-Upload.zip` only after the PHP checks and tests pass.

## What this continues

This uses the attached TFA7 project and the attached IT0049 TFA8 assessment. It retains the GTA V sample records, customer fields, username/full name fields, and the 400 × 400 JPEG avatar feature. It keeps Laravel 12, the framework version required by the supplied TFA7 composer.json. No separate module handouts were attached.

- `Route::resource('customers', CustomerController::class)` and `Route::resource('users', UserController::class)` are inside `auth` middleware.
- Both controllers implement index, create, store, show, edit, update and destroy.
- Breeze is a Composer development dependency. The build runs the real Blade scaffold installer, then reapplies the customized TFA8 source: email login, registration with username/full_name, password hashing, login throttling, session regeneration and POST logout.
- `User` extends Laravel's `Authenticatable` model. Passwords and remember tokens are hidden from serialization.
- Guest requests to all customer/user resource actions redirect to `/login`. Successful login returns to the intended protected page.
- The UI uses local CSS, so the hosted application has no Vite manifest or Node process requirement. Node is used during the Breeze build step only.
- Any registered user can manage records, as the assessment requests. No administrator role is specified in the supplied activity. Public registration therefore grants management access.
- A signed-in user cannot delete their own account. All state-changing forms include CSRF tokens.
- Avatars allow JPG/PNG/WebP up to 2 MB and dimensions up to 6000 × 6000; uploads are re-encoded, assigned random filenames, and served through an authenticated route. No storage symlink is required.

## Option A: build through GitHub (no local PHP required)

1. Create your GitHub repository and upload the **contents of this project folder**, including `.github/workflows/build.yml`, `.env.example`, migrations and tests. Do not upload the outer package's `PRIVATE` folder or any real `.env`.
2. Open your repository's **Actions** tab. Select **Build and test TFA8 InfinityFree package**. A push to main/master starts it automatically, or select **Run workflow**.
3. Wait for the job to finish successfully. A failed job does not produce a hosting package; open the failed step to see the actual error.
4. Under **Artifacts**, download **TFA8-InfinityFree-Upload**. Extract the artifact to obtain `TFA8-InfinityFree-Upload.zip`, then extract that ZIP on your computer.
5. Follow the hosting steps below. This route leaves database credentials as placeholders; fill them on the hosting account.

This workflow has been supplied but could not be executed from the authoring environment. It requires GitHub Actions access and internet access to package repositories. If your account requires enabling Actions, do that in the repository settings.

## Option B: build on your Windows computer

Requirements: PHP 8.2 or newer with mbstring, dom/xml, curl, fileinfo, gd, pdo_mysql, pdo_sqlite and zip; Composer 2; Node.js 22 LTS with npm; internet access. PHP in XAMPP must have those extensions enabled. Open a new VS Code terminal after installing tools so PATH is refreshed.

1. Copy `PRIVATE/hosting.env` from the outer package into this project folder and rename the copy to `.env`. That file retains the hosting connection supplied in your TFA7 attachment. Use it only if you will host TFA8 on that same account/database. Update `APP_URL` to the actual TFA8 address and change database settings if using a different database.
2. In VS Code, open this project folder and run this in a PowerShell terminal:

```powershell
powershell -NoProfile -File .\build.ps1
```

3. The script installs dependencies and Breeze, reapplies project customizations, checks PHP syntax, compiles views, runs tests on a separate in-memory SQLite database, installs production dependencies and builds the upload ZIP. It does not connect to or modify your hosting database.
4. Use `dist/TFA8-InfinityFree-Upload.zip` after the script reports success.

Never upload `.env`, `PRIVATE/hosting.env` or the hosting package to a public GitHub repository. Submit the source folder instead. The source folder contains `.env.example` for your professor.

## Upload to InfinityFree

InfinityFree does not provide the Composer/Artisan shell workflow on free hosting. Build before uploading.

1. Back up your current website and database. Prefer a separate TFA8 domain and database if TFA7 must remain available for grading.
2. In the hosting control panel, create/select the MySQL database and open phpMyAdmin.
3. Choose **one** database path:
   - **Empty/new database:** import `database-fresh.sql` from the built package. This creates the tables and five GTA V sample records.
   - **Existing database matching the attached TFA7 schema:** import `database-upgrade-tfa7.sql` once. It adds authentication fields and preserves existing customers, users and avatar paths. Do not import the fresh schema over existing tables. The upgrade is intentionally for the supplied TFA7 schema; it is not a universal migration for other database layouts.
4. Using FTP (for example FileZilla), upload **the contents of the built package's `htdocs` folder** into your domain's existing `htdocs` directory. Do not create `htdocs/htdocs`. Remove an existing hosting welcome `index.html` if it takes priority over index.php. Include hidden `.htaccess` and `.env` files. Upload extracted files rather than assuming the web file manager can extract the full Laravel ZIP.
5. Your hosting layout must be:

```text
htdocs/index.php
htdocs/.htaccess
htdocs/style.css
htdocs/avatar-placeholder.svg
htdocs/_app/.env
htdocs/_app/.htaccess
htdocs/_app/vendor/autoload.php
htdocs/_app/app/...
```

6. Edit `htdocs/_app/.env`:
   - `APP_URL=https://your-actual-domain` (use the domain that hosts TFA8; only use HTTPS after its certificate is active).
   - Set `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` to that account's exact MySQL details. The generated GitHub package has placeholders.
   - Preserve the generated `APP_KEY`. Do not regenerate it on every request or subsequent deployment.
   - Keep `APP_ENV=production`, `APP_DEBUG=false`, file sessions/cache, and synchronous queues.
7. Ensure `_app/storage` and `_app/bootstrap/cache` are writable by PHP. FTP folders normally use 755; use your host's recommended permissions if writes fail.
8. Open `/register` and create a new account using a new username and email. It signs you in. Existing GTA V users have no shared default password; once signed in, edit one to assign a unique email and password if it should become a login account.
9. If preserving avatars from the live TFA7 website, copy its actual `storage/app/public/avatars` files to `htdocs/_app/storage/app/public/avatars`. The attached TFA7 ZIP contains no uploaded avatar images.
10. Check that `https://your-domain/_app/.env` and `/_app/vendor/autoload.php` return **403 Forbidden**. The `_app/.htaccess` denies public access to private project files. Public requests run through the root `index.php`.

There is no browser installer, public database migration endpoint, shared login password, or shell command required on the hosting account.

## Access-control evidence for your submission

1. Log out. Open `/customers`, `/users`, `/customers/create` and `/users/create`; verify that each redirects to `/login`.
2. Log in. Open those same pages and verify that they load normally. The attempted protected page should open after successful login.
3. Create, view, edit and delete a test customer. Create a separate test user, edit its email/password, upload an avatar, view it and delete that user.
4. Try an invalid email, duplicate username/email, wrong password, non-image avatar and image larger than 2 MB. Confirm validation messages appear.
5. Log out again and use the browser Back button, then reload the protected page. It must redirect to login.
6. Keep screenshots of these checks if your professor requests evidence. Submit your GitHub repository URL and your actual working hosted URL, as stated in the activity.

## Password recovery

Login and registration do not require email delivery. Password-reset pages/controllers are included, but the forgot-password link is hidden while `MAIL_MAILER=log`. Configure a working SMTP provider accepted by your hosting environment before enabling SMTP password recovery. The app does not claim to email a reset link while mail delivery is disabled.

## Validation status and limits

The authoring environment had no PHP/Composer runtime and blocked dependency downloads. Consequently, no PHP execution, PHPUnit result, real Breeze installation, or live InfinityFree verification was available during preparation. Static source checks are recorded in the outer package's `CHECKS.txt`. The included build must pass before treating the hosting ZIP as ready.

The automated suite covers public pages, guest redirects for all resource verbs, registration and password hashing, login/intended redirects/logout, login throttling, CRUD and unique validation, avatar conversion/replacement/type/size checks, and seed records. The workflow also checks PHP syntax and Blade compilation. These checks do not replace a final check on your own hosting account.

References consulted: Laravel resource controllers (https://laravel.com/docs/12.x/controllers#resource-controllers), Breeze compatibility (https://github.com/laravel/breeze/blob/2.x/composer.json), InfinityFree framework support (https://forum.infinityfree.com/t/can-i-use-php-frameworks-like-symfony-and-laravel-in-free-hosting/37683).
