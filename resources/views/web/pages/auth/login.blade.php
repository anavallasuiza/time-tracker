@extends('web.layouts.master')
@section('content')
    <div class="row">
    <div class="col-sm-offset-3 col-sm-6">
        <h1 class="text-center">{{_('Login')}}</h1>
        <form method="post" action="{{ URL::current() }}">
            {{csrf_field()}}
            <div class="form-group {{$errors->has('email')?'has-error':'' }}">
                <label id="label-email" for="email" class="form-label">{{ _('Email') }}</label>
                <input id="email" class="form-control" required type="email" name="email" value="{{old('email')}}">
                @if($errors->has('email'))
                    <span class="help-block">
                        {{ $errors->first('email') }}
                    </span>
                @endif
            </div>

            <div class="form-group  {{$errors->has('password')?'has-error':'' }}">
                <label id="label-password" for="password" class="form-label">{{ _('Password') }}</label>
                <input id="password" class="form-control" required type="password" name="password" >
                @if($errors->has('password'))
                    <span class="help-block">
                        {{ $errors->first('password') }}
                    </span>
                @endif
            </div>

            <div class="form-group clearfix">
                <button type="submit" class="btn btn-danger btn-lg pull-right" name="action" value="login">
                    <?php echo _('Login'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
@stop