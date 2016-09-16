@extends('web.layouts.dashboard')
@section('content')
    <div class="jumbotron">
        <h1><?php echo _('Error 401 - Unauthorized'); ?></h1>

        <p>
            <?php echo _('Sorry but you have not authorization to view this site'); ?>
        </p>
    </div>
@stop