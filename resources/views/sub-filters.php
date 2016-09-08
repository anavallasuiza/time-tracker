<form method="get">
    <div class="row">
        <div class="col-sm-2 form-group">
            <select name="user" class="form-control filter">
                <option value=""><?= _('All Users'); ?></option>
                <?php foreach ($users as $user) { ?>
                    <option value="<?= $user->id; ?>" <?= ($filters['user'] == $user->id) ? 'selected' : ''; ?>><?= $user->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-4 form-group">
            <select name="activity" class="form-control filter">
                <option value=""><?= _('All Projects'); ?></option>
                <?php foreach ($activities as $activity) { ?>
                    <option value="<?= $activity->id; ?>" <?= ($filters['activity'] == $activity->id) ? 'selected' : ''; ?>><?= $activity->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-3 form-group">
            <select name="tag" class="form-control filter">
                <option value=""><?= _('All Tags'); ?></option>
                <?php foreach ($tags as $tag) { ?>
                    <option value="<?= $tag->id; ?>" <?= ($filters['tag'] == $tag->id) ? 'selected' : ''; ?>><?= $tag->name; ?></option>
                <?php } ?>
            </select>
        </div>
        <?php if(isset($clients)):?>
        <div class="col-sm-3 form-group">
            <select name="client" class="form-control filter">
                <option value=""><?= _('All clients'); ?></option>
                <option value="-1" <?php echo ($filters['client'] == -1) ? 'selected' : ''; ?>><?= _('No clients'); ?></option>
                <?php foreach ($clients as $client) { ?>
                    <option value="<?= $client->id; ?>" <?= ($filters['client'] == $client->id) ? 'selected' : ''; ?>><?= $client->name; ?></option>
                <?php } ?>
            </select>
        </div>
        <?php endif;?>
    </div>


    <div class="row">

    <div class="col-sm-9 form-group">
        <input type="search" name="description" value="<?= $filters['description']; ?>" class="form-control filter" placeholder="<?= _('Search in description'); ?>">
    </div>

    <div class="col-sm-3 form-group">
        <div class="input-daterange input-group">
            <input type="text" class="form-control filter" name="first" value="<?= $filters['first'] ? $filters['first']->format('d/m/Y') : ''; ?>" placeholder="<?= _('Start date'); ?>" />
            <span class="input-group-addon"><?= _('to'); ?></span>
            <input type="text" class="form-control filter" name="last" value="<?= $filters['last'] ? $filters['last']->format('d/m/Y') : ''; ?>" placeholder="<?= _('End date'); ?>" />
        </div>
    </div>
    </div>
        <?php if (isset($sort)) { ?>
            <input type="hidden" name="sort" value="<?= $sort; ?>" />
        <?php } ?>
</form>
