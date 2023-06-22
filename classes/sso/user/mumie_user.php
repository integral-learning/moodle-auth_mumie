<?php

namespace auth_mumie\user;

class mumie_user {
    private int $moodleid;
    private string $mumieid;

    /**
     * @param int    $moodleid
     * @param string $mumieid
     */
    public function __construct(int $moodleid, string $mumieid)
    {
        $this->moodleid = $moodleid;
        $this->mumieid = $mumieid;
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
}