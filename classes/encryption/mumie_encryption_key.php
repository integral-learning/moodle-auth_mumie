<?php

class mumie_encryption_key {
    const MUMIE_ENCRYPTION_KEY_TABLE = "auth_mumie_encryption_key";
    private string $id;
    private string $name;
    private string $key;

    /**
     * @param string $name
     * @param string $key
     */
    public function __construct(string $name, string $key) {
        $this->name = $name;
        $this->key = $key;
    }

    public function create() {
        global $DB;
        $DB->insert_record(self::MUMIE_ENCRYPTION_KEY_TABLE, ["name" => $this->name, "key" => $this->key]);
    }

    public function update() {
        global $DB;
        $DB->update_record(self::MUMIE_ENCRYPTION_KEY_TABLE, ["name" => $this->name, "key" => $this->key, "id" => $this->id]);
    }

    public static function getByName(string $name) : mumie_encryption_key | null{
        global $DB;
        $record = $DB->get_record(self::MUMIE_ENCRYPTION_KEY_TABLE, ["name" => $name]);
        return self::fromRecord($record);
    }

    private static function fromRecord($record) : mumie_encryption_key | null {
        if (!$record) {
            return null;
        }
        $encryption_key = new mumie_encryption_key($record->name, $record->key);
        $encryption_key->setId($record->id);
        return $encryption_key;
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key) : void
    {
        $this->key = $key;
    }

}