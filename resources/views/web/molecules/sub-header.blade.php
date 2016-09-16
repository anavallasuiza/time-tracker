<nav class="navbar navbar-default navbar-static">
    <div class="navbar-header">
        <a href="<?php echo url('/'); ?>" class="navbar-brand"><img src="<?php echo asset('images/logo-50.png'); ?>" alt="<?php echo _('A Navalla Suíza'); ?>" /></a>
        <?php if (! $unreadNotifications->isEmpty()) { ?>
        <a href="<?php echo url(route('notifications.index')); ?>" class="btn-notifications" aria-label="Notifications">
            <span class="glyphicon glyphicon-exclamation-sign text-warning" aria-hidden="true"></span>
        </a>
        <?php } ?>
    </div>
    <div class="collapse navbar-collapse js-navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown dropdown-large">
                <a href="#" id="header-timer" class="dropdown-toggle" data-toggle="dropdown">
                    <h1>00:00</h1>
                    <h2>----</h2>
                </a>

                <div class="dropdown-menu dropdown-menu-large">
                    @include('web.molecules.facts.add')
                </div>
            </li>
        </ul>
    </div>

</nav>