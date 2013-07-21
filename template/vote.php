<?php
/**
 * @var array $questions
 * @var array $answers
 */
?>
<h2><?=$poll->text?></h2>
<div class="text-error"><?=$poll->errorsText()?></div>
<form method="POST" class="form-horizontal">
    <input type="hidden" name="Poll[id]" value="<?=$poll->id?>">
    <ol>
<?php foreach ($questions as $question) {
            include('_vote.php');
        }
?>
    </ol>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn">Сохранить</button>
        </div>
    </div>
</form>