<tr data-id="<?= $fact->id; ?>" data-remote="<?= $fact->remote_id; ?>" class="row-fact">
    <td class="column-user"><a href="?user=<?= $fact->users->id; ?>"><?= $fact->users->name; ?></a></td>

    <?php if ($fact->description) { ?>
    <td class="column-activity"><a href="?activity=<?= $fact->activities->id; ?>" data-toggle="tooltip" data-placement="right" title="<?= $fact->description; ?>"><?= $fact->activities->name; ?> *</a></td>
    <?php } else { ?>
    <td class="column-activity"><a href="?activity=<?= $fact->activities->id; ?>"><?= $fact->activities->name; ?></a></td>
    <?php } ?>

    <td class="column-tag"><?=
        implode(', ', array_column($fact->tags->toArray(), 'name'));
    ?></td>

    <td class="text-center column-start"><?php
        echo $fact->start_time->format('d/m/Y');
        echo '<span class="hour'.($user->admin ? '' : ' hidden').'"> '.$fact->start_time->format('H:i').'</span>';
    ?></td>

    <td class="text-center column-end"><?php
        echo $fact->end_time->format('d/m/Y');
        echo '<span class="hour'.($user->admin ? '' : ' hidden').'"> '.$fact->end_time->format('H:i').'</span>';
    ?></td>

    <td class="text-center column-time">
        <div class="col-xs-8">
            <?= date('H:i', mktime(0, $fact->total_time)); ?>
        </div>

        <div class="col-xs-4 column-actions">
            <a href="#" class="glyphicon glyphicon-pencil" title="<?= _('Edit'); ?>" data-action="edit"><span><?= _('Edit'); ?></span></a>
        </div>
    </td>
</tr>