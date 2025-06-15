## [1.3.3] - 2025-06-14

### Added
- `--write` option to `fixit:sync-config` command
  - Automatically appends any missing root-level config keys to `config/fixit.php`
  - Preserves formatting and `env()` usage
  - Uses modern `[]` syntax for arrays

## [v1.4.3] - 2025-06-15

### Added
- Support for AI providers: `together`, `groq`
- Config: added `FIXIT_AI_MODEL` for provider-specific model control
- Config: refactored to allow dynamic model switching per provider

### Changed
- Command registration now works on Laravel 10 (removed use of protected kernel method)
- Service provider logic refactored into `Fixit\Bootstrap\*` classes for clarity

### Fixed
- Artisan error when using `commands()` outside allowed scope on Laravel 10

