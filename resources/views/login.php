<div class="row">
    <div class="col-sm-offset-3 col-sm-6">
        <h1 class="text-center"><?php echo _('Login'); ?></h1>

        <form method="post">
            <?php echo Form::token(); ?>

            <div class="form-group">
                <?php echo $form['user']; ?>
            </div>

            <div class="form-group">
                <?php echo $form['password']; ?>
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
                <input type="hidden" name="referer" value="<?php echo urlencode(getenv('HTTP_REFERER')); ?>" />

                <button type="submit" class="btn btn-danger btn-lg pull-right" name="action" value="login">
                    <?php echo _('Login'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
