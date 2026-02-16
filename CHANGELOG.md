# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v2.8.1] - 2026-02-16

### Fixed
- **YATBaseTable**: Removed `mixed` type from `$model` property to restore child class compatibility. Typed parent properties cannot be overridden without matching the type exactly in PHP 8.

## [v2.8.0] - 2026-02-14

### Added
- **MCP Tools**: Three MCP tools (ComponentDocs, ListComponents, ProjectContext) for Laravel Boost integration.
- **Skills**: Setup skill (bt-tables-setup) and docs maintenance skill for AI assistants.
- **Docs**: LLM reference docs and user docs for all 12 components (docs/llms/, docs/components/).
- **Docs**: AI assistant documentation with universal guide, Cursor rules, and code examples (docs/ai-assistants/).
- **Docs**: Doc templates for LLM and user documentation (docs/llms/_template.md, docs/components/_template.md).
- **Tests**: Comprehensive test suite expanded from 39 to 243 tests covering all column types, filter types, traits, bulk actions, inline editing, filters integration, row manipulation, and export.
- **Tests**: MCP integrity tests ensuring documentation stays in sync with code.
- **Translations**: 8 missing Spanish translation keys added.

### Changed
- **Code Quality**: Type hints added to 60 properties across all traits and classes.
- **Code Quality**: PHPDoc blocks added or corrected across 68 issues in all src/ files.
- **Code Quality**: Magic number replaced with PHP_INT_MAX constant.
- **Security**: Input validation and null safety improvements across multiple traits.
- **Cleanup**: Dead code, duplicate PHPDoc blocks, and Spanish comments removed.

### Fixed
- **Migration**: Corrected `down()` method to properly reverse migration.
- **Pagination**: Fixed typo in pagination property name.
- **Null Safety**: Added null checks to prevent errors with empty data sets.

## [2.7.1] - 2026-01-18

### Fixed
- Fixed filters with custom `queryCallback` failing when no matching database column exists.
- Virtual filters (those using `->query()` without a real column) now work correctly by generating a virtual key instead of requiring a column match.

## [2.7.0] - 2026-01-16

### Added
- Added `triggerCardInfoModal(bool)` method to `Column` class to optionally disable opening the info modal when clicking the card title on mobile.
- Support for closure callbacks in `cardTitle()` method to allow dynamic content generation for mobile card titles.

### Changed
- Refactored `Data` trait and `Column` serialization logic to support passing closures to views, enabling more dynamic UI definitions.
## [2.6.3] - 2026-01-16

### Added
- Added `centered()` method to `Column` class for easy content and header alignment.

### Fixed
- Fixed bulk column width issue where it expanded unnecessarily in tables with few columns.

## [2.6.2] - 2026-01-16

### Fixed
- Included styling fixes missed in v2.6.1 (row opacity adjustments).

## [2.6.1] - 2026-01-16

### Changed
- Improved table row visual hierarchy:
    - Reduced hover background opacity for clearer content visibility.
    - Subtle adjustment to even row background opacity for better contrast.

## [2.6.0] - 2026-01-07

### Added
- New `DateColumn` class for displaying formatted date values:
    - `inputFormat(string)` - specify expected input date format.
    - `outputFormat(string)` - define display format (default: `Y-m-d`).
    - `emptyValue(string)` - fallback value for null/empty dates.
- Date formatting logic in `Data::transformRow()` for automatic date parsing and formatting.
- Unit tests for `DateColumn` class.

## [2.5.0] - 2026-01-02

### Added
- Comprehensive test suite using PestPHP:
    - **Unit Tests**: `Column` logic (instantiation, unique keys, modifiers) and `Options` logic.
    - **Feature Tests**: Table rendering, pagination (per page, navigation), global search, and column sorting.
- Test infrastructure: `TestCase` setup with SQLite in-memory database and `Pest` configuration.
- Structural improvements in `yat-table.blade.php`:
    - Better handling of row expansion and mobile layout.
    - Improved empty search results display.
    - Enhanced data-attribute handling for accessibility and styling.

### Changed
- Improved project documentation structure and meta-information.
- Refactored `yat-table.blade.php` for better readability and performance.
- Updated `YATableComponent.stub` with improved code styling and type hints.

## [2.4.4] - 2025-12-31

### Fixed
- Added docs

## [2.4.3] - 2025-12-30

### Fixed
- Fixed column alignment when using `pushLeft()` and `pushRight()` by adding flex utilities to header wrappers.

### Changed
- Improved code styling in `Column` class (parameter type hints and return types).

## [2.4.1] - 2025-12-19

### Fixed
- Fixed inline editing:
    - Fixed display showing ID instead of label after save.
    - Fixed initial value mismatch (display vs ID) for relationship fields.
    - Fixed empty state defaulting to first option (added placeholder).
    - Fixed `Undefined array key` error when `updateField` is not in columns.
    - Fixed data persistence issues on pagination (added `wire:key` and cache invalidation).
- Added `setUpdateField()` method to `Column` to support updating relationship IDs while displaying names.
- Added translations for "Select an option" placeholder.

## [2.4.2] - 2025-12-24

### Changed
- Changed columns to be Sortable and Searchable by default.
- Added `sortable(bool)` and `searchable(bool)` modifiers to `Column` class to explicitly enable/disable these features.

## [2.4.0] - 2025-12-19

### Changed
- Enhanced Inline Editing UI:
    - Added success/error visual feedback with icons (Check/Exclamation).
    - Added loading state spinner during save.
    - Improved hover effects with specific card styling (inner div with `hover:bg-amber-200` type effect).
    - Made `updateField` return a boolean status for UI feedback.
- Updated documentation with comprehensive examples for inline editing.

## [2.3.11] - 2025-12-18

### Changed
- Updated default `inputThemeOverride` to `beartropy` in `View` trait.

## [2.3.10] - 2025-12-18

### Changed
- Updated defaults in `View` trait to fallback to `gray` theme.
- Updated pagination components to respect `buttonThemeOverride`.

### Added
- Added `getThemeConfig` helper method to `View` trait.

## [2.3.9] - 2025-12-18

### Changed
- Removed `.agent` folder from repository and added it to `.gitignore`.

## [2.3.8] - 2025-12-18

### Added
- Added `setButtonThemeOverride(?string $theme)` and `setInputThemeOverride(?string $theme)` to `View` trait to allow overriding themes for specific components.

## [2.3.7] - 2025-12-18

### Added
- Added `useGlobalSearch(bool)` method to `Search` trait to toggle global search input visibility.
- Added `showOnlyTable(bool)` method to `YATBaseTable` to quickly disable global search, pagination, and column toggle.
- Added `pushLeft()` and `pushRight()` methods to `Column` class for easy content and header alignment using Tailwind CSS.

### Fixed
- Fixed table header alignment (`text-left`, `pl-2`) for the row counter column in `yat-table.blade.php`.
