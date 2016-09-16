@extends('web.layouts.dashboard')
<h1></h1>
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><?php echo _('Sync database'); ?></h2>
        </div>
    </div>

    @if(!$synEnabled)
        <div class="well">
            {{ _('Synchronization script not configured') }}
        </div>
    @else
        <form class="text-center well submit-wait" data-message="<?php echo _('Please wait...'); ?>" method="post">
            <?php echo Form::token(); ?>
            <a href="<?php echo url('/'); ?>" class="btn btn-info"><?php echo _('Back'); ?></a>

            <button type="submit" name="action" value="sync" class="btn btn-success">
                <i class="glyphicon glyphicon-refresh"></i>
                <?php echo _('Syncronize database'); ?>
            </button>
        </form>
        @if(isset($action))
            @foreach ($response as $name => $log)
                <h2><?php echo $name; ?></h2>
                @foreach ($log as $row)
                    <div class="alert alert-<?php echo $row['status']; ?>">
                        <?php echo $row['message']; ?>
                    </div>
                @endforeach

            @endforeach

        @endif
    @endif
@stop