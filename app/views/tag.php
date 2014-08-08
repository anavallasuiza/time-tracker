<form method="post">
    <?= Form::token(); ?>
    <?= $form['id']; ?>

    <h1><?= sprintf(_('Tag %s'), $tag->name); ?></h1>

    <div class="form-group">
        <?= $form['name']; ?>
    </div>

    <div class="form-group text-center">
        <button type="submit" name="action" value="tag" class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?= _('Save'); ?>
        </button>
    </div>
</form>