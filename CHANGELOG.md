# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.3.7] - 2025-12-18

### Added
- Added `useGlobalSearch(bool)` method to `Search` trait to toggle global search input visibility.
- Added `showOnlyTable(bool)` method to `YATBaseTable` to quickly disable global search, pagination, and column toggle.
- Added `pushLeft()` and `pushRight()` methods to `Column` class for easy content and header alignment using Tailwind CSS.

### Fixed
- Fixed table header alignment (`text-left`, `pl-2`) for the row counter column in `yat-table.blade.php`.
