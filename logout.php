<?php
/**
 * This plugins enables user to login to OMB+ via Single Sign On.
 *
 * @package    auth
 * @subpackage sso2ombplus
 * @copyright  2017 integral-learning GmbH (http://www.integral-learning.de)
 * @author     Petrus Tan (petrus.tan@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (dirname(dirname(dirname(__FILE__))) . '/config.php');
global $CFG;

// Here we are currently ignoring the sesskey check. The current session is just logged out.
require_logout();
redirect($CFG->wwwroot);
