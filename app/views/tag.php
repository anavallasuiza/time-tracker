<form method="post">
    <?= Form::token(); ?>
    <?= $form['id']; ?>

    <h1><?= sprintf(_('Tag %s'), $tag->name); ?></h1>

    <div class="form-group">
        <?= $form['name']; ?>
    </div>

    <div class="form-group text-center">
        <a href="<?= url('/edit/'); ?>" class="btn btn-info">
            <i class="fa fa-undo"></i>
            <?= _('Back'); ?>
        </a>

        <button type="submit" name="action" value="tag<?= empty($tag->id) ? 'Add' : 'Edit'; ?>" class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?= _('Save'); ?>
        </button>
    </div>
</form>