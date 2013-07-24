<?php
/**
 * @var Poll $poll
 * @var Question[] $questions
 * @var array[] $answers
 * @var array[] $sections
 * @var bool $error
 */
?>
<h2>Результаты опроса "<?=$poll->text?>"</h2>
<?php if (count($sections)): ?>
    <strong>Выборка по пользователям:</strong><br />
    <?php foreach ($sections as $question_id => $answerIds) {
        echo $questions[$question_id]->text . '<ul>';
        foreach ($answerIds as $answer_id) {
            echo '<li>' . $answers[$question_id][$answer_id]->text . '</li>';
        }
        echo '</ul>';
    }
endif; ?>
<a class="btn" href="?action=vote&amp;sectional=1&amp;id=<?=$poll->id?>">В разрезе</a>
<?php if ($error):?>
    <div class="text-error">По выбранным критериям результаты не найдены! Показаны общие результаты.</div>
<?php endif; ?>
<?php foreach ($questions as $question):?>
<hr />
<div class="row-fluid">
    <div class="span5">
        <h4><?=$question->text?></h4>
        <?php foreach ($answers[$question->id] as $answer):?>
        <strong><?=$answer->text?> </strong><span class="pull-right"><?=$answer->count?></span>
        <div class="progress <?=$answer->count==$question->getMaxCount()?' progress-striped active' : ''; ?>">
            <div class="bar" style="width: <?=$answer->count/$question->getMaxCount()*100 ?>%;"></div>
        </div>
        <?php endforeach ?>
    </div>
</div>
<?php endforeach ?>
