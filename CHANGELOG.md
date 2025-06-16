## [1.3.3] - 2025-06-14

### Added
- `--write` option to `fixit:sync-config` command
  - Automatically appends any missing root-level config keys to `config/fixit.php`
  - Preserves formatting and `env()` usage
  - Uses modern `[]` syntax for arrays

## [1.6.0](https://github.com/coedevtech/coedevtech-fixit/compare/v1.5.1...v1.6.0) (2025-06-16)


### Features

* add LogExceptionToDb listener with encryption, deduplication, a… ([f353d87](https://github.com/coedevtech/coedevtech-fixit/commit/f353d87b06b01389aab36b7e41151d9ff92f5a2f))
* add LogExceptionToDb listener with encryption, deduplication, and alerting ([0f81716](https://github.com/coedevtech/coedevtech-fixit/commit/0f817160fbb6fe188581cd0272073e1fee70edd7))


### Miscellaneous Chores

* remove private repo reference ([3688b08](https://github.com/coedevtech/coedevtech-fixit/commit/3688b08e08924580bfa15cdd94ea0d6f75effd5b))
* remove private repo reference ([25bcf23](https://github.com/coedevtech/coedevtech-fixit/commit/25bcf23bfcf13074ff9e9957f39ed5b2aeab4731))
* remove private repo reference ([13367fa](https://github.com/coedevtech/coedevtech-fixit/commit/13367faf3f97af147119b8124296fcdc85e31172))
* remove private repo reference ([86a2dc8](https://github.com/coedevtech/coedevtech-fixit/commit/86a2dc83c0ceb5499ae74898a3c8cee3745183da))
* remove private repo reference ([a35617f](https://github.com/coedevtech/coedevtech-fixit/commit/a35617fc7c62dcf4602057b0a0ff265fe5f85498))

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
