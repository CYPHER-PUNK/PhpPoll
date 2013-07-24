<?php
/**
 * Controller.php class file.
 * 
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 * @link http://yiifad.ru/
 * @copyright 2012-2013 Ruslan Fadeev
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
 

class Controller {
    /**
     * @var string HTML content
     */
    public $content = '';

    public function __construct()
    {
        //ну не делать же все красиво для этого
        require_once('core/Model.php');
        require_once('models/Poll.php');
        require_once('models/Question.php');
        require_once('models/PollQuestion.php');
        require_once('models/Answer.php');
        require_once('models/QuestionAnswer.php');
        require_once('models/AnswerUser.php');
    }

    public function __destruct()
    {
        include('template/index.php');
    }

    public function actionIndex()
    {
        $polls = array();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id) {
            switch (@$_GET['do']) {
                case 'open': {
                    $_GET['status'] = $status = Poll::STATUS_ACTIVE;
                    break;
                }
                case 'close': {
                    $_GET['status'] = $status = Poll::STATUS_CLOSED;
                    break;
                }
            }
            if (isset($status)) {
                $noActive = true;
                if ($status == Poll::STATUS_ACTIVE) {
                    $noActive = Db::getInstance()->query('SELECT * ' . Poll::model()->table . ' WHERE status=' . $status )->num_rows == 0 ? true : false;
                }
                if ($noActive) {
                    Db::getInstance()->query('UPDATE ' . Poll::model()->table . ' SET status=' . $status . ' WHERE id=' . $id);
                } else {
                    $alertText = 'Активный опрос уже существует!';
                }
            }
        }
        $status = isset($_GET['status']) ?  'WHERE status = "' . intval($_GET['status']) . '"' : 'WHERE status IS null';
        $query = Db::getInstance()->query('SELECT * FROM ' . Poll::model()->table . ' '.$status);
        while ($poll = $query->fetch_object()) {
            $polls[] = $poll;
        }
        $this->render('polls', array('polls' => $polls, 'alertText' => isset($alertText) ? $alertText : 0));
    }

    public function actionCreate()
    {
        $poll = new Poll();
        $pollQuestion = new PollQuestion();
        $answerQuestion = new QuestionAnswer();
        /** @var Question[] $questions */
        $questions = array();

        /** @var Answer[] $answers */
        $answers = array();

        if (isset($_POST['Poll']) && is_array($_POST['Poll'])) {
            Db::getInstance()->autocommit(false);
            $poll->setAttributes($_POST['Poll']);

            $saved = $poll->save();
            $oneRequiredPassed = false;
            if (isset($_POST['Question']) && is_array($_POST['Question'])) {
                $i = 0;
                foreach ($_POST['Question'] as $i => $question) {
                    if (isset($question['is_required'])) {
                        $oneRequiredPassed = true;
                    }
                    $questions[$i] = new Question;
                    $questions[$i]->setAttributes($question);
                    if ($saved && $questions[$i]->save()) {
                        $pollQuestion->setAttributes(array('poll_id' => $poll->id, 'question_id' => $questions[$i]->id));
                        if (!$saved = $pollQuestion->save()) {
                            //print_r($questions[$i]);
                            //print_r($pollQuestion);
                            echo $pollQuestion->errorsText();
                        }
                    }
                    if (isset($_POST['Answer'][$i]) && is_array($_POST['Answer'][$i])) {
                        $j = 0;
                        foreach ($_POST['Answer'][$i] as $j => $answer) {
                            $answers[$questions[$i]->id][$j] = new Answer();
                            $answers[$questions[$i]->id][$j]->setAttributes($answer);
                            $answers[$questions[$i]->id][$j]->question_id = $questions[$i]->id;
                            if ($saved && ($saved = $answers[$questions[$i]->id][$j]->save())) {
                                $answerQuestion->setAttributes(array('answer_id' => $answers[$questions[$i]->id][$j]->id, 'question_id' => $questions[$i]->id));
                                if (!$saved = $answerQuestion->save()) {
                                    $answers[$questions[$i]->id][$j]->errors[] = $answerQuestion->errorsText();
                                }
                            }
                        }
                        if (count($_POST['Answer'][$i]) == 1) {
                            $answers[$questions[$i]->id][$j]->errors[] = 'Введите хотя бы два варианта ответа';
                            $saved = false;
                        }
                    }
                }
                if (!$oneRequiredPassed) {
                    $questions[$i]->errors[] = 'Хотя бы один вопрос должен быть обязательным';
                }
            }
            if ($saved && $oneRequiredPassed) {
                Db::getInstance()->commit();
                $this->redirect('?action=update&id=' . $poll->id);
            } else {
                Db::getInstance()->rollback();
            }
        } else {
            $questions[] = new Question;
            $answers[][] = new Answer();
        }
        $this->render('form', array('saved' => isset($saved) ? $saved : null, 'poll' => $poll, 'questions' => $questions, 'answers' => $answers));
    }

    public function actionUpdate()
    {
        $poll = Poll::model()->findByPk(intval($_GET['id']));
        /** @var Question[] $questions */
        $questions = array();
        /** @var array[int][Answer] $answers */
        $answers = array();
        $query = Db::getInstance()->query(
            'SELECT question.* FROM ' . Question::model()->table . ' JOIN ' . PollQuestion::model(
            )->table . ' pq ON (pq.question_id=question.id) WHERE pq.poll_id=' . intval($_GET['id'])
        );
        while ($result = $query->fetch_object()) {
            $questions[$result->id] = new Question(false);
            $questions[$result->id]->setAttributes((array)$result);

            //@todo можно все сделать через один запрос, но дабы не париться ибо со временем плохо
            $query2 = Db::getInstance()->query(
                'SELECT answer.* FROM ' . Answer::model(
                )->table . ' JOIN question_answer aq ON (aq.answer_id=answer.id) WHERE aq.question_id="' . $result->id . '"'
            );
            $i = 0;
            while ($result2 = $query2->fetch_object()) {
                $answers[$result->id][$i] = new Answer(false);
                $answers[$result->id][$i]->setAttributes((array)$result2);
                ++$i;
            }
        }

        if (isset($_POST['Poll']) && is_array($_POST['Poll'])) {
            Db::getInstance()->autocommit(false);
            $poll->setAttributes($_POST['Poll']);
            $saved = $poll->save();
            $oneRequiredPassed = false;

            if (isset($_POST['Question']) && is_array($_POST['Question'])) {
                $i = 0;
                foreach ($_POST['Question'] as $i => $question) {
                    if (isset($question['is_required'])) {
                        $oneRequiredPassed = true;
                    }
                    if (!isset($questions[$i])) {
                        $questions[$i] = new Question;
                        //$pollQuestion = new PollQuestion();
                    }
                    $questions[$i]->is_multiple = $questions[$i]->is_required = false;//@todo bug
                    $questions[$i]->setAttributes($question);
                    if ($saved && ($saved = $questions[$i]->save()) && $questions[$i]->getIsNewRecord()) {
                        $pollQuestion = new PollQuestion();
                        $pollQuestion->setAttributes(array('poll_id' => $poll->id, 'question_id' => $questions[$i]->id));
                        $saved = $pollQuestion->save();
                    }

                    if (isset($_POST['Answer'][$i]) && is_array($_POST['Answer'][$i])) {
                        $j = 0;
                        //Db::getInstance()->query('DELETE FROM ' . Answer::model()->table . ' WHERE question_id="' . $questions[$i]->id . '"');
                        foreach ($_POST['Answer'][$i] as $j => $answer) {
                            if (!isset($answers[$questions[$i]->id][$j])) {
                                $answers[$questions[$i]->id][$j] = new Answer();
                            }
                            $answers[$questions[$i]->id][$j]->setAttributes($answer);
                            $answers[$questions[$i]->id][$j]->question_id = $questions[$i]->id;
                            if ($saved && ($saved = $answers[$questions[$i]->id][$j]->save()) && $answers[$questions[$i]->id][$j]->getIsNewRecord()) {
                                $answerQuestion = new QuestionAnswer();
                                $answerQuestion->setAttributes(array('answer_id' => $answers[$questions[$i]->id][$j]->id, 'question_id' => $questions[$i]->id));
                                if (!$saved = $answerQuestion->save()) {
                                    $answers[$questions[$i]->id][$j]->errors[] = $answerQuestion->errorsText();
                                }
                            }
                        }
                        if (count($_POST['Answer'][$i]) == 1) {
                            $answers[$questions[$i]->id][$j]->errors[] = 'Введите хотя бы два варианта ответа';
                            $saved = false;
                        }
                    }
                }
                if (!$oneRequiredPassed) {
                    $questions[$i]->errors[] = 'Хотя бы один вопрос должен быть обязательным';
                    Db::getInstance()->rollback();
                } else if ($saved) {
                    Db::getInstance()->commit();
                    //$errors = Db::getInstance()->error;
                }
            }
        }

        $this->render('form', array('saved' => isset($saved) ? $saved : null, 'poll' => $poll, 'questions' => $questions, 'answers' => $answers));
    }

    public function actionDelete()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $questionIds = array();
        $answerIds = array();
        if ($id) {
            $db = Db::getInstance();
            $result = $db->query('SELECT question_id FROM ' . PollQuestion::model()->table . ' WHERE poll_id="' . $id . '"');
            while ($row = $result->fetch_object()) {
                $questionIds[] = $row->question_id;
            }
            $result2 = $db->query('SELECT answer_id FROM ' . QuestionAnswer::model()->table . ' WHERE question_id IN (' . implode(',', $questionIds) . ')');
            while ($row2 = $result2->fetch_object()) {
                $answerIds[] = $row2->answer_id;
            }
            if (count($questionIds)) {
                $db->query('DELETE FROM poll WHERE id="' . $id . '"');
                $db->query('DELETE FROM question WHERE id IN (' . implode(',', $questionIds) . ')');
                $db->query('DELETE FROM answer WHERE id IN (' . implode(',', $answerIds) . ')');
                //остальные удаляются через mysql ON DELETE
            }
        }
        $this->redirect('?action=index');
    }

    public function actionVote()
    {

        $poll   = new Poll(false);
        if ($_GET['sectional'] && isset($_GET['id'])) {
            $poll->id = intval($_GET['id']);
        } else {
            $result = Db::getInstance()->query('SELECT * FROM ' . $poll->table . ' WHERE status=' . Poll::STATUS_ACTIVE)->fetch_object();
            $poll->setAttributes((array)$result);
        }
        /** @var Question[] $questions */
        $questions = array();
        $answers = array();
        $query = <<<SQL
SELECT question.*
FROM question
JOIN poll_question ON poll_question.question_id = question.id AND poll_id={$poll->id}
SQL;
        $result = Db::getInstance()->query($query);
        Db::getInstance()->autocommit(false);
        $userId = 0;
        $saved = true;
        while ($question = $result->fetch_object()) {
            $questions[$question->id] = new Question(false);
            $questions[$question->id]->setAttributes((array)$question);

            if (isset($_POST['AnswerUser'][$question->id])) {
                if (!$userId) {
                    Db::getInstance()->query('INSERT INTO user (id) VALUES (null)');
                    $userId = Db::getInstance()->insert_id;
                }
                if ($userId) {
                    foreach ($_POST['AnswerUser'][$question->id] as $answerUser) {
                        /** @var array[int][AnswerUser] $_answerUser */
                        $_answerUser[$question->id][$answerUser['answer_id']] = new AnswerUser();
                        $_answerUser[$question->id][$answerUser['answer_id']]->setAttributes(array('answer_id' => $answerUser['answer_id'], 'user_id' => $userId));
                        if (!$_answerUser[$question->id][$answerUser['answer_id']]->save()) {
                            $saved = false;
                            echo $_answerUser[$question->id][$answerUser['answer_id']]->errorsText();
                        }
                        if (!$question->is_multiple && count($_answerUser[$question->id]) > 1) {
                            $questions[$question->id]->errors[] = 'Можно выбрать только один вариант ответа!';
                        }
                    }
                }
            } elseif (isset($_POST['AnswerUser']) && $questions[$question->id]->is_required) {
                $questions[$question->id]->errors[] = 'Вы должны ответить на обязательный вопрос';
                $saved = false;
            }

            $query2 = <<<SQL
SELECT answer.*
FROM answer
JOIN question_answer ON question_answer.answer_id=answer.id AND question_answer.question_id={$question->id}
SQL;
            $result2 = Db::getInstance()->query($query2);
            while ($answer = $result2->fetch_object()) {
                $answers[$question->id][$answer->id] = new Answer(false);
                $answers[$question->id][$answer->id]->setAttributes((array)$answer);
            }
        }
        if ($saved && isset($_answerUser)) {
            Db::getInstance()->commit();
            $this->redirect('?action=result&id=' . intval($_POST['Poll']['id']));
        } else {
            Db::getInstance()->rollback();
            if (isset($_POST['Poll'])) {
                $poll->errors[] = 'WTF???';
            }
        }
        $this->render(
            'vote',
            array(
                'answerUser' => isset($_answerUser) ? $_answerUser : 0,
                'questions'  => $questions,
                'answers'    => $answers,
                'poll'       => $poll
            )
        );
    }

    public function actionResult()
    {
        $poll   = new Poll(false);
        $poll->id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $result = Db::getInstance()->query('SELECT * FROM ' . $poll->table . ' WHERE id=' . $poll->id)->fetch_object();
        $poll->setAttributes((array)$result);

        //print_r($_POST['AnswerUser']);

        if (isset($_POST['AnswerUser']) && is_array($_POST['AnswerUser'])) {
            $sections = array();
            foreach ($_POST['AnswerUser'] as $questionId => $answerUsers) {
                $answerIds = array();
                foreach ($answerUsers as $answerUser) {
                    //$answerIds[$questionId] = $answerUser['answer_id'];
                    $answerIds[] = $answerUser['answer_id'];
                    $sections[$questionId][] = $answerUser['answer_id'];
                }
                $result = Db::getInstance()->query('SELECT user_id FROM ' . AnswerUser::model()->table . ' WHERE answer_id IN (' . implode(',', $answerIds) . ')');
                while ($row = $result->fetch_object()) {
                    $userIdsAll[$questionId][] = $row->user_id;
                }
            }

            if (isset($userIdsAll)) {//для резальтатов опроса в разрезе
                $userIds = reset($userIdsAll);
                foreach ($userIdsAll as $questionId => $_userIds) {
                    $userIds = array_intersect($userIds, $_userIds);
                }
            }

            /*if (count($answerIds)) {
                $result = Db::getInstance()->query('SELECT user_id FROM ' . AnswerUser::model()->table . ' WHERE answer_id IN (' . implode(',', $answerIds) . ')');
                while ($row = $result->fetch_object()) {
                    $userIds[] = $row->user_id;
                }
            }*/
        }

        /** @var Question[] $questions */
        $questions = array();
        $answers = array();
        $query = <<<SQL
SELECT question.*
FROM question
JOIN poll_question ON poll_question.question_id = question.id AND poll_id={$poll->id}
SQL;
        $result = Db::getInstance()->query($query);
        while ($question = $result->fetch_object()) {
            $questions[$question->id] = new Question(false);
            $questions[$question->id]->setAttributes((array)$question);
            //в назрезе
            /*if (isset($_POST['AnswerUser'][$question->id]) && is_array($_POST['AnswerUser'][$question->id])) {
                $answerIds = array();
                $sections = array();
                foreach ($_POST['AnswerUser'][$question->id] as $answerUser) {
                    $answerIds[$question->id][] = $answerUser['answer_id'];
                    $sections[$question->id][] = $answerUser['answer_id'];
                }
                if (count($answerIds)) {
                    $result = Db::getInstance()->query('SELECT user_id FROM ' . AnswerUser::model()->table . ' WHERE answer_id IN (' . implode(',', $answerIds) . ')');
                    while ($row = $result->fetch_object()) {
                        $userIds[] = $row->user_id;
                    }
                }
            }*/

            $query2 = <<<SQL
SELECT answer.*
FROM answer
JOIN question_answer ON question_answer.answer_id=answer.id AND question_answer.question_id={$question->id}
SQL;
            $result2 = Db::getInstance()->query($query2);
            $ids = array();
            while ($answer = $result2->fetch_object()) {
                $answers[$question->id][$answer->id] = new Answer(false);
                $answers[$question->id][$answer->id]->setAttributes((array)$answer);
                $ids[] = $answer->id;
            }
            $whereUser = isset($userIds) && count($userIds) ? ' AND user_id IN(' . implode(',', $userIds) . ')' : '';
            //echo 'SELECT * FROM ' . AnswerUser::model()->table . ' WHERE answer_id IN('. implode(',', $ids) . ')' . $whereUser;
            $result3 = Db::getInstance()->query('SELECT * FROM ' . AnswerUser::model()->table . ' WHERE answer_id IN('. implode(',', $ids) . ')' . $whereUser);
            $maxCount = 0;
            while ($answerUser = $result3->fetch_object()) {
                $maxCount = ++$answers[$question->id][$answerUser->answer_id]->count;
                if ($questions[$question->id]->getMaxCount() < $maxCount) {
                    $questions[$question->id]->setMaxCount($maxCount);
                }
            }
        }

        $this->render('result', array('poll' => $poll, 'questions' => $questions, 'answers' => $answers, 'sections' => isset($sections) ? $sections : array(), 'error' => (isset($userIds) && !count($userIds))));
    }

    private function render($file, $data = null)
    {
        if (is_array($data)) {
            extract($data, EXTR_PREFIX_SAME, 'data');
        }
        $path = 'template/' . $file . '.php';
        if (file_exists($path)) {
            ob_start();
            ob_implicit_flush(false);
            require($path);
            $this->content .= ob_get_clean();
            //ob_end_clean();
        }
    }

    /**
     * Redirects the browser to the specified URL.
     * @param string $url URL to be redirected to. Note that when URL is not
     * absolute (not starting with "/") it will be relative to current request URL.
     * @param boolean $terminate whether to terminate the current application
     * @param integer $statusCode the HTTP status code. Defaults to 302. See {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}
     * for details about HTTP status code.
     */
    public function redirect($url, $terminate = true, $statusCode = 302)
    {
        if (strpos($url, '/') === 0 && strpos($url, '//') !== 0) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
        }
        header('Location: ' . $url, true, $statusCode);
        if ($terminate) {
            exit;
        }
    }
}
