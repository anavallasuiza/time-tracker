<form method="post">
    <?= Form::token(); ?>
    <?= $form['id']; ?>

    <h1><?= sprintf(_('Activity %s'), $activity->name); ?></h1>

    <div class="row">
        <div class="col-lg-10 col-xs-8">
            <div class="form-group">
                <?= $form['name']; ?>
            </div>
        </div>

        <div class="col-lg-2 col-xs-4">
            <div class="form-group">
                <input type="text" name="total_hours" value="<?= sprintf(_('Estimated %s hours'), $activity->total_hours); ?>" class="form-control text-center" readonly />
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="checkbox">
            <label>
                <?= $form['archived']; ?>
                <?= _('This activity is archived'); ?>
            </label>
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