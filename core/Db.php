<?php
/**
 * Class Db
 * Singleton class
 */
class Db extends mysqli
{
    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_NAME = 'polls';

    public $table = '';

    private static $instance = NULL;

    //вот он и синглтон правда хотелось бы делать на pdo
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Db();
            if (mysqli_connect_errno()) {
                throw new Exception("Database connection failed: " . mysqli_connect_error());
            }
        }
        return self::$instance;
    }

    /** @noinspection PhpHierarchyChecksInspection */
    protected function __construct()
    {
        parent::__construct(self::DB_HOST, self::DB_USER, self::DB_PASS, self::DB_NAME);
        $this->set_charset('utf8');
        if (mysqli_connect_errno()) {
            throw new Exception(mysqli_connect_error(), mysqli_connect_errno());
        }
    }

    private function __clone() {}

    public function mysql_insert_array($arr)
    {
        $dba = $this->compile_db_insert_string($arr);
        if (self::getInstance()->query("INSERT INTO {$this->table} ({$dba['FIELD_NAMES']}) VALUES({$dba['FIELD_VALUES']})")) {
            return true;
        } else {
            return false;
        }
    }

    public function mysql_update_array($arr, $where = '')
    {
        $dba   = $this->compile_db_update_string($arr);
        $query = "UPDATE $this->table SET $dba";
        if ($where) {
            $query .= ' WHERE ' . $where;
        }
        if (self::getInstance()->query($query)) {
            return true;
        } else {
            return false;
        }
    }

    private function compile_db_insert_string($data)
    {
        $field_names = $field_values = '';
        foreach ($data as $k => $v) {
            $v = self::getInstance()->real_escape_string($v);
            $field_names .= '`' . $k . '`,';
            if (is_numeric($v) and intval($v) == $v) {
                $field_values .= $v . ',';
            } else {
                $field_values .= "'$v',";
            }
        }
        $field_names  = preg_replace("/,$/", '', $field_names);
        $field_values = preg_replace("/,$/", '', $field_values);
        return array('FIELD_NAMES' => $field_names, 'FIELD_VALUES' => $field_values);
    }

    private function compile_db_update_string($data)
    {
        $return_string = '';
        foreach ($data as $k => $v) {
            $v = self::getInstance()->real_escape_string($v);
            if (is_numeric($v) and intval($v) == $v) {
                $return_string .= $k . '=' . $v . ',';
            } else {
                $return_string .= $k . "='" . $v . "',";
            }
        }
        $return_string = preg_replace("/,$/", '', $return_string);

        return $return_string;
    }
}
