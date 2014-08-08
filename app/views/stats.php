<?= View::make('sub-filters')->with([
    'filters' => $filters
])->render(); ?>

<?php foreach ($stats as $group) { ?>
<?php if (empty($group['rows'])) continue; ?>

<h2><?= $group['title']; ?></h2>

<?php foreach ($group['rows'] as $row) { ?>
<h4 class="text-muted">
    <div class="clearfix">
        <div class="pull-right">
            <?= \App\Libs\Utils::progressText($row); ?>
        </div>

        <?php if ($row['selected']) { ?>
        <a href="<?= \App\Libs\Utils::url($group['filter'], null); ?>" class="fa fa-times text-muted"></a>
        <?php } ?>

        <a href="<?= \App\Libs\Utils::url($group['filter'], $row['id']); ?>"><?= $row['name']; ?></a>
    </div>
</h4>

<div class="progress">
    <?= \App\Libs\Utils::progressBar($row); ?>
</div>
<?php } ?>
<?php } ?>