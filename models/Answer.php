<?php
/**
 * Answer class file.
 *
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 */

class Answer extends Model {
    public $id;
    public $text = '';
    public $count = 0;

    public $table = 'answer';
    protected $attributes = array('id', 'text');

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Poll the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function check()
    {
        if (empty($this->text)) {
            $this->errors[] = 'Введите текст ответа!';
            return false;
        }
        return true;
    }
}
