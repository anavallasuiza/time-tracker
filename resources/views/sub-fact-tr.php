<tr data-id="<?php echo $fact->id; ?>" data-remote="<?php echo $fact->remote_id; ?>" class="row-fact">
    <td class="column-user"><a href="?user=<?php echo $fact->users->id; ?>"><?php echo $fact->users->name; ?></a></td>

    <?php if ($fact->description) { ?>
    <td class="column-activity"><a href="?activity=<?php echo $fact->activities->id; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $fact->description; ?>"><?php echo $fact->activities->name; ?> *</a></td>
    <?php } else { ?>
    <td class="column-activity"><a href="?activity=<?php echo $fact->activities->id; ?>"><?php echo $fact->activities->name; ?></a></td>
    <?php } ?>

    <td class="column-tag">
    <?php foreach ($fact->tags as $tag):?>
        <a href="?tag=<?php echo $tag->id; ?>" class="label label-default"><?php echo $tag->name?></a>
    <?php  endforeach;?>
    </td>
    <?php if($fact->activities->hasClient()): ?>
        <td><a href="?client=<?php echo $fact->activities->client->id; ?>"><?php echo $fact->activities->client->name; ?></a></td>
    <?php else:?>
        <td> <?php echo _('No client')?></td>
    <?php endif;?>
    <td class="text-center column-start"><?php echo $fact->start_time->format($user->dateFormat); ?></td>
    <td class="text-center column-end"><?php echo $fact->end_time->format($user->dateFormat); ?></td>

    <td class="text-right column-time">
        <div class="col-xs-8">
            <?php echo date('H:i', mktime(0, $fact->total_time)); ?>
        </div>

        <div class="col-xs-4 column-actions">
            <?php if ($user->admin || ($user->id === $fact->users->id)) { ?>
            <a href="#" class="glyphicon glyphicon-pencil" title="<?php echo _('Edit'); ?>" data-action="edit">
                <span><?php echo _('Edit'); ?></span>
            </a>
            <?php } ?>
        </div>
    </td>
</tr>