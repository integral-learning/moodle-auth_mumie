<?php

namespace auth_mumie\user;

use auth_mumie\hash\hashing_service;
use auth_mumie\hash\mumie_id_hash;

require_once($CFG->dirroot . '/auth/mumie/classes/sso/hash/hashing_service.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/user/mumie_user.php');

class mumie_user_service {
    public static function get_user($moodleid, $mumietask) : mumie_user {
        if (self::use_id_masking($mumietask)) {
            $mumieid = hashing_service::generate_hash($moodleid, $mumietask)->getHash();
        } else {
            $mumieid = $moodleid;
        }
        return new mumie_user($moodleid, $mumieid);
    }

    public static function get_from_mumie_user(string $mumieid) : mumie_user {
        if (self::is_mumie_id_masked($mumieid)) {
            $moodleid = mumie_id_hash::find_by_hash($mumieid)->getUser();
        } else {
            $moodleid = (int) $mumieid;
        }
        return new mumie_user($moodleid, $mumieid);
    }

    private static function use_id_masking(mixed $mumietask) : bool {
        return isset($mumietask->use_hashed_id) && $mumietask->use_hashed_id == 1;
    }

    private static function is_mumie_id_masked(string $mumieid) : bool {
        return strlen($mumieid) >= 128;
    }
}