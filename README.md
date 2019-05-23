# moodle-auth_mumie

## Welcome to MUMIE Single Sign On

This plugin allows moodle users to access content on the e-learning platform [MUMIE](https://www.mumie.net/) without having to register a new a account or to login. MUMIE Single Sign On is not meant to be used as a standalone, but in combination with [MUMIE Task](https://github.com/integral-learning/moodle-mod_mumie). Together, these two plugins allow the integration of MUMIE content into your moodle courses.

## Key features
* ### Open MUMIE Tasks without having to register or log into MUMIE servers 
  When a user starts a MUMIE Task for the first time, a new account is automatically registered. The new username is composed of the moodle userid and a organisation shorthand. No further personal data are necessary
  
* ### Automatically logout from MUMIE servers when logging out of moodle
  Some students share computers with others. That's why we made sure, that users are automatically logged out from all MUMIE servers, whenever they log out from moodle. 

## How to get access to MUMIE content
You need to be a MUMIE partner to use our content on moodle. Please contact us over [email](mailto:contact@integral-learning.de) for more information.

## Installation

1. Download this plugin as a ZIP file
2. Go to *Site Administration*
3. Select *Plugins*->*Install plugins*
4. Drag and drop the ZIP file into the respecting field
5. Click *Install plugin from the ZIP file*
6. Enter your MUMIE API key and your organisation shorthand and click *Save changes*
7. Go to *Site Administration*->*Plugins*->*Manage authentication* and enable MUMIE Single Sign On by clicking on the eye icon next to it

For more information, please visit our [wiki](https://wiki.mumie.net/wiki/MUMIE-Moodle-integration)!
