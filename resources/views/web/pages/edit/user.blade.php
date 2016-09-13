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

        <div class="col-sm-4 col-xs-6">
            <?php echo $form['user']; ?>
        </div>

        <div class="col-sm-4 col-xs-12">
            <?php echo $form['email']; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <?php echo $form['password']; ?>
        </div>

        <div class="col-xs-6">
            <?php echo $form['password_confirmation']; ?>
        </div>
    </div>

    <?php echo $form['api_key']; ?>

    <div class="row">
        <div class="col-xs-6">
            <?php echo $form['store_hours']; ?>
        </div>

        <?php if (isset($form['enabled'])) { ?>
        <div class="col-xs-6">
            <?php echo $form['enabled']; ?>
        </div>
        <?php } ?>
    </div>

    <div class="form-group text-center">
        <a href="<?php echo url(route('v2.edit.index')); ?>" class="btn btn-info">
            <i class="fa fa-undo"></i>
            <?php echo _('Back'); ?>
        </a>

        <button type="submit" name="action" class="btn btn-success">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <?php echo _('Save'); ?>
        </button>
    </div>
</form>
@stop