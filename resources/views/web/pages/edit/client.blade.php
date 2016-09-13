@extends('web.layouts.base')
@section('content')
    <form method="post" action="{{$action}}">
    <?php echo Form::token(); ?>
    <?php echo $form['id']; ?>

    <h1><?php echo sprintf($formHeader); ?></h1>

    <div class="row">
        <div class="col-sm-4 col-xs-6">
            <?php echo $form['name']; ?>
        </div>
    </div>

    <div class="form-group text-center">
        <a href="<?php echo url(route('v2.edit.index')); ?>" class="btn btn-info">
            <i class="fa fa-undo"></i>
            <?php echo _('Back'); ?>
        </a>

        <button type="submit" name="action"  class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?php echo _('Save'); ?>
        </button>
    </div>
</form>

<?php if(isset($clientActivities)):?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title"><?php echo _('Activities'); ?></h2>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-xs-12">

    <table class="table table-hover facts-table">
        <thead>
        <tr>
            <th class="column-activity"><?php echo _('Activity'); ?></th>
            <th><?php echo _('Status'); ?></th>
            <th><?php echo _('Created at'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($clientActivities as $activity): ?>
        <tr>
            <td>
                <a href="<?php echo url(route('activity.edit',['id'=>$activity->id]))?>"><?php echo $activity->name; ?></a>
            </td>
            <td>
                <?php if(!$activity->isArchived()):?>
                <span class="label label-success"><?php echo _('Active')?></span>
                <?php else:?>
                <span class="label label-default"><?php echo _('Archived')?></span>
                <?php endif;?>

            </td>
            <td><?php echo $activity->created_at->format($user->dateFormat);  ?></td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>

<?php endif; ?>
@stop