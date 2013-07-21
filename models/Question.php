<?php

class Question extends Model {
    public $id;
    public $is_multiple = false;
    public $is_required = false;
    public $text = '';

    public $table = 'question';
    protected $attributes = array('is_multiple', 'is_required', 'text');

    private $_maxCount = 0;

    /**
     * @param int $maxCount
     */
    public function setMaxCount($maxCount)
    {
        $this->_maxCount = $maxCount;
    }

    /**
     * @return int
     */
    public function getMaxCount()
    {
        return $this->_maxCount;
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Poll the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        if ($this->is_required == 'on') {
            $this->is_required = true;
        }
        if ($this->is_multiple == 'on') {
            $this->is_multiple = true;
        }
        return parent::beforeSave();
    }

    public function check()
    {
        if (empty($this->text)) {
            $this->errors[] = 'Введите текст вопроса!';
            return false;
        }
        return true;
    }


}
