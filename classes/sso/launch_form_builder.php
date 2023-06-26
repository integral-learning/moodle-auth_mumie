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

use auth_mumie\token\sso_token;

/**
 * This class is used to create an HTML form that's used to launch the SSO POST request
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class launch_form_builder {
    private sso_token $ssotoken;
    private \stdClass $mumietask;
    private string $deadlinefragment;

    /**
     * @param string    $userid
     * @param \stdClass    $ssotoken
     * @param string    $org
     * @param \stdClass $mumie
     */
    public function __construct(sso_token $ssotoken, \stdClass $mumie) {
        $this->ssotoken = $ssotoken;
        $this->mumietask = $mumie;
        $this->deadlinefragment = '';
    }

    public function with_deadline(int $deadline) : launch_form_builder {
        $this->deadlinefragment = $this->get_deadline_signature_inputs($deadline);
        return $this;
    }

    private function get_deadline_signature_inputs(int $deadline) : string {
        $problempath = auth_mumie_get_problem_path($this->mumietask);
        $deadlinedata = json_encode( [
            "deadline" => $deadline,
            "userId" => $this->ssotoken->get_user(),
            "problemPath" => $problempath
        ]);
        $signeddata = \mumie_cryptography_service::sign_data($deadlinedata);
        return "<input type='hidden' name='deadline' id='deadline' type='text' value='{$deadlinedata}'>
        <input type='hidden' name='deadlineSignature' id='deadlineSignature' type='text' value='{$signeddata}'>";
    }

    public function build() : string {
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
