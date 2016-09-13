@extends('web.layouts.dashboard')
@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><?php echo _('Notifications'); ?></h2>
        </div>
    </div>
    @if($notifications->isEmpty())
        <div class="well">
            {{ _('There are not notifications') }}
        </div>
    @endif

    @foreach ($notifications as $notification)
        <div class="alert alert-warning" role="alert">
            <form method="post" action="{{url(route('v2.notifications.read',['id'=>$notification->id]))}}">
                <?php echo Form::token(); ?>
                <button type="submit" class="close" aria-label="Mark as read"><span class="glyphicon glyphicon-ok"
                                                                                    aria-hidden="true"></span></button>
            </form>

            <strong>[{{$notification->created_at}}] {{$notification->title}}</strong> {{$notification->description}}
        </div>
    @endforeach
@stop