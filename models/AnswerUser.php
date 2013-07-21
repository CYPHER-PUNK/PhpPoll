<?php
/**
 * AnswerUser class file.
 * 
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 */
 

class AnswerUser extends Model {
    /**
     * @var integer
     */
    public $answer_id;

    /**
     * @var integer
     */
    public $user_id;

    public $table = 'answer_user';
    public $primaryKey = null;

    protected $attributes = array('answer_id', 'user_id');

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
