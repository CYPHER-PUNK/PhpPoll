<?php
/**
 * @var $question Question
 * @var $answers Answer[]
 */
?>
<hr />
<div class="control-group <?php if ($question->errorsText()) echo 'error';?>">
    <label class="control-label" for="Question_<?=$question->id;?>"><?=$question->errorsText()?></label>
    <div class="controls">
        <input id="Question_<?=$question->id;?>" type="text" class="span5" name="Question[<?=$question->id;?>][text]" placeholder="Введите вопрос" value="<?=$question->text;?>">
    </div>
</div>
<div class="control-group">
    <div class="controls">
        <label class="checkbox">
            <input name="Question[<?=$question->id;?>][is_required]" type="checkbox"<?php echo $question->is_required ? ' checked="checked"' : ''; ?>> Обязательный
        </label>
    </div>
</div>
<div class="control-group">
    <div class="controls">
        <label class="checkbox">
            <input name="Question[<?=$question->id;?>][is_multiple]" type="checkbox"<?php echo $question->is_multiple ? ' checked="checked"' : ''; ?>> Можно выбирать несколько вариантов
        </label>
    </div>
</div>
<div class="control-group">
    <label class="control-label">Варианты ответа</label>
    <div class="controls">
        <div class="input-append">
<?php $i=0; $style=''; foreach ($answers[$question->id] as $answer):/** @var Answer $answer */?>
<?php if ($i!==0) {echo '<br /><br />';}++$i;?>
    <?php if(count($answer->errors)):?>
        <?php $style = ' style="border-color: #b94a48;';?>
        <span class="error" style="color: #b94a48; font-size: 14px;"><?=$answer->errorsText();?></span><br />
    <?php endif ?>
        <input<?=$style?> class="span3" id="<?=$question->id?>" name="Answer[<?=$question->id;?>][][text]" type="text" value="<?=$answer->text?>" placeholder="Введите ответ" />
<?php endforeach; ?>
            <button onclick="addFormAnswer($(this))" class="btn btn-info" type="button">+</button>
        </div>
    </div>
</div>