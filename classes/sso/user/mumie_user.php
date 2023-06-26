<?php

namespace auth_mumie\user;

class mumie_user {
    private int $moodleid;
    private string $mumieid;
    private string $firstname;
    private string $lastname;
    private string $email;

    /**
     * @param int    $moodleid
     * @param string $mumieid
     */
    public function __construct(int $moodleid, string $mumieid)
    {
        $this->moodleid = $moodleid;
        $this->mumieid = $mumieid;
    }

    public function load() {
        global $DB;
        $user = $DB->get_record('user', array('id' => $this->moodleid));
        if (!$user) {
            return;
        }
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->email = $user->email;
    }

    /**
     * @return int
     */
    public function get_moodle_id() : int
    {
        return $this->moodleid;
    }

    /**
     * @param int $moodleid
     */
    public function set_moodle_id(int $moodleid) : void
    {
        $this->moodleid = $moodleid;
    }

    /**
     * @return string
     */
    public function get_mumie_id() : string
    {
        return $this->mumieid;
    }

    /**
     * @param string $mumieid
     */
    public function set_mumie_id(string $mumieid) : void
    {
        $this->mumieid = $mumieid;
    }

    /**
     * @return string
     */
    public function get_firstname() : string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function get_lastname() : string
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function get_email() : string
    {
        return $this->email;
    }
}