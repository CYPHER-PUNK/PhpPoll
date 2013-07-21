<?php
/**
 * @var Poll $poll
 * @var Question[] $questions
 * @var array[] $answers
 */
?>
<h2><?=$poll->text?></h2>
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
