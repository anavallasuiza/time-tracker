<form method="post" id="facts-form-edit" class="facts-form hidden">
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
            <input type="text" name="start" value="" class="form-control" placeholder="<?= _('Start date and hour'); ?>" />
        </div>

        <div class="col-sm-2 col-xs-6 text-center form-group">
            <input type="text" name="end" value="" class="form-control" placeholder="<?= _('Start date and hour'); ?>" />
        </div>

        <div class="col-sm-1 text-center form-group">
            <input type="text" name="time" value="00:00" class="form-control text-center" <?= $user->store_hours ? 'readonly' : ''; ?> />
        </div>

        <div class="col-sm-2 text-center form-group">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="action" value="factEdit" />

            <button type="submit" name="action" value="factEdit" class="btn btn-success">
                <i class="glyphicon glyphicon-floppy-disk" title="<?= _('Save'); ?>"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <input type="text" name="description" value="" class="form-control" placeholder="<?= _('Activity description'); ?>" />
    </div>

    <div class="form-group alert alert-danger hidden" rel="error-message"></div>

    <div class="form-group alert alert-warning hidden" rel="remote">
        <?= _('This fact was added from external app. Please edit original source instead this line.'); ?>
    </div>
</form>
