<form class="text-center well submit-wait" data-message="<?= _('Please wait...'); ?>" method="post">
    <input type="hidden" name="action" value="gitUpdate" />

    <a href="<?= url('/'); ?>" class="btn btn-info"><?= _('Back'); ?></a>

    <button type="submit" name="update" value="repository" class="btn btn-success">
        <i class="glyphicon glyphicon-refresh"></i>
        <?= _('Update repository'); ?>
    </button>

    <button type="submit" name="update" value="composer" class="btn btn-success">
        <i class="glyphicon glyphicon-refresh"></i>
        <?= _('Update composer'); ?>
    </button>
</form>

<?php if ($action) { ?>
<pre><code><?= $response; ?></code></pre>
<?php } ?>