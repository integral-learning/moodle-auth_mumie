# MUMIE Single Sign On - Changelog

All important changes to this plugin will be documented in this file
## [v1.3.1] - 2020-03-03
### Added
- MUMIE Problems can now be added to the plugin's server-course-task structure. 
This means that the use of tasks that are not part of the offical server structre is now supported as well.

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
