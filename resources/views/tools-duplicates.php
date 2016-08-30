<form class="text-center well submit-wait" data-message="<?= _('Please wait...'); ?>" method="post">
    <a href="<?= url('/'); ?>" class="btn btn-info"><?= _('Back'); ?></a>

    <?php if ($facts) { ?>
    <button type="submit" name="action" value="toolsDuplicates" class="btn btn-success">
        <i class="glyphicon glyphicon-trash"></i>
        <?= _('Clean'); ?>
    </button>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center"><?= _('Delete'); ?></th>
                <th class="text-center"><?= _('Start'); ?></th>
                <th class="text-center"><?= _('End'); ?></th>
                <th class="text-center"><?= _('Time'); ?></th>
                <th class="text-center"><?= _('User'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($facts as $duplicates) { ?>
            <?php foreach ($duplicates as $fact) { ?>
            <tr class="text-center">
                <td><input type="checkbox" name="checked[]" value="<?= $fact->id; ?>" <?= $fact->checked ? 'checked' : ''; ?> /></td>
                <td><?= $fact->start_time->format('d-m-Y H:i:s'); ?></td>
                <td><?= $fact->end_time->format('d-m-Y H:i:s'); ?></td>
                <td><?= $fact->total_time; ?></td>
                <td><?= $fact->users->name; ?></td>
            </tr>
            <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</form>

<?php if ($action) { ?>
<pre><code><?= $response; ?></code></pre>
<?php } ?>