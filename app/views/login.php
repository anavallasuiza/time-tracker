<div class="row">
    <div class="col-sm-offset-3 col-sm-6">
        <h1 class="text-center"><?= _('Login'); ?></h1>

        <form method="post">
            <?= Form::token(); ?>

            <div class="form-group">
                <?= $form['user']; ?>
            </div>

            <div class="form-group">
                <?= $form['password']; ?>
            </div>

            <div class="hidden">
                <div class="col-ms-6 form-group">
                    <input type="email" name="email" class="form-control required" />
                </div>

                <div class="col-ms-6 form-group">
                    <input type="varchar" name="url" class="form-control required" />
                </div>
            </div>

            <div class="form-group clearfix">
                <input type="hidden" name="referer" value="<?= urlencode(getenv('HTTP_REFERER')); ?>" />

                <button type="submit" class="btn btn-danger btn-lg pull-right" name="action" value="login">
                    <?= _('Login'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
