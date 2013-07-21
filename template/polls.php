<?php
/**
 * @var Poll[] $polls
 * @var string $alertText
 */
?>
<div class="btn-group">
    <a href="?action=index&amp;status=<?=Poll::STATUS_ACTIVE?>" class="btn btn-info<?=$polls[0]->status == Poll::STATUS_ACTIVE ? ' active' : '';?>">Активные</a>
    <a href="?action=index" class="btn btn-inverse<?=$polls[0]->status == Poll::STATUS_DRAFT ? ' active' : '';?>">Черновики</a>
    <a href="?action=index&amp;status=<?=Poll::STATUS_CLOSED?>" class="btn<?=$polls[0]->status == Poll::STATUS_CLOSED ? ' active' : '';?>">Закрытые</a>
</div>
<div>&nbsp;</div>
<?php if (!count($polls)):?>
    <div class="alert in alert-block fade alert-info"><a href="#" class="close" data-dismiss="alert">×</a>Нет опросов.</div>
<?php else:?>
    <?php if ($alertText):?>
        <div class="alert in alert-block fade alert-info"><a href="#" class="close" data-dismiss="alert">×</a><?=$alertText?></div>
    <?php endif;?>
<table class="table table-striped table-bordered">
    <tr>
        <th>#</th>
        <th>Список опросов-вопросов внутри</th>
        <th>Управление</th>
    </tr>
<?php foreach ($polls as $poll): /** @var Poll $poll */ ?>
    <tr>
        <td><?=$poll->id?></td>
        <td><?=$poll->text?></td>
        <td>
<?php if ($poll->status == Poll::STATUS_ACTIVE):?>
            <a class="btn btn-mini" href='?action=result&amp;id=<?=$poll->id?>'>Посмотреть результаты</a>
            <a class="btn btn-mini" href='?action=index&amp;do=close&amp;id=<?=$poll->id?>'>Закрыть опрос</a>
<?php elseif ($poll->status == Poll::STATUS_DRAFT):?>
            <a class="btn btn-mini" href='?action=update&amp;id=<?=$poll->id?>'>Изменить</a>
            <a class="btn btn-mini" href='?action=index&amp;do=open&amp;id=<?=$poll->id?>'>Активировать опрос</a>
<?php else:?>
            <a class="btn btn-mini" href='?action=result&amp;id=<?=$poll->id?>'>Посмотреть результаты</a>
            <a class="btn btn-mini" href='?action=index&amp;do=open&amp;id=<?=$poll->id?>'>Активировать опрос</a>
<?php endif;?>
            <a class="btn btn-mini" href='?action=delete&amp;id=<?=$poll->id?>'>Удалить</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
<?php endif;?>
