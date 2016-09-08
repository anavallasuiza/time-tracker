<form class="text-center well submit-wait" data-message="<?php echo _('Please wait...'); ?>" method="post">
    <input type="hidden" name="action" value="gitUpdate" />

    <a href="<?php echo url('/'); ?>" class="btn btn-info"><?php echo _('Back'); ?></a>

    <button type="submit" name="update" value="repository" class="btn btn-success">
        <i class="glyphicon glyphicon-refresh"></i>
        <?php echo _('Update repository'); ?>
    </button>

    <button type="submit" name="update" value="composer" class="btn btn-success">
        <i class="glyphicon glyphicon-refresh"></i>
        <?php echo _('Update composer'); ?>
    </button>
</form>

<?php if ($action) { ?>
<pre><code><?php echo $response; ?></code></pre>
<?php } ?>