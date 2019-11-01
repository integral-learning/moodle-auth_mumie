<?php

class mumie_admin_setting_configselect_id_hashing extends admin_setting_configselect {
    const ID_HASHING_ENABLED = 1;
    const ID_HASHING_DISABLED = 0;
    const ID_HASHING_UNSET = -1;
    const SETTING_NAME = "auth_mumie/id_hashing_enabled";

    public function __construct($visiblename, $description, $defaultsetting = -1) {
        $choices = array(self::ID_HASHING_ENABLED => get_string("mumie_enabled", 'auth_mumie'),
            self::ID_HASHING_DISABLED => get_string('mumie_disabled', 'auth_mumie'),
            self::ID_HASHING_UNSET => get_string("mumie_choose_later", 'auth_mumie'),
        );
        parent::__construct(self::SETTING_NAME, $visiblename, $description, $defaultsetting = -1, $choices);
    }

    public function write_setting($data) {

        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return parent::write_setting($data);
    }

    public function validate($data) {
        $current_setting = $this->get_setting();
        if ($data == $current_setting || $current_setting == "") {
            return true;
        } else if ($current_setting == self::ID_HASHING_UNSET) {
            return true;
        }
        return get_string("mumie_id_hashing_setting_locked", 'auth_mumie');
    }

    public static function get_value() {
        return get_config('auth_mumie', self::SETTING_NAME);
    }
}

function debug_to_console($data) {
    if (is_array($data) || is_object($data)) {
        echo ("<script>console.log('PHP: " . json_encode($data) . "');</script>");
    } else {
        echo ("<script>console.log('PHP: " . $data . "');</script>");
    }
}
