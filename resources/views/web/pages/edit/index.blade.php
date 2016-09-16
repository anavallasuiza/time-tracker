@extends('web.layouts.dashboard')
@section('content')
    <div class="row">
    <div class="col-xs-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?php echo _('Activities'); ?></h2>
            </div>
        </div>

        <div class="list-group">
            <?php foreach ($activities as $activity) { ?>
            <a href="<?php echo url(route('edit.activity.edit', ['id' => $activity->id])); ?>" class="list-group-item">
                <?php echo $activity->name; ?>

                <span class="label label-<?php echo $activity->archived ? 'warning' : 'primary'; ?> pull-right">
                    <?php echo $activity->total_hours; ?>
                    <i class="glyphicon glyphicon-<?php echo $activity->archived ? 'pause' : 'ok'; ?>"></i>
                </span>
            </a>
            <?php } ?>
        </div>

        <a href="<?php echo url(route('edit.activity.add')); ?>" class="btn btn-success btn-block"><?php echo _('Add new activity'); ?></a>
    </div>

    <div class="col-xs-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?php echo _('Tags'); ?></h2>
            </div>
        </div>

        <div class="list-group">
            <?php foreach ($tags as $tag) { ?>
            <a href="<?php echo url(route('edit.tag.edit', ['id' => $tag->id])); ?>" class="list-group-item"><?php echo $tag->name; ?></a>
            <?php } ?>
        </div>

        <a href="<?php echo url(route('edit.tag.add')); ?>" class="btn btn-success btn-block"><?php echo _('Add new tag'); ?></a>
    </div>

    <?php if ($users) { ?>
    <div class="col-xs-6 mt-20">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?php echo _('Users'); ?></h2>
            </div>
        </div>

        <div class="list-group">
            <?php foreach ($users as $user) { ?>
            <a href="<?php echo url(route('edit.user.edit', ['id' => $user->id])); ?>" class="list-group-item">
                <?php echo $user->name; ?>

                <span class="label label-<?php echo $user->enabled ? 'primary' : 'warning'; ?> pull-right">
                    <?php echo $user->total_hours; ?>
                    <i class="glyphicon glyphicon-<?php echo $user->enabled ? 'ok' : 'paused'; ?>"></i>
                </span>
            </a>
            <?php } ?>
        </div>

        <a href="<?php echo url(route('edit.user.add')); ?>" class="btn btn-success btn-block"><?php echo _('Add new user'); ?></a>
    </div>
    <?php } ?>

    <?php if ($clients) { ?>
    <div class="col-xs-6 mt-20">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?php echo _('Clients'); ?></h2>
            </div>
        </div>

        <ul class="list-group">
            <?php foreach ($clients as $client) { ?>
            <li class="list-group-item">
                <a href="<?php echo url(route('edit.client.edit', ['id' => $client->id])); ?>" class="text-black">
                    <?php echo $client->name; ?>
                </a>
                <div class="pull-right">
                    <span class="label label-success" data-toggle="tooltip" data-placement="top" title="<?php echo sprintf(_('%s active activities'), $client->activitiesActives()->count())?>"><?php echo $client->activitiesActives()->count() ?></span>
                    <span class="label label-default" data-toggle="tooltip" data-placement="top" title="<?php echo sprintf(_('%s archived activities'), $client->activitiesArchived()->count())?>"><?php echo $client->activitiesArchived()->count() ?></span>
                </div>
            </li>
            <?php } ?>
        </ul>

        <a href="<?php echo url(route('edit.client.add')); ?>" class="btn btn-success btn-block"><?php echo _('Add new client'); ?></a>
    </div>
    <?php } ?>
</div>
@stop