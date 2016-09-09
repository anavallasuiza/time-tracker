@extends('web.layouts.base')
@section('content')
    <div class="jumbotron">
        <h1><?php echo _('Error 404 - Not Found'); ?></h1>

        <p>
            <?php echo _('Sorry. The page you are looking for does not exist.'); ?>
        </p>
        <p>
            <?php echo _('We may have removed a page to which you found a link, or you may have the wrong address for the page you are looking for');?>
        </p>
    </div>
@stop