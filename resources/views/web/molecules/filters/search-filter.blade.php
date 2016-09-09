<form method="get">
    <div class="row">
        <div class="col-sm-2 form-group">
            <select name="user" class="form-control filter">
                <option value=""><?php echo _('All Users'); ?></option>
                <?php foreach ($users as $user) { ?>
                <option value="<?php echo $user->id; ?>" <?php echo ($filters['user'] == $user->id) ? 'selected' : ''; ?>><?php echo $user->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-4 form-group">
            <select name="activity" class="form-control filter">
                <option value=""><?php echo _('All Projects'); ?></option>
                <?php foreach ($activities as $activity) { ?>
                <option value="<?php echo $activity->id; ?>" <?php echo ($filters['activity'] == $activity->id) ? 'selected' : ''; ?>><?php echo $activity->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-3 form-group">
            <select name="tag" class="form-control filter">
                <option value=""><?php echo _('All Tags'); ?></option>
                <?php foreach ($tags as $tag) { ?>
                <option value="<?php echo $tag->id; ?>" <?php echo ($filters['tag'] == $tag->id) ? 'selected' : ''; ?>><?php echo $tag->name; ?></option>
                <?php } ?>
            </select>
        </div>
        <?php if(isset($clients)):?>
        <div class="col-sm-3 form-group">
            <select name="client" class="form-control filter">
                <option value=""><?php echo _('All clients'); ?></option>
                <option value="-1" <?php echo ($filters['client'] == -1) ? 'selected' : ''; ?>><?php echo _('No clients'); ?></option>
                <?php foreach ($clients as $client) { ?>
                <option value="<?php echo $client->id; ?>" <?php echo ($filters['client'] == $client->id) ? 'selected' : ''; ?>><?php echo $client->name; ?></option>
                <?php } ?>
            </select>
        </div>
        <?php endif;?>
    </div>


    <div class="row">

        <div class="col-sm-9 form-group">
            <input type="search" name="description" value="<?php echo $filters['description']; ?>" class="form-control filter" placeholder="<?php echo _('Search in description'); ?>">
        </div>

        <div class="col-sm-3 form-group">
            <div class="input-daterange input-group">
                <input type="text" class="form-control filter" name="first" value="<?php echo $filters['first'] ? $filters['first']->format('d/m/Y') : ''; ?>" placeholder="<?php echo _('Start date'); ?>" />
                <span class="input-group-addon"><?php echo _('to'); ?></span>
                <input type="text" class="form-control filter" name="last" value="<?php echo $filters['last'] ? $filters['last']->format('d/m/Y') : ''; ?>" placeholder="<?php echo _('End date'); ?>" />
            </div>
        </div>
    </div>
    <?php if (isset($sort)) { ?>
    <input type="hidden" name="sort" value="<?php echo $sort; ?>" />
    <?php } ?>
</form>

