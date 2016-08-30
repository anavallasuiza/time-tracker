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
        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker3.css" />
        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.0.2/css/bootstrap3/bootstrap-switch.min.css" />
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Press+Start+2P" />
        <link rel="stylesheet" type="text/css" href="<?= asset('css/styles.css'); ?>" />
    </head>

    <body>
        <div class="page-header">
            <div class="container">
                <?= View::make('sub-header')->render(); ?>
            </div>
        </div>

        <?php if ($flash = Session::get('flash-message')) { ?>
        <div class="container center">
            <div class="alert alert-<?= $flash['status']; ?>">
                <?= $flash['message']; ?>
                <?php Session::forget('flash-message'); ?>
            </div>
        </div>
        <?php } ?>

        <div class="container">
            <?= $body; ?>
        </div>

        <footer id="footer" class="text-center">
            <div class="container">
                <div class="well">
                    <?php if ($user) { ?>

                    <a href="<?= url('/stats'); ?>" class="label label-default"><?= _('Stats'); ?></a>
                    <a href="<?= url('/stats/calendar'); ?>" class="label label-default"><?= _('Calendar'); ?></a>
                    <a href="<?= url('/edit'); ?>" class="label label-default"><?= _('Edit'); ?></a>
                    <a href="<?= url('/sync'); ?>" class="label label-default"><?= _('Sync'); ?></a>

                    <?php if ($user->admin) { ?>
                    <a href="<?= url('/git-update'); ?>" class="label label-default"><?= _('Update environment'); ?></a>
                    <?php } ?>

                    <?php } ?>

                    <p><a href="https://github.com/anavallasuiza/time-tracker" class="text-muted">
                        <i class="fa fa-github"></i>
                        <?= _('View on Github'); ?>
                    </a></p>
                </div>
            </div>
        </footer>

        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="///cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/js/bootstrap-datepicker.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/floatthead/1.2.8/jquery.floatThead.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.7.0/moment.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.0.2/js/bootstrap-switch.min.js"></script>
        <script src="//igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>

        <script>
        var BASE_WWW = '<?= URL::to('/'); ?>';
        </script>

        <script src="<?= asset('js/scripts.js'); ?>" type="text/javascript"></script>
    </body>
</html>
