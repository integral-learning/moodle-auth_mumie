<?php

namespace auth_mumie;

use auth_mumie\user\mumie_user_service;
use auth_mumie\token\token_service;
use auth_mumie\token\sso_token;

require_once($CFG->dirroot . '/auth/mumie/classes/sso/user/mumie_user_service.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/token/token_service.php');
require_once($CFG->dirroot . '/mod/mumie/lib.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/launch_form_builder.php');



class sso_service {
    const WORKSHEET_PREFIX = "worksheet_";
    public static function sso($moodleid, $mumietask) : void {
        $mumieuser = mumie_user_service::get_user($moodleid, $mumietask);
        $ssotoken = token_service::generate_sso_token($mumieuser);
        $deadline = mumie_get_effective_duedate($moodleid, $mumietask);
        echo self::get_launch_form($ssotoken, $mumietask, $deadline);
    }

    private static function get_launch_form(sso_token $token, $mumietask, $deadline) : string {
        $launchformbuilder = new launch_form_builder($token, $mumietask);

        $problempath = auth_mumie_get_problem_path($mumietask);
        if (self::include_signed_deadline($problempath, $deadline)) {
            $launchformbuilder->with_deadline($deadline);
        }
        return $launchformbuilder->build();
    }

    private static function include_signed_deadline(string $problempath, $deadline) : bool {
        return str_starts_with($problempath, self::WORKSHEET_PREFIX)
            && $deadline > 0;
    }
}