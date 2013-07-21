<?php
/**
 * PollQuestion class file.
 * 
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 */

class PollQuestion extends Model {
    /**
     * @var integer
     */
    public $poll_id;

    /**
     * @var integer
     */
    public $question_id;

    public $table = 'poll_question';
    public $primaryKey = null;

    protected $attributes = array('poll_id', 'question_id');

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Poll the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
