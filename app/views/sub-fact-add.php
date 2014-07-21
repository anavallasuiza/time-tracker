<form method="post" id="facts-form-add" class="facts-form">
    <?= Form::token(); ?>

    <div class="row">
        <div class="col-sm-3 form-group">
            <select name="activity" class="form-control" required>
                <option value=""><?= _('Select one activity'); ?></option>
                <?php foreach ($activities as $activity) { ?>
                <option value="<?= $activity->id; ?>"><?= $activity->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-2 form-group">
            <select name="tag" class="form-control" required>
                <option value=""><?= _('Select one tag'); ?></option>
                <?php foreach ($tags as $tag) { ?>
                <option value="<?= $tag->id; ?>"><?= $tag->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-2 col-xs-6 text-center form-group">
            <input type="text" name="start" value="<?= (new \Datetime())->format('d/m/Y H:i'); ?>" class="form-control" placeholder="<?= _('Start date and hour'); ?>" />
        </div>

        <div class="col-sm-2 col-xs-6 text-center form-group">
            <input type="text" name="end" value="<?= (new \Datetime())->format('d/m/Y H:i'); ?>" class="form-control" placeholder="<?= _('Start date and hour'); ?>" />
        </div>

        <div class="col-sm-1 text-center form-group">
            <input type="text" name="time" value="00:00" class="form-control text-center" readonly />
        </div>

        <div class="col-sm-2 text-center form-group">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="action" value="add" />

            <button type="button" data-action="play" class="btn btn-primary">
                <i class="glyphicon glyphicon-play" title="<?= _('Start'); ?>"></i>
            </button>

            <button type="button" data-action="refresh" class="btn btn-info hidden">
                <i class="glyphicon glyphicon-refresh" title="<?= _('Refresh'); ?>"></i>
            </button>

            <button type="submit" name="action" value="add" class="btn btn-success">
                <i class="glyphicon glyphicon-floppy-disk" title="<?= _('Stop and save'); ?>"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <input type="text" name="description" value="" class="form-control" placeholder="<?= _('Activity description'); ?>" />
    </div>

    <div class="form-group alert alert-danger hidden" rel="error-message"></div>
</form>