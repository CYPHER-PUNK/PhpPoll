<?php
/**
 * Poll class file.
 *
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 */

class Poll extends Model {
    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = null;
    const STATUS_CLOSED = 0;
    /**
     * @var int
     */
    public $id;

    public $status = null;

    /**
     * @var string varchar 255
     */
    public $text = '';

    public $table = 'poll';
    protected $attributes = array('status', 'text');

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
            $this->errors[] = 'Введите название вопроса!';
            return false;
        }
        return true;
    }
}
