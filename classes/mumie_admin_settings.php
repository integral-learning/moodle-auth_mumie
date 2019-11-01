<?php

class mumie_admin_setting_configselect_encryption extends admin_setting_configselect {
    const ENCRYPTION_ENABLED = 1;
    const ENCRYPTION_DISABLED = 0;
    const ENCRYPTION_UNSET = -1;
    const SETTING_NAME = "auth_mumie/encryption_enabled";

    public function __construct($visiblename, $description, $defaultsetting = -1) {
        $choices = array(self::ENCRYPTION_ENABLED => "enabled", self::ENCRYPTION_DISABLED => 'disabled', self::ENCRYPTION_UNSET => "Choose later");
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
        debug_to_console("data is: " . $data . " get_setting is : " . $this->get_setting());
        $current_setting = $this->get_setting();
        if ($data == $current_setting || $current_setting == "") {
            return true;
        } else if ($current_setting == self::ENCRYPTION_UNSET) {
            return true;
        }
        return get_string("mumie_encryption_setting_locked", 'auth_mumie');
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
