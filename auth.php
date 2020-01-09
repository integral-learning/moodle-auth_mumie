<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Enables single sign on and single sign out with MUMIE servers
 *
 * @package auth_mumie
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once ($CFG->libdir . '/authlib.php');
require_once ($CFG->dirroot . '/auth/mumie/classes/mumie_server.php');
require_once ($CFG->dirroot . '/auth/mumie/lib.php');

/**
 * Enables single sign on and single sign out with MUMIE servers
 *
 * @package auth_mumie
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_mumie extends auth_plugin_base {

    /**
     * The name of the component. Used by the configuration.
     */
    const COMPONENT_NAME = 'auth_mumie';

    /**
     * Constructor
     */
    public function __construct() {
        $this::auth_plugin_mumie();
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_mumie() {
        $this->authtype = 'mumie';
        $this->config = get_config(self::COMPONENT_NAME);
    }

    /**
     * Send logout requests to all MUMIE servers
     */
    public function prelogout_hook() {
        global $DB, $USER;
        global $CFG;
        $tokentable = "auth_mumie_sso_tokens";

        if ($DB->get_record($tokentable, array('the_user' => $USER->id))
            || $DB->get_record($tokentable, array('the_user' => auth_mumie_get_hashed_id($USER->id)))) {
            $redirecturl = "{$CFG->wwwroot}/auth/mumie/prelogout.php?sesskey={$USER->sesskey}&logoutUrl="
            . json_encode(auth_mumie\mumie_server::get_all_logout_urls())
            . "&redirect=" . urlencode("{$CFG->wwwroot}/login/logout.php?sesskey={$USER->sesskey}");
            $DB->delete_records($tokentable, array('the_user' => $USER->id));
            $DB->delete_records_select(
                $tokentable,
                ' the_user LIKE :the_user',
                array('the_user' => auth_mumie_get_hashed_id($USER->id) . '%')
            );
            redirect($redirecturl);
        }
    }

    /**
     *  Empty implementation. This plugin does not handle user login to moodle using password.
     *
     * @param string $username The user
     * @param string $password The password
     */
    public function user_login($username, $password) {
    }
}