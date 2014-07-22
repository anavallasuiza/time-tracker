<?= View::make('sub-filters')->with([
    'filters' => $filters,
    'sort' => $sort
])->render(); ?>

<table class="table table-hover facts-table">
    <thead>
        <tr>
            <th class="column-user"><?= _('User'); ?></th>
            <th class="column-activity"><?= _('Activity'); ?></th>
            <th class="column-tag"><?= _('Tags'); ?></th>
            <th class="text-center column-start">
                <a href="<?= \App\Libs\Utils::url('sort', ($sort === 'start-desc') ? 'start-asc' : 'start-desc'); ?>"><?= _('Start time'); ?></a>
            </th>
            <th class="text-center column-end">
                <a href="<?= \App\Libs\Utils::url('sort', ($sort === 'end-desc') ? 'end-asc' : 'end-desc'); ?>"><?= _('End time'); ?></a>
            </th>
            <th class="text-center column-time">
                <a href="<?= \App\Libs\Utils::url('sort', ($sort === 'total-desc') ? 'total-asc' : 'total-desc'); ?>"><?= _('Total time'); ?></a>
            </th>
        </tr>
    </thead>

    <tbody>
        <?php
        foreach ($facts as $fact) {
            echo View::make('sub-fact-tr')->with('fact', $fact)->render();
        }
        ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="6"><strong class="pull-right"><?= sprintf(_('Total time: %s'), $total_time); ?></strong></td>
        </tr>
    </tfoot>
</table>

<?= View::make('sub-fact-edit')->with([
    'activities' => $activities,
    'tags' => $tags
])->render(); ?>

<div class="text-center">
    <?php
    if (($rows !== -1) && ($facts->getLastPage() > 1)) {
        echo $facts->appends(Input::all())->links();
    }
    ?>

    <?php
    echo '<p>';

    echo ($rows === 20) ? '<strong>' : '';
    echo '<a href="'.\App\Libs\Utils::url('rows', 20).'">'._('20').'</a>';
    echo ($rows === 20) ? '</strong>' : '';

    echo ' | '.(($rows === 50) ? '<strong>' : '');
    echo '<a href="'.\App\Libs\Utils::url('rows', 50).'">'._('50').'</a>';
    echo ($rows === 50) ? '</strong>' : '';

    echo ' | '.(($rows === 100) ? '<strong>' : '');
    echo '<a href="'.\App\Libs\Utils::url('rows', 100).'">'._('100').'</a>';
    echo ($rows === 100) ? '</strong>' : '';

    echo ' | '.(($rows === -1) ? '<strong>' : '');
    echo '<a href="'.\App\Libs\Utils::url('rows', -1).'">'._('All').'</a>';
    echo ($rows === -1) ? '</strong>' : '';

    echo ' | <a href="'.url('/stats').'">'._('Stats').'</a>';
    echo ' | <a href="'.url('/sync').'">'._('Sync').'</a>';
    echo ' | <a href="'.\App\Libs\Utils::url('export', 'csv').'">'._('Export as CSV').'</a>';

    if ($user->admin) {
        echo ' | <a href="'.url('/dump-sql').'">'._('Dump SQL').'</a>';
    }

    echo '</p>';
    ?>
</div>
