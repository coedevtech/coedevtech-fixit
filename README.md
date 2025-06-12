# FixIt – Laravel Error Logger Package

FixIt is a Laravel package that automatically logs all unhandled exceptions to a database table. It supports optional AES encryption and email alerts for faster debugging and incident response.

## 🚀 Features

- ✅ Logs all Laravel exceptions to the database
- 🔐 Optional encryption of error data
- 📩 Sends email alerts when an error occurs
- 🛠 Artisan tools: `fixit:status`, `fixit:clear`, `fixit:purge-old`
- 📦 Config file publishing
- 🔁 Retention policy for auto-cleaning old logs
- 🔬 Pest test coverage
- 🧩 PHP 8.0+ and Laravel 9/10/11 compatible

---

## 📦 Installation

```bash
composer require your-vendor/fixit
php artisan fixit:install

The installation command will:

Prompt you to enable/disable encryption

Generate a secure encryption key (if selected)

Publish the config and migration files

Optionally run migrations


🔧 Artisan Commands

php artisan fixit:status      # View error stats
php artisan fixit:clear       # Clear logs (with filters)
php artisan fixit:purge-old   # Delete logs older than N days

🛡 Security Notes
Sensitive data is encrypted using Laravel’s Crypt if enabled.

Logging fails gracefully if exceptions are thrown during the save.

Use fixit:purge-old with a scheduler to keep your DB lean.

🧪 Testing

./vendor/bin/pest

🤝 Contributing
Pull requests and ideas are welcome! Please fork the repo and submit a PR to the main branch.

📄 License
This package is open-sourced software licensed under the MIT license.

🔗 Credits
Created and maintained by Chukwuneke Onyedika
Built with ❤️ for the Laravel community.
