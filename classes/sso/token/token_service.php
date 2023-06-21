<?php

namespace auth_mumie\token;

class token_service {
    public static function generate_sso_token($mumietask, $user) : sso_token {
        $externaluser = self::get_external_user($user, $mumietask);
        if ($token = sso_token::find_by_user($externaluser)) {
            $token->setToken(self::generate_token());
            $token->setTimecreated(time());
            $token->update();
        } else {
            $token = new sso_token(self::generate_token(), $externaluser, time());
            $token->create();
        }
        return $token;
    }

    private static function get_external_user($user, $mumietask) {
        if (self::use_hashed_user($mumietask)) {

        }
        return $user;
    }

    private static function use_hashed_user($mumietask) : bool {
        return isset($mumietask->use_hashed_id) && $mumietask->use_hashed_id == 1;
    }

    private static function generate_token() {
        return auth_mumie_get_token(20);
    }
}