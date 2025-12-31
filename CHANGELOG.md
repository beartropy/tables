# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


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
