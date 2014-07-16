<!doctype html>

<html lang="en">
    <head>
        <meta charset="UTF-8">

        <title><?= _('ANS Time Tracker'); ?></title>

        <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />

        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/united/bootstrap.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/lumen/bootstrap.min.css" />
        <link rel="stylesheet" href="//eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/css/datepicker3.css" />
        <link rel="stylesheet" href="<?= asset('css/styles.css'); ?>" />
    </head>

    <body>
        <div class="page-header">
            <div class="container">
                <a href="<?= url('/'); ?>" class="pull-left"><img src="<?= asset('images/logo.png'); ?>" alt="<?= _('A Navalla Suíza'); ?>" /></a>
                <h1 class="pull-left"><?= _('Time Tracker'); ?></h1>
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
        <script src="<?= asset('js/scripts.js'); ?>" type="text/javascript"></script>
    </body>
</html>
