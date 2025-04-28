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
 * Execute a script that sends logout requests to all MUMIE servers
 *
 * @package auth_mumie
 * @copyright  2017-2025 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
$logouturls = required_param("logoutUrl", PARAM_RAW);
$redirect = required_param("redirect", PARAM_URL);
?>

<script>

    var logouturls = Object.values(

    <?php
    /**
     * converts logout urls from php to js
     *
     * @package auth_mumie
     * @copyright  2017-2025 integral-learning GmbH (https://www.integral-learning.de/)
     * @author Tobias Goltz (tobias.goltz@integral-learning.de)
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
        echo $logouturls
    ?>
    );
    var promises = [];

    logouturls.forEach(function(url) {
        promises.push(logoutFromServer(url));
    });

    Promise.all(promises)
    .then(function (values) {
        window.location.href = `
        <?php
         /**
          * go to $redirect
          *
          * @package auth_mumie
          * @copyright  2017-2025 integral-learning GmbH (https://www.integral-learning.de/)
          * @author Tobias Goltz (tobias.goltz@integral-learning.de)
          * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
          */
            echo $redirect
        ?>`;
    });

    function logoutFromServer(url) {
        var promise = new Promise(function (resolve, reject) {
            var request = new XMLHttpRequest();

            request.open("GET", url);
            request.withCredentials = true;
            request.timeout = 10000;
            request.send();
            request.onreadystatechange = function () {
                resolve();
            }
            request.ontimeout = function (e) {
                resolve();
            }
        })
        return promise;
    }

</script>

