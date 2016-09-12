<form method="post"  action="{{url(route('v2.time.fact.add'))}}"  id="facts-form-add" class="facts-form">
    <?php echo Form::token(); ?>

    <div class="row">
        <div class="col-sm-3 form-group">
            <select name="activity" class="form-control" required>
                <option value=""><?php echo _('Select one activity'); ?></option>
                <?php foreach ($activities as $activity) { ?>
                <option value="<?php echo $activity->id; ?>"><?php echo $activity->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-2 form-group">
            <select name="tag" class="form-control" required>
                <option value=""><?php echo _('Select one tag'); ?></option>
                <?php foreach ($tags as $tag) { ?>
                <option value="<?php echo $tag->id; ?>"><?php echo $tag->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-2 col-xs-6 text-center form-group">
            <input type="text" name="start" value="<?php echo date('d/m/Y H:i'); ?>" class="form-control" placeholder="<?php echo _('Start date and hour'); ?>" />
        </div>

        <div class="col-sm-2 col-xs-6 text-center form-group">
            <input type="text" name="end" value="<?php echo date('d/m/Y H:i'); ?>" class="form-control" placeholder="<?php echo _('Start date and hour'); ?>" />
        </div>

        <div class="col-sm-1 text-center form-group">
            <input type="text" name="time" value="00:00" class="form-control text-center" <?php echo $user->store_hours ? 'readonly' : ''; ?> />
        </div>

        <div class="col-sm-2 text-center form-group">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="action" value="factAdd" />

            <button type="button" data-action="play" class="btn btn-primary">
                <i class="glyphicon glyphicon-play" title="<?php echo _('Start'); ?>"></i>
            </button>

            <button type="button" data-action="refresh" class="btn btn-info hidden">
                <i class="glyphicon glyphicon-refresh" title="<?php echo _('Refresh'); ?>"></i>
            </button>

            <button type="submit" name="action" value="factAdd" class="btn btn-success">
                <i class="glyphicon glyphicon-floppy-disk" title="<?php echo _('Stop and save'); ?>"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <input type="text" name="description" value="" class="form-control" placeholder="<?php echo _('Activity description'); ?>" />
    </div>

    <div class="form-group alert alert-danger hidden" rel="error-message"></div>
</form>