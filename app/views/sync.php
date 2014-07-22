
<?php if (is_array($response)) { ?>

<?php foreach ($response as $name => $log) { ?>
<h2><?= $name; ?></h2>

<?php foreach ($log as $row) { ?>
<div class="alert alert-<?= $row['status']; ?>">
    <?= $row['message']; ?>
</div>
<?php } ?>

<?php } ?>

<?php } else { ?>

<pre><code><?= $response; ?></code></pre>

<?php } ?>

<div class="text-center">
    <a href="<?= url('/'); ?>" class="btn btn-info"><?= _('Back'); ?></a>
</div>