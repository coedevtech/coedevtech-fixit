# 🛠️ fixIt – Laravel Error Logging & Notification Package

`fixIt` is a Laravel package that captures and logs all exceptions into a database table — with optional encryption, email or Slack alerts, and a powerful CLI interface. Designed to give you full visibility into unhandled errors, without clutter or guesswork.

---

## 🚀 Features

- ✅ Logs all unhandled exceptions to the database
- 🔐 Optional field-level encryption using Laravel Crypt
- 📧 **Multi-recipient email alerts support**
- ⚙️ Configurable notification system (email + Slack supported)
- 🧠 AI-powered fix suggestions (optional)
- 🧪 `fixit:verify-config` to validate and auto-patch `.env`
- 📊 Artisan CLI: `fixit:report` to view, filter, and fix errors
- ✍️ `fixit:sync-config` to merge missing config keys
- 🛠️ `fixit:sync-migrations` to publish and run package migrations
- 💡 Extensible alert interface (plug your own Discord, webhook, etc.)

---

## 🧩 Requirements

| Dependency | Version |
|------------|---------|
| **PHP**    | `^8.1`, `^8.2`, or `^8.3` |
| **Laravel**| `^10.x`, `^11.x`, or `^12.x` |

---

## 📦 Installation

```bash
composer require coedevtech/fixit
```

Then run the installer:

```bash
php artisan fixit:install
```

---

## ⚙️ Configuration

To publish the config file:

```bash
php artisan vendor:publish --tag=fixit-config
```

To verify and auto-patch missing `.env` keys:

```bash
php artisan fixit:verify-config
php artisan fixit:verify-config --fix
```

For JSON output (CI pipelines):

```bash
php artisan fixit:verify-config --json
```

---

## 🔐 Manual Encryption / Decryption

```php
use Fixit\Facades\Fixit;

$encrypted = Fixit::encrypt(['email' => 'user@example.com']);
$decrypted = Fixit::decrypt($encrypted);
```

---

## 📧 Multi-Email Notifications

Configure in `.env`:

```env
FIXIT_SEND_EMAIL=true
FIXIT_NOTIFICATION_EMAIL=admin@example.com,dev@example.com
FIXIT_ALLOW_MULTIPLE_EMAILS=true
```

> Emails will be sent to all valid addresses if `FIXIT_ALLOW_MULTIPLE_EMAILS` is true.

---

## 🤖 AI Suggestions (Optional)

Enable AI-powered suggestions:

```env
FIXIT_AI_ENABLED=true
FIXIT_AI_API_KEY=sk-xxx    # or use FIXIT_AI_API_URL
FIXIT_AI_PROVIDER=openai   # or fixit-proxy, groq, etc.
FIXIT_AI_MODEL=gpt-3.5-turbo # or gpt-4 based on your provider
FIXIT_AI_API_URL=https://www.proxy-url.com # Used for fixit-proxy
```

---

## 🗃️ Database Table

Includes fields like:

- `id`
- `url`
- `request`
- `response`
- `ip`
- `exception`
- `file`
- `line`
- `trace`
- `fingerprint`
- `occurrences`
- `last_seen_at`
- `environment`
- `status` (`not_fixed`, `fixed`)
- `created_at`, `updated_at`

Table name is configurable.

---

## 📊 CLI Usage

```bash
php artisan fixit:report             # View logs
php artisan fixit:report --all
php artisan fixit:report --fix=3
php artisan fixit:verify-config --fix
php artisan fixit:sync-config --write
php artisan fixit:sync-migrations
php artisan fixit:purge-old
php artisan fixit:status
php artisan fixit:clear --only=fixed
php artisan fixit:clear --before=1684780800
```

---

## 🔌 Extend Alert Channels

Implement:

```php
Fixit\Contracts\FixitAlertInterface
```

Bind your own alert system (Discord, webhook, etc.) via service provider.

---

## 🛡️ Security & Best Practices

- Uses Laravel Crypt under the hood
- No user or session info tracked by default
- Designed for extensibility, dev-friendliness, and CI integration

---

## 📝 Changelog

See [Releases](https://github.com/coedevtech/coedevtech-fixit/releases) for detailed changes.