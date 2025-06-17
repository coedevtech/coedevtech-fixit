## [1.3.3] - 2025-06-14

### Added
- `--write` option to `fixit:sync-config` command
  - Automatically appends any missing root-level config keys to `config/fixit.php`
  - Preserves formatting and `env()` usage
  - Uses modern `[]` syntax for arrays

## [1.8.4](https://github.com/coedevtech/coedevtech-fixit/compare/v1.8.3...v1.8.4) (2025-06-17)


### Bug Fixes

* updated exception ([eabad0b](https://github.com/coedevtech/coedevtech-fixit/commit/eabad0b19453f050385072c5204efb49dbd6e0d6))
* updated exception ([8fabeb8](https://github.com/coedevtech/coedevtech-fixit/commit/8fabeb881a0189556ff6d6dceba0e1c72b5018fe))
* updated exception ([26e03e6](https://github.com/coedevtech/coedevtech-fixit/commit/26e03e6a2a3e369bf0aea647f99ffa42a7202aed))

## [1.8.3](https://github.com/coedevtech/coedevtech-fixit/compare/v1.8.2...v1.8.3) (2025-06-17)


### Bug Fixes

* updated mail template ([49ad3e5](https://github.com/coedevtech/coedevtech-fixit/commit/49ad3e54ae36cd86897e48468a03243355538979))
* updated mail template ([2941806](https://github.com/coedevtech/coedevtech-fixit/commit/294180660af5e45076ec173b943f7375293fefd0))
* updated mail template ([9e0956e](https://github.com/coedevtech/coedevtech-fixit/commit/9e0956ee0b3184be8dc81fac4321b1bf9b5c3980))

## [1.8.1](https://github.com/coedevtech/coedevtech-fixit/compare/v1.8.0...v1.8.1) (2025-06-17)


### Bug Fixes

* updated install file ([12b97a1](https://github.com/coedevtech/coedevtech-fixit/commit/12b97a1b9426edff7bcaaae8511444e070ea8c89))
* updated install file ([2e2b799](https://github.com/coedevtech/coedevtech-fixit/commit/2e2b799151f3ec09912ec14b1d8a2d20fb4bc71b))

## [1.8.0](https://github.com/coedevtech/coedevtech-fixit/compare/v1.7.0...v1.8.0) (2025-06-17)


### Features

* redesign error email template with Fixit branding, and markdown rendering ([6196ab4](https://github.com/coedevtech/coedevtech-fixit/commit/6196ab4d577535d3ac57009e4b593b28ac2b78b4))


### Bug Fixes

* fixed tests ([e40f87f](https://github.com/coedevtech/coedevtech-fixit/commit/e40f87f776ac48433bea1f67dd32abba0d06e40c))

## [1.7.0](https://github.com/coedevtech/coedevtech-fixit/compare/v1.6.0...v1.7.0) (2025-06-16)


### Features

* removed space ([dcff146](https://github.com/coedevtech/coedevtech-fixit/commit/dcff14609fd811119b9de5db2744bf619afd5d87))


### Bug Fixes

* removed space ([3d9c88a](https://github.com/coedevtech/coedevtech-fixit/commit/3d9c88a0a37ee68803d489ff8886f4461897d210))
* removed space ([e77af02](https://github.com/coedevtech/coedevtech-fixit/commit/e77af02338119f587454e306b02617753893cf0d))
* removed space ([b5a4ae7](https://github.com/coedevtech/coedevtech-fixit/commit/b5a4ae7a89b1221fa93c84661fcf51d43a69978c))

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
