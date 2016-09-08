<form method="post">
    <?= Form::token(); ?>
    <?= $form['id']; ?>

    <h1><?= sprintf($formHeader); ?></h1>

    <div class="row">
        <div class="col-sm-4 col-xs-6">
            <?= $form['name']; ?>
        </div>
    </div>

    <div class="form-group text-center">
        <a href="<?= url('/edit/'); ?>" class="btn btn-info">
            <i class="fa fa-undo"></i>
            <?= _('Back'); ?>
        </a>

        <button type="submit" name="action" value="user<?= empty($clientId) ? 'Add' : 'Edit'; ?>" class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?= _('Save'); ?>
        </button>
    </div>
</form>