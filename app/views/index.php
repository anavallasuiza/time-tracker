
<div class="row">
    <div class="col-sm-6">
        <select name="user" class="form-control" onchange="window.location = '?user=' + this.value;">
            <option value=""><?= _('All'); ?></option>
            <?php foreach ($users as $user) { ?>
            <option value="<?= $user->id; ?>" <?= ($filter['user'] == $user->id) ? 'selected' : ''; ?>><?= $user->name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-sm-6">
        <select name="user" class="form-control" onchange="window.location = '?activity=' + this.value;">
            <option value=""><?= _('All'); ?></option>
            <?php foreach ($activities as $activity) { ?>
            <option value="<?= $activity->id; ?>" <?= ($filter['activity'] == $activity->id) ? 'selected' : ''; ?>><?= $activity->name; ?></option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="row">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= _('User'); ?></th>
                <th><?= _('Activity'); ?></th>
                <th><?= _('Start time'); ?></th>
                <th><?= _('End time'); ?></th>
                <th><?= _('Total time'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($facts as $fact) { ?>
            <tr>
                <td><?= $fact->users->name; ?></td>
                <td><?= $fact->activities->name; ?></td>
                <td><?= $fact->start_time->format('d/m/Y H:i'); ?></td>
                <td><?= $fact->end_time->format('d/m/Y H:i'); ?></td>
                <td><?= $fact->start_time->diff($fact->end_time)->format('%H:%I'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>