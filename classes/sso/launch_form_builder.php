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
 * This class is used to create an HTML form that's used to launch the SSO POST request
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/mumie/classes/cryptography/mumie_cryptography_service.php');
require_once($CFG->dirroot . '/auth/mumie/lib.php');

use auth_mumie\token\sso_token;
use auth_mumie\user\mumie_user;

/**
 * This class is used to create an HTML form that's used to launch the SSO POST request
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class launch_form_builder {
    /**
     * @var sso_token|\stdClass
     */
    private sso_token $ssotoken;
    /**
     * @var \stdClass
     */
    private \stdClass $mumietask;
    /**
     * @var mumie_user
     */
    private mumie_user $user;
    /**
     * @var string
     */
    private string $deadlinefragment;

    /**
     * Create a new instance
     * @param sso_token  $ssotoken
     * @param \stdClass  $mumie
     * @param mumie_user $user
     */
    public function __construct(sso_token $ssotoken, \stdClass $mumie, mumie_user $user) {
        $this->ssotoken = $ssotoken;
        $this->mumietask = $mumie;
        $this->user = $user;
        $this->deadlinefragment = '';
    }

    /**
     * Add a deadline parameter to the launch form.
     * @param int $deadline
     * @return $this
     */
    public function with_deadline(int $deadline): launch_form_builder {
        $this->deadlinefragment = $this->get_deadline_signature_inputs($deadline);
        return $this;
    }

    /**
     * Get the html string input for deadline parameter
     * @param int $deadline
     * @return string
     */
    private function get_deadline_signature_inputs(int $deadline): string {
        $deadlineinmilliseconds = auth_mumie_get_deadline_in_ms($deadline);
        $syncidlowercase = strtolower($this->user->get_sync_id());
        $signeddata = \mumie_cryptography_service::sign_data(
            $deadlineinmilliseconds,
            $syncidlowercase,
            $this->get_worksheet_id()
        );
        return "<input type='hidden' name='deadline' id='deadline' type='text' value='{$deadlineinmilliseconds}'>
        <input type='hidden' name='deadlineSignature' id='deadlineSignature' type='text' value='{$signeddata}'>";
    }

    /**
     * Get worksheet id from problem path
     * @return string
     */
    private function get_worksheet_id(): string {
        $problempath = auth_mumie_get_problem_path($this->mumietask);
        return str_replace(sso_service::WORKSHEET_PREFIX, "", $problempath);
    }

    /**
     * Get the launch form html code as string
     * @return string
     * @throws \dml_exception
     */
    public function build(): string {
        $loginurl = auth_mumie_get_login_url($this->mumietask);
        $org = get_config("auth_mumie", "mumie_org");
        $problemurl = auth_mumie_get_problem_url($this->mumietask);
        $problempath = auth_mumie_get_problem_path($this->mumietask);

        return"
            <form id='mumie_sso_form' name='mumie_sso_form' method='post' action='{$loginurl}'>
                <input type='hidden' name='userId' id='userId' type ='text' value='{$this->ssotoken->get_user()}'/>
                <input type='hidden' name='token' id='token' type ='text' value='{$this->ssotoken->get_token()}'/>
                <input type='hidden' name='org' id='org' type ='text' value='{$org}'/>
                <input type='hidden' name='resource' id='resource' type ='text' value='{$problemurl}'/>
                <input type='hidden' name='path' id='path' type ='text' value='{$problempath}'/>
                <input type='hidden' name='lang' id='lang' type ='text' value='{$this->mumietask->language}'/>
                {$this->deadlinefragment}
            </form>
            <script>
            document.forms['mumie_sso_form'].submit();
            </script>
        ";
    }
}
