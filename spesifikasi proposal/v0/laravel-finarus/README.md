# FinFlow Laravel - Project Template

Project template Laravel Breeze dengan tampilan identik ke FinFlow Next.js original.

## Struktur Folder

```
laravel-finflow/
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php          # Main layout
│   ├── components/
│   │   ├── sidebar.blade.php      # Sidebar navigation
│   │   └── header.blade.php       # Header component
│   ├── dashboard.blade.php        # Dashboard page
│   ├── transaksi/index.blade.php  # Transactions page
│   ├── kategori/index.blade.php   # Categories page
│   ├── anggaran/index.blade.php   # Budget page
│   ├── tabungan/index.blade.php   # Savings goals page
│   ├── laporan/index.blade.php    # Reports page
│   ├── dompet/index.blade.php     # E-Wallet page
│   ├── pengaturan.blade.php       # Settings page
│   └── bantuan.blade.php          # Help page
├── public/logos/                  # 8 Indonesian bank & e-wallet logos
├── routes/web.php                 # All routes configured
└── app/                           # Models & Controllers (ready for your logic)
```

## Setup Instructions

### 1. Create Laravel Breeze Project
```bash
composer create-project laravel/laravel finflow
cd finflow
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
```

### 2. Copy Template Files
```bash
# Copy views
cp -r /path/to/laravel-finflow/resources/views/* resources/views/

# Copy logos
mkdir -p public/logos
cp /path/to/laravel-finflow/public/logos/* public/logos/

# Copy routes
cp /path/to/laravel-finflow/routes/web.php routes/
```

### 3. Run Application
```bash
# Terminal 1
npm run dev

# Terminal 2
php artisan serve
```

Visit http://localhost:8000

## Features

✅ Responsive Design (Mobile, Tablet, Desktop)
✅ Dark Mode Support
✅ Sidebar Navigation
✅ Header with Search & Notifications
✅ Tailwind CSS Styling
✅ 8 Dashboard Pages
✅ 8 Indonesian Bank & E-Wallet Logos
✅ Authentication Ready (Breeze)
✅ Smooth Animations

## Pages Included

- Dashboard - Financial overview
- Transactions - Transaction management
- Categories - Category management
- Budget - Budget planning
- Savings Goals - Savings goal tracking
- Reports - Financial reports & analytics
- E-Wallet & Banking - Account management
- Settings - User preferences
- Help - FAQ & support

## Frontend Pages with Dummy Data

All pages are ready with UI and sample data. For backend integration:

1. Create Models for each entity (Transaction, Category, Budget, etc.)
2. Create Controllers to fetch data from database
3. Update routes to pass data to views
4. Modify forms to submit to your API/routes

## Customization

### Colors
Edit Tailwind colors in `tailwind.config.js`

### Dark Mode
Edit `resources/views/components/header.blade.php` script section

### Add Pages
Create new file in `resources/views/yourpage.blade.php` and add route in `routes/web.php`

## Next Steps

1. Setup your database based on proposal
2. Create Models: `php artisan make:model Transaction -mcr`
3. Create Migrations for database tables
4. Create Controllers to handle business logic
5. Update views with form actions pointing to your controllers
6. Add validation & API logic

## Support

- Laravel Docs: https://laravel.com/docs
- Breeze: https://laravel.com/docs/breeze
- Tailwind: https://tailwindcss.com/docs

## License

MIT License

