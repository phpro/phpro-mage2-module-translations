# Changelog
## [1.0.0] - 2022-01-28
### Added
- Simple backend CRUD to manage translations
- Single and multi-inline editing via the grid-overview
- Import and export via CSV files via CLI
- Import and export via default Magento import/export functionality
- Prepare new translations via data patch scripts
- (Re)generate frontend translations (JSON translation files) via CLI and backend

## [1.1.0] - 2022-05-18
### Added
- PHP 8.0 compatibility

## [1.2.0] - 2022-05-19
### Added
- PHP 8.1 compatibility
### Removed
- PHP 8.0 compatibility

## [1.2.1] - 2022-06-29
### Bugfix
- Fix return type

## [1.2.2] - 2022-10-14
### Added
- Hyva support: remove requirejs from head.additional

## [1.3.0] - 2024-03-28
### Updated
- PHP ^8.1 compatibility 

## [1.4.0] - 2024-06-05
### Updated
- Set minimum PHP version to 8.3
### Bugfix
- Fix return types console commands
- Locales are not found if set in env.php and not in core_config_table

## [1.4.1] - 2024-07-02
### Bugfix
- Fix faulty doc blocs preventing di:compile

## [1.4.2] - 2024-07-05
### Updated
- Allow PHP 8.2
