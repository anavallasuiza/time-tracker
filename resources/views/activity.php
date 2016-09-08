<form method="post">
    <?= Form::token(); ?>
    <?= $form['id']; ?>

    <h1><?= sprintf(_('Activity %s'), $activity->name); ?></h1>
    <div class="alert alert-warning">
        <?= sprintf(_('Take care %s this activity. There are users using automated tools based in Basecamp projects names. Name used to this activity must be exactly as Basecamp project name or duplicated projects will be created.'), empty($activity->id) ? 'creating' : 'editing'); ?>
    </div>

    <div class="row">
        <div class="col-lg-8 col-sm-6 col-xs-12">
            <?= $form['name']; ?>
        </div>

        <div class="col-lg-2 col-sm-3 col-xs-6">
            <?= $form['archived']; ?>
        </div>

        <div class="col-lg-2 col-sm-3 col-xs-6">
            <?= $form['total_hours']; ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><?= _('Client'); ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
            <?= $form['id_clients']; ?>
        </div>

    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><?= _('Times by tag'); ?></h2>
        </div>
    </div>

    <div class="row">
        <?php foreach ($tags as $tag) { ?>
        <div class="col-lg-2 col-md-3 col-xs-8">
            <div class="form-group">
                <label for="tag-<?= $tag->id; ?>"><?= $tag->name; ?></label>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-xs-4">
            <div class="form-group">
                <input type="text" class="form-control" name="tags[<?= $tag->id; ?>]" value="<?= isset($tag->estimations[0]) ? $tag->estimations[0]->hours : ''; ?>" placeholder="<?= _('Hours'); ?>">
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="form-group text-center">
        <a href="<?= url('/edit/'); ?>" class="btn btn-info">
            <i class="fa fa-undo"></i>
            <?= _('Back'); ?>
        </a>

        <button type="submit" name="action" value="activity<?= empty($activity->id) ? 'Add' : 'Edit'; ?>" class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?= _('Save'); ?>
        </button>
    </div>
</form>