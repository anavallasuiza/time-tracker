@extends('web.layouts.dashboard')
@section('content')
    @include('web.molecules.filters.search-filter', [
        'filters' => $filters
    ])
<div class="panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title"><?php echo _('Total hours worked every day'); ?></h2>
    </div>
</div>

<table class="table calendar">
    <?php foreach ($calendar as $week => $days) { ?>
    <tr>
        <?php foreach ($days as $hours) { ?>
        <th>
            <?php echo date('D j M', $hours['time']); ?>
        </th>
        <?php } ?>
    </tr>

    <tr>
        <?php foreach ($days as $hours) { ?>
        <td>
            <?php if ($hours['hours'] > 0) { ?>
            <a href="<?php echo url(route('time.index',[
                'user' => $filters['user'],
                'activity' => $filters['activity'],
                'tag' => $filters['tag'],
                'first' => date('d/m/Y', $hours['time']),
                'last' => date('d/m/Y', $hours['time'])
            ])); ?>"><?php echo \App\Libs\Utils::minutes2hour($hours['hours']); ?></a>
            <?php } else { ?>
            <span class="text-muted">0</span>
            <?php } ?>
        </td>
        <?php } ?>
    </tr>
    <?php } ?>
</table>
@stop
