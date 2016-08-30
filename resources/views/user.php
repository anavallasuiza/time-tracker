<form method="post">
    <?= Form::token(); ?>
    <?= $form['id']; ?>

    <h1><?= sprintf(_('User %s'), $user->name); ?></h1>

    <div class="row">
        <div class="col-sm-4 col-xs-6">
            <?= $form['name']; ?>
        </div>

        <div class="col-sm-4 col-xs-6">
            <?= $form['user']; ?>
        </div>

        <div class="col-sm-4 col-xs-12">
            <?= $form['email']; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <?= $form['password']; ?>
        </div>

        <div class="col-xs-6">
            <?= $form['password_repeat']; ?>
        </div>
    </div>

    <?= $form['api_key']; ?>

    <div class="row">
        <div class="col-xs-6">
            <?= $form['store_hours']; ?>
        </div>

        <?php if (isset($form['enabled'])) { ?>
        <div class="col-xs-6">
            <?= $form['enabled']; ?>
        </div>
        <?php } ?>
    </div>

    <div class="form-group text-center">
        <a href="<?= url('/edit/'); ?>" class="btn btn-info">
            <i class="fa fa-undo"></i>
            <?= _('Back'); ?>
        </a>

        <button type="submit" name="action" value="user<?= empty($user->id) ? 'Add' : 'Edit'; ?>" class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?= _('Save'); ?>
        </button>
    </div>
</form>