<form class="text-center well submit-wait" data-message="<?php echo _('Please wait...'); ?>" method="post">
    <a href="<?php echo url('/'); ?>" class="btn btn-info"><?php echo _('Back'); ?></a>

    <?php if ($facts) { ?>
    <button type="submit" name="action" value="toolsDuplicates" class="btn btn-success">
        <i class="glyphicon glyphicon-trash"></i>
        <?php echo _('Clean'); ?>
    </button>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center"><?php echo _('Delete'); ?></th>
                <th class="text-center"><?php echo _('Start'); ?></th>
                <th class="text-center"><?php echo _('End'); ?></th>
                <th class="text-center"><?php echo _('Time'); ?></th>
                <th class="text-center"><?php echo _('User'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($facts as $duplicates) { ?>
            <?php foreach ($duplicates as $fact) { ?>
            <tr class="text-center">
                <td><input type="checkbox" name="checked[]" value="<?php echo $fact->id; ?>" <?php echo $fact->checked ? 'checked' : ''; ?> /></td>
                <td><?php echo $fact->start_time->format('d-m-Y H:i:s'); ?></td>
                <td><?php echo $fact->end_time->format('d-m-Y H:i:s'); ?></td>
                <td><?php echo $fact->total_time; ?></td>
                <td><?php echo $fact->users->name; ?></td>
            </tr>
            <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</form>

<?php if ($action) { ?>
<pre><code><?php echo $response; ?></code></pre>
<?php } ?>