# 🛠️ fixIt – Laravel Error Logging & Notification Package

`fixIt` is a Laravel package that captures and logs all exceptions into a database table — with optional encryption, email or Slack alerts, and a powerful CLI interface. Designed to give you full visibility into unhandled errors, without clutter or guesswork.

---

## 🚀 Features

- ✅ Logs all unhandled exceptions to the database
- 🔐 Optional field-level encryption using Laravel Crypt
- ⚙️ Configurable notification system (email + Slack supported)
- 🧠 AI-powered fix suggestions (optional)
- 🌪️ Built-in Pest tests
- 📊 Artisan CLI: `fixit:report` to view, filter, and fix errors
- ✍️ `fixit:sync-config` to merge missing config keys
- 🛠️ `fixit:sync-migrations` to publish and run package migrations
- 💡 Extensible alert interface (plug your own Discord, webhook, etc.)

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

To check for missing config keys later, run:

```bash
php artisan fixit:sync-config
```

To automatically append missing keys using short `[]` array syntax:

```bash
php artisan fixit:sync-config --write
```

---

## 🔐 Manual Encryption / Decryption

`fixIt` provides two static methods via its facade to manually encrypt or decrypt data:

### Encrypt data

```php
use Fixit\Facades\Fixit;

$encrypted = Fixit::encrypt(['email' => 'user@example.com']);
```

This encrypts any string or array using AES-256-CBC with a secure IV and stores it base64-encoded.

### Decrypt data

```php
$decrypted = Fixit::decrypt($encrypted);
```

This will return the original value (array or string), decrypted securely.

---

## 🗃️ Database Table

`fixIt` creates a `fixit_errors` table with the following columns:

- `id`
- `url`
- `request`
- `response`
- `ip`
- `status` (`not_fixed`, `fixed`)
- `fingerprint`
- `last_seen_at`
- `occurrences`
- `created_at`, `updated_at`

You can change the table name in the config.

---

## ⚒️ Publishing Migrations

To publish and run any new package-provided migrations (e.g. adding new columns):

```bash
php artisan fixit:sync-migrations
```

This ensures that columns like `fingerprint`, `last_seen_at`, and `occurrences` are always present.

---

## 📧 Email Notifications

To receive an email when an error is logged:

1. Set `send_on_error` to `true`
2. Set the `notifications.email` in the config file
3. Ensure Laravel mail is properly configured

> 🧠 If you're using `QUEUE_CONNECTION=database` or `QUEUE_CONNECTION=redis`, you must run:
>
> ```bash
> php artisan queue:work
> ```
>
> Otherwise, queued emails will not be sent and may block request execution depending on your queue setup.

---

## 🧠 AI Suggestions (Optional)

`fixIt` supports AI-powered suggestions for fixing logged errors. This is completely optional.

To enable:

1. Set `FIXIT_AI_ENABLED=true` in your `.env`
2. Set either:
   - `FIXIT_AI_API_URL` (for your custom AI proxy)
   - or `FIXIT_AI_API_KEY` (to use OpenAI directly)
3. Set `FIXIT_AI_PROVIDER=openai` or `fixit-proxy`

If enabled, suggestions are included in:
- 📧 Email alerts
- 💬 Slack alerts
- Future CLI/reporting support

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

Sync and patch your config file:

```bash
php artisan fixit:sync-config          # show missing keys
php artisan fixit:sync-config --write  # append missing keys to config/fixit.php
```

Publish and apply package migrations:

```bash
php artisan fixit:sync-migrations
```

---

## 🔌 Extending Alerts

You can bind your own alert channel by implementing the `Fixit\Contracts\FixitAlertInterface`.

Example for Slack, Discord, or webhook alerts:

```php
use Fixit\Contracts\FixitAlertInterface;

class SlackAlert implements FixitAlertInterface {
    public function send(string $message, ?Throwable $exception = null, ?string $suggestion = null): void {
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