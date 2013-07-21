<?php
/**
 * @var Question $question
 * @var array $answers
 * @var AnswerUser $answerUser
 */
$checked = array();
$type = $question->is_multiple ? 'checkbox' : 'radio';
?>
<div class="text-error"><?=$question->errorsText()?></div>
<li>
    <?=$question->text?>
    <span style="color: red"><?=$question->is_required ? '*' : ''?></span>
<?php foreach ($answers[$question->id] as $answer): /** @var Answer $answer */
$checked = isset($answerUser[$question->id][$answer->id]) ? ' checked="checked"' : '';
?>
    <label class="<?=$type?>">
        <input name="AnswerUser[<?=$question->id?>][][answer_id]" type="<?=$type?>" value="<?=$answer->id?>"<?=$checked?>>
        <?=$answer->text?>
        <?=$answer->errorsText()?>
    </label>
<?php endforeach;?>
<div>&nbsp;</div>
</li>
