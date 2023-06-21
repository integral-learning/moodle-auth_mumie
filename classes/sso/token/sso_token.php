<?php

namespace auth_mumie\token;

class sso_token {
    const SSO_TOKEN_TABLE = "auth_mumie_sso_tokens";

    private int $id;
    private string $token;
    private string $user;
    private int $timecreated;

    /**
     * @param string $token
     * @param string $user
     * @param int    $timecreated
     */
    public function __construct(string $token, string $user, int $timecreated)
    {
        $this->token = $token;
        $this->user = $user;
        $this->timecreated = $timecreated;
    }

    public function create() : void {
        global $DB;
        $DB->insert_record(self::SSO_TOKEN_TABLE, array("the_user" => $this->user, "token" => $this->token, "timecreated" => $this->timecreated));
    }

    public function update() : void {
        global $DB;
        $DB->update_record(self::SSO_TOKEN_TABLE, array("the_user" => $this->user, "token" => $this->token, "timecreated" => $this->timecreated, "id" => $this->id));
    }

    public static function find_by_user(string $user) : sso_token {
        global $DB;
        $record = $DB->get_record(self::SSO_TOKEN_TABLE, ["the_user", $user]);
        $token = new sso_token($record->token, $record->user, $record->timecreated);
        $token->setId($record->id);
        return $token;
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token) : void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getUser() : string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user) : void
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getTimecreated() : int
    {
        return $this->timecreated;
    }

    /**
     * @param int $timecreated
     */
    public function setTimecreated(int $timecreated) : void
    {
        $this->timecreated = $timecreated;
    }



}