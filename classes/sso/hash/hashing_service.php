<?php

namespace auth_mumie\hash;
require_once($CFG->dirroot . '/auth/mumie/classes/sso/hash/mumie_id_hash.php');
require_once($CFG->dirroot . '/auth/mumie/lib.php');

class hashing_service {
    public static function generate_hash($user, $mumietask) : mumie_id_hash {
        $mumieidhash = new mumie_id_hash($user, self::get_hash_with_suffix($user, $mumietask));
        $mumieidhash->save();
        return $mumieidhash;
    }

    private static function get_hash_with_suffix($user, $mumietask) : string {
        $hash = auth_mumie_get_hashed_id($user);
        if ($mumietask->privategradepool) {
            $hash .= '@gradepool' . $mumietask->course . '@';
        }
        return $hash;
    }

}