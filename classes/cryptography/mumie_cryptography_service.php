<?php

require_once($CFG->dirroot . '/auth/mumie/classes/encryption/mumie_encryption_key.php');

class mumie_cryptography_service
{
    const PUBLIC_KEY_NAME = "public";
    const PRIVATE_KEY_NAME = "private";
    public static function get_public_key() : mumie_cryptographic_key | null {
        return mumie_cryptographic_key::getByName(self::PUBLIC_KEY_NAME);
    }

    public static function get_private_key() : mumie_cryptographic_key | null {
        return mumie_cryptographic_key::getByName(self::PRIVATE_KEY_NAME);
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
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];

        self::update_key_pair($privatekey, $pubKey);
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
        $cryptographickey = mumie_cryptographic_key::getByName($name);
        if (!is_null($cryptographickey)) {
            $cryptographickey->setKey($key);
            $cryptographickey->update();
        } else {
            $cryptographickey = new mumie_cryptographic_key($name, $key);
            $cryptographickey->create();
        }
    }
}