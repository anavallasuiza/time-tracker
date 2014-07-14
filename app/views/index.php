<table class="table table-hover">
    <thead>
        <tr>
            <th><?= _('User'); ?></th>
            <th><?= _('Activity'); ?></th>
            <th><?= _('Start time'); ?></th>
            <th><?= _('End time'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($facts as $fact) { ?>
        <tr>
            <td><?= $fact->users->name; ?></td>
            <td><?= $fact->activities->name; ?></td>
            <td><?= $fact->start_time; ?></td>
            <td><?= $fact->end_time; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>