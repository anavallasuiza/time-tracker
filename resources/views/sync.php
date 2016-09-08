<form class="text-center well submit-wait" data-message="<?php echo _('Please wait...'); ?>" method="post">
    <?php echo Form::token(); ?>
    <a href="<?php echo url('/'); ?>" class="btn btn-info"><?php echo _('Back'); ?></a>

    <button type="submit" name="action" value="sync" class="btn btn-success">
        <i class="glyphicon glyphicon-refresh"></i>
        <?php echo _('Syncronize database'); ?>
    </button>
</form>

<?php if ($action) { ?>

<?php foreach ($response as $name => $log) { ?>
<h2><?php echo $name; ?></h2>

<?php foreach ($log as $row) { ?>
<div class="alert alert-<?php echo $row['status']; ?>">
    <?php echo $row['message']; ?>
</div>
<?php } ?>

<?php } ?>

<?php } ?>
