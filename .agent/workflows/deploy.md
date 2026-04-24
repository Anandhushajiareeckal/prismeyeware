---
description: How to deploy Prism Eyewear updates (Authentication & Redesign)
---

Follow these steps via SSH to deploy the latest changes to your production server.

### 1. Update the Code
If you are using Git, pull the latest changes:
```bash
git pull origin main
```
*If you are manually uploading files, ensure all modified files in `app/`, `resources/`, `routes/`, and `database/` are replaced.*

### 2. Update Dependencies (Optional)
If any new packages were added, run:
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Clear and Refresh Caches
Since routes and layouts have changed, you **must** clear the Laravel caches:
```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```
*Tip: You can run `php artisan optimize` afterwards to re-cache for better performance.*

### 4. Seed the Staff User
To create the dummy staff user on your live server, run this command:
```bash
php artisan tinker --execute="App\Models\User::firstOrCreate(['email' => 'staff@prismeyewear.com'], ['name' => 'Staff User', 'password' => \Illuminate\Support\Facades\Hash::make('Staff@1234'), 'email_verified_at' => now()]);"
```

### 5. Verify Permissions
Ensure the `storage` and `bootstrap/cache` directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 🔐 IMPORTANT: Default Admin
Your main admin account is:
- **Email:** `admin@prismeyewear.com`
- **Password:** `password` (Please change this immediately via the new **My Profile** page after logging in!)
