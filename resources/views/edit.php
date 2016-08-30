<div class="row">
    <div class="col-xs-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?= _('Activities'); ?></h2>
            </div>
        </div>

        <div class="list-group">
            <?php foreach ($activities as $activity) { ?>
            <a href="<?= url('activity', $activity->id); ?>" class="list-group-item">
                <?= $activity->name; ?>

                <span class="label label-<?= $activity->archived ? 'warning' : 'primary'; ?> pull-right">
                    <?= $activity->total_hours; ?>
                    <i class="glyphicon glyphicon-<?= $activity->archived ? 'pause' : 'ok'; ?>"></i>
                </span>
            </a>
            <?php } ?>
        </div>

        <a href="<?= url('activity'); ?>" class="btn btn-success btn-block"><?= _('Add new activity'); ?></a>
    </div>

    <div class="col-xs-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?= _('Tags'); ?></h2>
            </div>
        </div>

        <div class="list-group">
            <?php foreach ($tags as $tag) { ?>
            <a href="<?= url('tag', $tag->id); ?>" class="list-group-item"><?= $tag->name; ?></a>
            <?php } ?>
        </div>

        <a href="<?= url('tag'); ?>" class="btn btn-success btn-block"><?= _('Add new tag'); ?></a>
    </div>

    <?php if ($users) { ?>
    <div class="col-xs-6 mt-20">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?= _('Users'); ?></h2>
            </div>
        </div>

        <div class="list-group">
            <?php foreach ($users as $user) { ?>
            <a href="<?= url('user', $user->id); ?>" class="list-group-item">
                <?= $user->name; ?>

                <span class="label label-<?= $user->enabled ? 'primary' : 'warning'; ?> pull-right">
                    <?= $user->total_hours; ?>
                    <i class="glyphicon glyphicon-<?= $user->enabled ? 'ok' : 'paused'; ?>"></i>
                </span>
            </a>
            <?php } ?>
        </div>

        <a href="<?= url('user'); ?>" class="btn btn-success btn-block"><?= _('Add new user'); ?></a>
    </div>
    <?php } ?>
</div>
