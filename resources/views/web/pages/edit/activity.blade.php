@extends('web.layouts.dashboard')
@section('content')
    <form method="post">
    <?php echo Form::token(); ?>
    <?php echo $form['id']; ?>

        <h1><?php echo sprintf($formHeader); ?></h1>
    <div class="alert alert-warning">
        <?php echo sprintf(_('Take care with this activity. There are users using automated tools based in Basecamp projects names. Name used to this activity must be exactly as Basecamp project name or duplicated projects will be created.')); ?>
    </div>

    <div class="row">
        <div class="col-lg-8 col-sm-6 col-xs-12">
            <?php echo $form['name']; ?>
        </div>

        <div class="col-lg-2 col-sm-3 col-xs-6">
            <?php echo $form['archived']; ?>
        </div>

        <div class="col-lg-2 col-sm-3 col-xs-6">
            <?php echo $form['total_hours']; ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><?php echo _('Client'); ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
            <?php echo $form['id_clients']; ?>
        </div>

    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><?php echo _('Times by tag'); ?></h2>
        </div>
    </div>

    <div class="row">
        <?php foreach ($activityTags as $tag) {
            ?>
        <div class="col-lg-2 col-md-3 col-xs-8">
            <div class="form-group">
                <label for="tag-<?php echo $tag->id; ?>"><?php echo $tag->name; ?></label>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-xs-4">
            <div class="form-group">
                <input type="text" class="form-control" name="tags[<?php echo $tag->id; ?>]" value="<?php echo isset($tag->estimations[0]) ? $tag->estimations[0]->hours : ''; ?>" placeholder="<?php echo _('Hours'); ?>">
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="form-group text-center">
        <a href="<?php echo url(route('edit.index')); ?>" class="btn btn-info">
            <i class="fa fa-undo"></i>
            <?php echo _('Back'); ?>
        </a>

        <button type="submit" name="action"  class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?php echo _('Save'); ?>
        </button>
    </div>
</form>
@stop