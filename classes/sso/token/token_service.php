<?php

namespace auth_mumie\token;

use auth_mumie\user\mumie_user;
require_once($CFG->dirroot . '/auth/mumie/classes/sso/token/sso_token.php');

class token_service {
    public static function generate_sso_token(mumie_user $user) : sso_token {
        if ($token = sso_token::find_by_user($user->get_mumie_id())) {
            $token->setToken(self::generate_token());
            $token->setTimecreated(time());
            $token->update();
        } else {
            $token = new sso_token(self::generate_token(), $user->get_mumie_id(), time());
            $token->create();
        }
        return $token;
    }

    private static function generate_token() {
        return auth_mumie_get_token(20);
    }
}