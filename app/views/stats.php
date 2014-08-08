<?= View::make('sub-filters')->with([
    'filters' => $filters
])->render(); ?>

<?php foreach ($stats as $group) { ?>
<?php if (empty($group['rows'])) continue; ?>

<h2><?= $group['title']; ?></h2>

<?php foreach ($group['rows'] as $row) { ?>
<h4 class="text-muted">
    <?php if ($row['selected']) { ?>
    <a href="<?= \App\Libs\Utils::url($group['filter'], null); ?>" class="fa fa-times text-muted"></a>
    <?php } ?>

    <a href="<?= \App\Libs\Utils::url($group['filter'], $row['id']); ?>"><?= $row['name']; ?></a>
    (<?= \App\Libs\Utils::minutes2hour($row['time']); ?> - <?= $row['percent']; ?>%)
</h4>

<div class="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="<?= $row['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $row['percent']; ?>%;">
    </div>
</div>
<?php } ?>
<?php } ?>