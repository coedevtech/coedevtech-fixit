# 🛠️ fixIt – Laravel Error Logging & Notification Package

`fixIt` is a Laravel package that captures and logs all exceptions into a database table — with optional encryption, email alerts, and a powerful CLI interface. Designed to give you full visibility into unhandled errors, without clutter or guesswork.

---

## 🚀 Features

- ✅ Logs all unhandled exceptions to the database
- 🔐 Optional field-level encryption using Laravel Crypt
- ⚙️ Configurable notification system (email-based out of the box)
- 🧪 Built-in Pest tests
- 📊 Artisan CLI: `fixit:report` to view, filter, and fix errors
- 💡 Extensible alert interface (use your own Slack, Discord, etc.)

---

## 🧩 Requirements

| Dependency | Version |
|------------|---------|
| **PHP**    | `^8.1` or `^8.2` or `^8.3` |
| **Laravel**| `^10.x` or `^11.x` or `^12.x` |

---

## 📦 Installation

```bash
composer require coedevtech/fixit
```

Then publish and install:

```bash
php artisan fixit:install
```

During installation, you’ll be prompted to enable encryption (optional). If enabled, a `FIXIT_ENCRYPTION_KEY` will be added to your `.env` file.

---

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=fixit-config
```

**`config/fixit.php`**

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Settings
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'enabled' => env('FIXIT_ENCRYPTION', false),
        'key' => env('FIXIT_ENCRYPTION_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'driver' => env('FIXIT_NOTIFICATION_DRIVER', 'email'),
        'send_on_error' => env('FIXIT_SEND_EMAIL', false),
        'email' => env('FIXIT_NOTIFICATION_EMAIL', 'admin@example.com'),
        'slack_webhook' => "",
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Logging Settings
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'table' => 'fixit_errors',
        'status_default' => 'not_fixed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Future Scalability
    |--------------------------------------------------------------------------
    | Use these fields for upcoming features like retention, log channels, etc.
    */
    'retention' => [
        'enabled' => false,
        'days' => 30,
    ],
];
```

---

## 🗃️ Database Table

`fixIt` creates a `fixit_errors` table with the following columns:

- `id`
- `url`
- `request`
- `response`
- `ip`
- `status` (`not_fixed`, `fixed`)
- `created_at`, `updated_at`

You can change the table name in the config.

---

## 📧 Email Notifications

To receive an email when an error is logged:

1. Set `send_on_error` to `true`
2. Set the `notifications.email` in the config file
3. Ensure Laravel mail is properly configured

---

## 🧪 Running Tests

```bash
./vendor/bin/pest
```

All tests are written using Pest and cover encryption, logging, config, and notifications.

---

## 🖥️ CLI Usage

View error logs:

```bash
php artisan fixit:report
```

Filter errors:

```bash
php artisan fixit:report --status=fixed
php artisan fixit:report --all
```

Mark error as fixed:

```bash
php artisan fixit:report --fix=3
```

---

## 🔌 Extending Alerts

You can bind your own alert channel by implementing the `Fixit\Contracts\FixitAlertInterface`.

Example for Slack, Discord, or webhook alerts.

```php
use Fixit\Contracts\FixitAlertInterface;

class SlackAlert implements FixitAlertInterface {
    public function send(string $message): void {
        // Your logic here
    }
}
```

Then bind it in a service provider:

```php
app()->bind(FixitAlertInterface::class, SlackAlert::class);
```

---

## 🛡️ Security & Best Practices

- Uses Laravel’s encryption system
- Avoids session or user tracking by default
- Decoupled and test-driven design
- Ready to extend with custom drivers or UI layers

---

## 📝 Changelog

See [Releases](https://github.com/coedevtech/coedevtech-fixit/releases) for full changelog.

