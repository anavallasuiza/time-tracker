@extends('web.layouts.base')
@section('content')
@include('web.molecules.filters.search-filter', [
    'filters' => $filters,
    'sort' => $sort,
    'clients'=>$clients
])
<table class="table table-hover facts-table">
    <thead>
        <tr>
            <th class="column-user"><?php echo _('User'); ?></th>
            <th class="column-activity"><?php echo _('Activity'); ?></th>
            <th class="column-tag"><?php echo _('Tags'); ?></th>
            <th class="column-activity"><?php echo _('Client'); ?></th>
            <th class="text-center column-start">
                <a href="<?php echo \App\Libs\Utils::url('sort', ($sort === 'start-desc') ? 'start-asc' : 'start-desc'); ?>"><?php echo _('Start time'); ?></a>
            </th>
            <th class="text-center column-end">
                <a href="<?php echo \App\Libs\Utils::url('sort', ($sort === 'end-desc') ? 'end-asc' : 'end-desc'); ?>"><?php echo _('End time'); ?></a>
            </th>
            <th class="text-center column-time">
                <a href="<?php echo \App\Libs\Utils::url('sort', ($sort === 'total-desc') ? 'total-asc' : 'total-desc'); ?>"><?php echo _('Total time'); ?></a>
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($facts as $fact)
            @include('web.atoms.fact',['fact'=>$fact])
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="7"><strong class="pull-right"><?php echo sprintf(_('Total time: %s'), $total_time); ?></strong></td>
        </tr>
    </tfoot>
</table>
@include('web.molecules.facts.edit',[
    'activities' => $activities,
    'tags' => $tags
])

<div class="text-center">
    <?php
    if (($rows !== -1) && ($facts->hasPages())) {
        echo $facts->appends(Input::all())->render();
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

    echo ' | <a href="'.\App\Libs\Utils::url('export', 'csv').'">'._('Export as CSV').'</a>';

    if ($user->admin) {
        echo ' | <a href="'.url('/dump-sql').'">'._('Dump SQL').'</a>';
    }

    echo '</p>';
    ?>
</div>

<script>
var HOUR = <?php echo $user->store_hours ? 'true' : 'false'; ?>;
</script>
@stop