<form class="text-center well submit-wait" data-message="<?= _('Please wait...'); ?>" method="post">
    <?= Form::token(); ?>
    <a href="<?= url('/'); ?>" class="btn btn-info"><?= _('Back'); ?></a>

    <button type="submit" name="action" value="sync" class="btn btn-success">
        <i class="glyphicon glyphicon-refresh"></i>
        <?= _('Syncronize database'); ?>
    </button>
</form>

<?php if ($action) { ?>

<?php foreach ($response as $name => $log) { ?>
<h2><?= $name; ?></h2>

<?php foreach ($log as $row) { ?>
<div class="alert alert-<?= $row['status']; ?>">
    <?= $row['message']; ?>
</div>
<?php } ?>

<?php } ?>

<?php } ?>
