<?= View::make('sub-filters')->with([
    'filters' => $filters
])->render(); ?>

<table class="table">
    <?php foreach ($calendar as $week => $days) { ?>
    <tr>
        <?php foreach ($days as $hours) { ?>
        <th>
            <?= date('j/n', $hours['time']); ?>
        </th>
        <?php } ?>
    </tr>

    <tr>
        <?php foreach ($days as $hours) { ?>
        <td>
            <a href="<?= url('/').'?'.getenv('QUERY_STRING'); ?>"><?= \App\Libs\Utils::minutes2hour($hours['hours']); ?></a>
        </td>
        <?php } ?>
    </tr>
    <?php } ?>
</table>