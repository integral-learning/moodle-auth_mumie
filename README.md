# moodle-auth_mumie

## Welcome to MUMIE Single Sign On

This plugin allows Moodle users to access content on the e-learning platform 
[MUMIE](https://www.integral-learning.de/) without having to register a new account or to login.  
MUMIE Single Sign On is not meant to be used as a standalone but in combination with 
[MUMIE Task](https://github.com/integral-learning/moodle-mod_mumie). Together, these two plugins 
allow the integration of MUMIE content into your Moodle courses.

## What is MUMIE?
**Explorative Math Learning**

MUMIE is an e-learning platform for learning and teaching mathematics and computer science. It 
grew out of the needs of practical teaching at the interface between high school and university. 
MUMIE is highly flexible and can be integrated with other learning and content management 
systems. Its courses and high-quality course material are easily adjusted to any kind of  
pedagogical scenario. It has built-in learning and training environments with wiki-like 
dedicated social networks for virtual tutorials and self-organized learning enhancing cognitive 
and meta-cognitive skills. Powerful authoring tools support the production of new content. This  
opens the door to new, challenging and more efficient pedagogical scenarios.

MUMIE platform and content are the result of a cooperative effort by leading universities in 
Europe supported by the company integral-learning.

## Key features of MUMIE Single Sign On
* ### Open MUMIE Tasks without having to register or log into MUMIE servers 
  When a user starts a MUMIE Task for the first time, a new account is automatically registered. 
  The new username is composed of the Moodle user id and organization shorthand. No further  
  personal data are necessary
  
* ### Automatically log out from MUMIE servers when logging out of moodle
  Some students share computers with others. That's why we made sure that users are 
  automatically logged out from all MUMIE servers whenever they log out from moodle. 

## How to get access to MUMIE content
You need to be a MUMIE partner to use our content on Moodle. Please contact us over 
[email](mailto:contact@integral-learning.de) for more information.

## Installation

1. Download this plugin as a ZIP file
2. Go to *Site Administration*
3. Select *Plugins*->*Install plugins*
4. Drag and drop the ZIP file into the respecting field
5. Click *Install plugin from the ZIP file*
6. Enter your MUMIE API key and your organization shorthand and click *Save changes*. If you 
   don't know your key and your shorthand yet, you can add them later on the plugin settings page.
7. Go to *Site Administration*->*Plugins*->*Manage authentication* and enable MUMIE Single Sign 
   On by clicking on the eye icon next to it
8. Please contact us over [email](mailto:contact@integral-learning.de) and send us the URL of 
   your Moodle server and the IP address it uses for outgoing HTTP requests. We need those to  
   configure our own servers.

## Requirements

This activity plugin requires JavaScript to work!

## Wiki
For further information, detailed step-by-step guides and FAQs, please visit our 
[wiki](https://wiki.mumie.net/wiki/MUMIE-LMS-integration)!

