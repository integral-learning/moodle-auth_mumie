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
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/auth/mumie/classes/encryption/mumie_encryption_key.php');

class mumie_cryptography_service {
    const PUBLIC_KEY_NAME = "public";
    const PRIVATE_KEY_NAME = "private";
    public static function get_public_key() : mumie_cryptographic_key | null {
        return mumie_cryptographic_key::get_by_name(self::PUBLIC_KEY_NAME);
    }

    public static function get_private_key() : mumie_cryptographic_key | null {
        return mumie_cryptographic_key::get_by_name(self::PRIVATE_KEY_NAME);
    }

    public static function ensure_key_pair_exist() {
        $publickey = self::get_public_key();
        $privatekey = self::get_private_key();

        if (is_null($publickey) || is_null($privatekey)) {
            self::generate_key_pair();
        }
    }

    private static function generate_key_pair() : void {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
        $res = openssl_pkey_new($config);

        openssl_pkey_export($res, $privatekey);
        $pubkey = openssl_pkey_get_details($res);
        $pubkey = $pubkey["key"];

        self::update_key_pair($privatekey, $pubkey);
    }

    private static function update_key_pair(string $privatekey, string $publickey) : void {
        self::update_private_key($privatekey);
        self::update_public_key($publickey);
    }

    private static function update_public_key(string $key) : void {
        self::update_key(self::PUBLIC_KEY_NAME, $key);
    }

    private static function update_private_key(string $key) : void {
        self::update_key(self::PRIVATE_KEY_NAME, $key);
    }

    private static function update_key(string $name, string $key) {
        $cryptographickey = mumie_cryptographic_key::get_by_name($name);
        if (!is_null($cryptographickey)) {
            $cryptographickey->set_key($key);
            $cryptographickey->update();
        } else {
            $cryptographickey = new mumie_cryptographic_key($name, $key);
            $cryptographickey->create();
        }
    }
}
