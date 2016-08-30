<?= View::make('sub-filters')->with([
    'filters' => $filters
])->render(); ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title"><?= _('Total hours worked every day'); ?></h2>
    </div>
</div>

<table class="table calendar">
    <?php foreach ($calendar as $week => $days) { ?>
    <tr>
        <?php foreach ($days as $hours) { ?>
        <th>
            <?= date('D j M', $hours['time']); ?>
        </th>
        <?php } ?>
    </tr>

    <tr>
        <?php foreach ($days as $hours) { ?>
        <td>
            <?php if ($hours['hours'] > 0) { ?>
            <a href="<?= url('/').'?'.http_build_query([
                'user' => $filters['user'],
                'activity' => $filters['activity'],
                'tag' => $filters['tag'],
                'first' => date('d/m/Y', $hours['time']),
                'last' => date('d/m/Y', $hours['time'])
            ]); ?>"><?= \App\Libs\Utils::minutes2hour($hours['hours']); ?></a>
            <?php } else { ?>
            <span class="text-muted">0</span>
            <?php } ?>
        </td>
        <?php } ?>
    </tr>
    <?php } ?>
</table>