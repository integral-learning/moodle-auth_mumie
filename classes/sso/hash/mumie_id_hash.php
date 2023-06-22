<?php

namespace auth_mumie\hash;

class mumie_id_hash {
    const HASH_ID_TABLE = "auth_mumie_id_hashes";
    private int $id;
    private int $user;
    private string $hash;

    /**
     * @param int    $user
     * @param string $hash
     */
    public function __construct(int $user, string $hash)
    {
        $this->user = $user;
        $this->hash = $hash;
    }

    public function save() : void {
        if (!self::find($this->user, $this->hash)) {
            $this->create();
        }
    }

    public function create() : void {
        global $DB;
        $DB->insert_record(self::HASH_ID_TABLE, ["the_user" => $this->user, "hash" => $this->hash]);
    }

    public function update() : void {
        global $DB;
        $DB->update_record(self::HASH_ID_TABLE, ["id" => $this->id, "the_user" => $this->user, "hash" => $this->hash]);
    }

    public static function find_by_user($user) : ?mumie_id_hash {
        global $DB;
        $record = $DB->get_record(self::HASH_ID_TABLE, ["the_user" => $user]);
        return self::from_record($record);
    }

    private static function find($user, $hash) : ?mumie_id_hash {
        global $DB;
        $record = $DB->get_record(self::HASH_ID_TABLE, ["the_user" => $user, "hash" => $hash]);
        return self::from_record($record);
    }

    private static function find_by_hash($hash) : ?mumie_id_hash {
        global $DB;
        $record = $DB->get_record(self::HASH_ID_TABLE, ["hash" => $hash]);
        return self::from_record($record);
    }

    private static function from_record($record) : ?mumie_id_hash {
        if ($record == null) {
            return null;
        }
        $id_hash = new mumie_id_hash($record->the_user, $record->hash);
        $id_hash->setId($record->id);
        return $id_hash;
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
     * @return int
     */
    public function getUser() : int
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser(int $user) : void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getHash() : string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash) : void
    {
        $this->hash = $hash;
    }
}