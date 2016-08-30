<?= View::make('sub-filters')->with([
    'filters' => $filters
])->render(); ?>

<ul class="nav nav-tabs" role="tablist">
    <li class="dropdown active">
        <a role="button" data-toggle="dropdown" href="#">
            <?= $filters['times'] ? _('Times relatives to dates') : _('Times relatives to projects'); ?>
            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu" role="menu">
            <li role="presentation">
                <a role="menuitem" tabindex="-1" href="<?= \App\Libs\Utils::url('times', null); ?>"><?= _('Times relatives to projects'); ?></a>
            </li>

            <li role="presentation">
                <a role="menuitem" tabindex="-1" href="<?= \App\Libs\Utils::url('times', 'dates'); ?>"><?= _('Times relatives to dates'); ?></a>
            </li>
        </ul>
    </li>
</ul>

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