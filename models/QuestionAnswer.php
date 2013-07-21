<?php
/**
 * QuestionAnswer class file.
 * 
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 */
 

class QuestionAnswer extends Model {
    /**
     * @var integer
     */
    public $answer_id;

    /**
     * @var integer
     */
    public $question_id;

    public $table = 'question_answer';
    public $primaryKey = null;

    protected $attributes = array('answer_id', 'question_id');

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
