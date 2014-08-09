<form method="post">
    <?= Form::token(); ?>
    <?= $form['id']; ?>

    <h1><?= sprintf(_('Tag %s'), $tag->name); ?></h1>

    <?= $form['name']; ?>

    <div class="alert alert-warning">
        <?= sprintf(_('Take care %s this tag. There are users using automated tools based in predefined tags. Name used to this tag must be exactly as their have previously defined or old tags names will be created again.'), empty($tag->id) ? 'creating' : 'editing'); ?>
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