<?= View::make('sub-filters')->with([
    'filters' => $filters
])->render(); ?>

<?php foreach ($stats as $group => $values) { ?>
<?php if (empty($values)) continue; ?>

<h2><?= $group; ?></h2>

<?php foreach ($values as $row) { ?>
<h4 class="text-muted"><?= $row['name']; ?> (<?= \App\Libs\Utils::minutes2hour($row['time']); ?>)</h4>

<div class="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="<?= $row['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $row['percent']; ?>%;">
    </div>
</div>
<?php } ?>
<?php } ?>