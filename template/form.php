<?php
/**
 * @var $this Controller
 * @var $poll Poll
 * @var $questions Question[]
 * @var $saved bool|null
 */
?>
<script type="text/javascript">
    $(function() {
        formHtml = $("#form").html();
    });
    var pollCount = 0;
    function addForm() {
        pollCount++;
        $(formHtml)
            .slice(0, 9)//берем первые только
            .find('.input-append input:gt(0), .input-append br')//оставляем один пустой ответ
            .remove()
            .end()

            .find(':input')//чистим inputs
            .each(function() {
                $(this).val('');
                $(this).attr('checked', false);
                this.name = this.name.replace(/\[(\d+)\]/, function(){
                    return '[' + pollCount + ']';
                });
                this.id = pollCount;
            })
            .end()
            .appendTo($("#form"));
    }
    function addFormAnswer(el) {
        var lastInput = el.parent().find('input:last');
        var currentQuestionNumber = $(lastInput).attr('id');
        var newInput = $('<br /><br /><input id="' + currentQuestionNumber + '" class="span3 added" name="Answer[' + currentQuestionNumber + '][][text]" type="text">');
        $(lastInput).after(newInput);
        newInput.focus();
    }
</script>
<?php if ($saved) echo '<div class="alert in alert-block fade alert-success"><a href="#" class="close" data-dismiss="alert">×</a>Опрос сохранен.</div>';?>
<h2><?=(isset($_GET['action']) && $_GET['action'] == 'update') ? 'Изменить' : 'Создать'?> опрос</h2>
<form method="POST" class="form-horizontal">
    <div class="control-group <?php if ($poll->errorsText()) echo 'error';?>">
        <label class="control-label" for="Poll_<?=$poll->id;?>"><?=$poll->errorsText()?></label>
        <div class="controls">
            <input autofocus="autofocus" id="Poll_<?=$poll->id;?>" type="text" class="span5" name="Poll[text]" placeholder="Название опроса" value="<?=$poll->text;?>">
        </div>
    </div>
    <div id="form">
        <?php foreach ($questions as $question) {
            include('_form.php');
        }
        ?>
    </div>
    <div class="control-group">
        <button class="add_answers btn btn-success" onclick="addForm(); return false;"><i class="icon-plus icon-white"></i> Добавить вопрос</button>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn">Сохранить</button>
        </div>
    </div>
</form>
