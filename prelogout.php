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
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$logouturls = $_GET["logoutUrl"];
$redirect = json_encode($_GET["redirect"]);

?>

<script>
    var logouturls = Object.values(<?php echo $logouturls ?>);
    var promises = [];

    logouturls.forEach(function(url) {
        promises.push(logoutFromServer(url));
    });

    Promise.all(promises)
    .then(function (values) {
        window.location.href =<?php echo $redirect ?>;
    });

    function logoutFromServer(url) {
        var promise = new Promise(function (resolve, reject) {
            var request = new XMLHttpRequest();
            request.open("GET", url);
            console.log("DONT WAIT FOR RESOIOSE")
            request.send();
            request.onreadystatechange = function () {
                console.log(request.response);
                resolve();
            }
        })
        return promise;
    }

</script>


