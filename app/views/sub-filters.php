<form method="get" class="row">
    <?php if (isset($sort)) { ?>
    <input type="hidden" name="sort" value="<?= $sort; ?>" />
    <?php } ?>

    <?php if ($user->admin) { ?>

    <div class="col-sm-2 form-group">
        <select name="user" class="form-control filter">
            <option value=""><?= _('All Users'); ?></option>
            <?php foreach ($users as $user) { ?>
            <option value="<?= $user->id; ?>" <?= ($filters['user'] == $user->id) ? 'selected' : ''; ?>><?= $user->name; ?></option>
            <?php } ?>
        </select>
    </div>

    <?php } else {?>

    <div class="col-sm-2 form-group">
        <input type="text" class="form-control" value="<?= $user->name; ?>" readonly disabled />
    </div>

    <?php } ?>

    <div class="col-sm-3 form-group">
        <select name="activity" class="form-control filter">
            <option value=""><?= _('All Projects'); ?></option>
            <?php foreach ($activities as $activity) { ?>
            <option value="<?= $activity->id; ?>" <?= ($filters['activity'] == $activity->id) ? 'selected' : ''; ?>><?= $activity->name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-sm-2 form-group">
        <select name="tag" class="form-control filter">
            <option value=""><?= _('All Tags'); ?></option>
            <?php foreach ($tags as $tag) { ?>
            <option value="<?= $tag->id; ?>" <?= ($filters['tag'] == $tag->id) ? 'selected' : ''; ?>><?= $tag->name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-sm-2 form-group">
        <input type="search" name="description" value="<?= $filters['description']; ?>" class="form-control filter" placeholder="<?= _('Search in description'); ?>">
    </div>

    <div class="col-sm-3 form-group">
        <div class="input-daterange input-group">
            <input type="text" class="form-control filter" name="first" value="<?= $filters['first'] ? $filters['first']->format('d/m/Y') : ''; ?>" placeholder="<?= _('Start date'); ?>" />
            <span class="input-group-addon"><?= _('to'); ?></span>
            <input type="text" class="form-control filter" name="last" value="<?= $filters['last'] ? $filters['last']->format('d/m/Y') : ''; ?>" placeholder="<?= _('End date'); ?>" />
        </div>
    </div>
</form>
