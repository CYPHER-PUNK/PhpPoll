<?php
/**
 * Model.php class file.
 *
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 * @link http://yiifad.ru/
 * @copyright 2012-2013 Ruslan Fadeev
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
require_once('Db.php');
class Model extends Db
{
    /**
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * @var array
     */
    public $errors = array();

    /**
     * @var bool
     */
    private $isNewRecord = null;

    private static $_models=array();// class name => model

    /**
     * @var string
     */
    protected $attributes = array();

    public function __construct($isNewRecord = true)
    {
        $this->isNewRecord = $isNewRecord;
        $this->id = 0;
    }

    /**
     * Returns the static model of the specified Model class.
     * The model returned is a static instance of the Model class.
     * It is provided for invoking class-level methods (something similar to static class methods.)
     *
     * EVERY derived Model class must override this method as follows,
     * <pre>
     * public static function model($className=__CLASS__)
     * {
     *     return parent::model($className);
     * }
     * </pre>
     *
     * @param string $className active record class name.
     * @return Model active record model instance.
     */
    public static function model($className = __CLASS__)
    {
        if (isset(self::$_models[$className])) {
            return self::$_models[$className];
        } else {
            return self::$_models[$className] = new $className();
        }
    }

    /**
     * @param array $values attributeName=>value
     */
    public function setAttributes($values/*, $safeOnly = true*/)
    {
        if (!is_array($values)) {
            return;
        }
        //$attributes = array_flip($safeOnly ? $this->getSafeAttributeNames() : $this->attributeNames());
        foreach ($values as $name => $value) {
            if (in_array($name, $this->attributes) || $name == $this->primaryKey) {
                $this->$name = $value;
            }
        }
    }

    public function beforeSave()
    {
        return true;
    }

    public function check()
    {
        return true;
    }

    public function save($check=true)
    {
        $this->beforeSave();
        if ($check) {
            if (!$this->check()) {
                return false;
            }
        }
        $values = array();
        foreach ($this->attributes as $name) {
            if ($this->{$name} != null) {
                $values[$name] = $this->{$name};
            }
        }
        if ($this->isNewRecord) {
            if ($this->mysql_insert_array($values)) {
                //@todo composite primaryKey
                if (is_string($this->primaryKey) && property_exists($this, $this->primaryKey)) {
                    $this->id = self::getInstance()->insert_id;
                }
                return true;
            }
        } else {
            if ($this->mysql_update_array($values, 'id=' . (int)$this->id)) {
                return true;
            }
        }
        $this->errors[] = self::getInstance()->errno . ' ' . self::getInstance()->error;
        return false;
    }

    public function errorsText()
    {
        return implode("\r\n<br />", $this->errors);
    }

    public function getIsNewRecord()
    {
        return (bool)$this->isNewRecord;
    }

    public function setIsNewRecord($value = null)
    {
        $this->isNewRecord = $value;
    }

    /**
     * @param int $id
     * @return bool|Model
     */
    public function findByPk($id = null)
    {
        if (get_class($this) == __CLASS__) {
            return false;
        }
        $result = self::getInstance()->query('SELECT * FROM ' . $this->table . ' WHERE id="' . $id . '"');
        if ($result->num_rows) {
            $this->setAttributes((array)$result->fetch_object());
            $this->setIsNewRecord(false);
            return $this;
        }
        return false;
    }
}