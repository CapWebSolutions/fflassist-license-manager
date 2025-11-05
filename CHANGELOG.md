# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.6] - 2025-11-05

### Changed

- Updated documentation block for import_ffl_data cli command in includes\class-import-ffl-license.php

## [1.1.5] - 2025-03-31

### Added

- Add placeholder files for license modify logic. 

### Changed

- Re-enable import via settings page for launch.
- Add `global $wpdb;` statement to refactored import code. 

## [1.1.4] - 2025-03-24

### Changed

- General restructuring of admin import functionality. 
- Documentation and formatting in CLI import functionalitry. 

### Removed

- admin. js - functionality no longer required. 

## [1.1.3] - 2025-03-05

### Changed

- Styling for search results display on settings page. 
- Adjustments for table formatted output of search results. 
- Adjust search js to insert results into proper div.
- Adjust search meta box settings to provide additional direction. 

## [1.1.2] - 2025-03-04

### Added
 - Placeholder constant for ffl license number. 
 - Search capability on FFL license management settings page, new Search tab.
 - Added new Modify tab for future work. This will have ability to edit FFL license info in db.
 
## [1.1.1] - 2025-02-27

### Removed

- Removed commented out code from v1.1.0. 

## [1.1.0] - 2025-02-19

### Removed

- Removed namespacing throughout plugin for simplicity. 

## [1.0.3] - 2025-02-04

### Changed

- Refactor include for get_plugin_data.

## [1.0.2] - 2025-01-28

### Removed

- Debug code.

## [1.0.1] - 2024-11-19

### Added

- Create plugin settings page for import management
- License validation for Gravity Forms form ID 1
- Reformatting license code from entered values to accept 'dashed' or 'not dashed'

### Remove

- Original generic settings page creation

## [1.0.0] - 2024-10-28

### Added

- Initial commit