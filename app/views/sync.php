
<?php foreach ($response as $data) { ?>
<h2><?= $data['user']; ?></h2>

<?php foreach ($data['log'] as $row) { ?>
<div class="alert alert-<?= $row['status']; ?>">
    <?= $row['message']; ?>
</div>
<?php } ?>

<?php } ?>

<div class="text-center">
    <a href="<?= url('/'); ?>" class="btn btn-info"><?= _('Back'); ?></a>
</div>