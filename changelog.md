# MUMIE Single Sign On - Changelog

All important changes to this plugin will be documented in this file

## [v1.3.5] - TODO
### Added
- Implemented Privacy-API

### Fixed
- Editing a MUMIE Task whose MUMIE server was deleted no longer causes an error

## [v1.3.4] - 2020-12-10
### Added
- Problem selector URL can be changed in admin settings

## [v1.3.3] - 2020-06-17
### Fixed
- Fixed minified js files

### Added
- Admin can now decide whether grades should be shared between courses

## [v1.3.2] - 2020-06-09
### Added
- MUMIE course names are now available in multiple languages
- MUMIE Tasks can now link an entire course at once. Grades will not be synchronized for these kind of activities.
- Teachers can now create MUMIE Tasks for LEMON servers.

### Fixed
- MUMIE server's names are now trimmed before saving. Trailing whitespace caused mod_form's javascript to crash.

### Changed
- Now using different constants for gradesync format and course format versions

## [v1.3.1] - 2020-03-03
### Added
- MUMIE Problems can now be added to the plugin's server-course-task structure. 
This means that the use of tasks that are not part of the official server structre is now supported as well.

## [v1.3.0] - 2020-02-04
### Fixed
- Capabilities are now given a proper name.

### Changed
- Added object structure for MUMIE Servers, Courses, Tasks and tags to 
improve code quality and maintain consistency between MUMIE plugins for different LMS.
- A user can now have multiple MUMIE accounts. This is especially useful for grade pools

## [v1.2] - 2019-11-05
### Added
- Single Sign On and Single Sign Out now also work with hashed MOODLE user ids.


## [v1.1] - 2019-16-06
### Added

- Moodle admins now can decide, whether a user's e-mail should be shared with MUMIE servers. 
Disabling this option does not impact mail addresses that have already been shared with MUMIE.
