<!doctype html>

<html lang="en">
    <head>
        <meta charset="UTF-8">

        <title><?= _('ANS Time Tracker'); ?></title>

        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes">

        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>

        <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />

        <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/united/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/lumen/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="//eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/css/datepicker3.css" />
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Press+Start+2P" />
        <link rel="stylesheet" type="text/css" href="<?= asset('css/styles.css'); ?>" />
    </head>

    <body>
        <div class="page-header">
            <div class="container">
                <?= View::make('sub-header')->render(); ?>
            </div>
        </div>

        <?php if ($flash = Session::get('flash-message')) { ?>
        <div class="alert alert-<?= $flash['status']; ?>">
            <div class="container center">
                <?= $flash['message']; ?>
            </div>
        </div>
        <?php } ?>

        <div class="container">
            <?= $body; ?>
        </div>

        <footer class="text-center panel-body">
            <div class="container">
                <a href="https://github.com/anavallasuiza/time-tracker" class="text-muted"><?= _('View on Github'); ?></a>
            </div>
        </footer>

        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="//eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/floatthead/1.2.8/jquery.floatThead.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.7.0/moment.min.js"></script>
        <script src="//igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>

        <script>
        var BASE_WWW = '<?= URL::to('/'); ?>';
        </script>

        <script src="<?= asset('js/scripts.js'); ?>" type="text/javascript"></script>
    </body>
</html>
