## [1.3.3] - 2025-06-14

### Added
- `--write` option to `fixit:sync-config` command
  - Automatically appends any missing root-level config keys to `config/fixit.php`
  - Preserves formatting and `env()` usage
  - Uses modern `[]` syntax for arrays