<nav class="navbar navbar-default navbar-static">
    <div class="navbar-header">
        <a href="<?= url('/'); ?>" class="navbar-brand"><img src="<?= asset('images/logo-50.png'); ?>" alt="<?= _('A Navalla Suíza'); ?>" /></a>
    </div>

    <?php if (Request::is('/')) { ?>
    <div class="collapse navbar-collapse js-navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown dropdown-large">
                <a href="#" id="header-timer" class="dropdown-toggle" data-toggle="dropdown">
                    <h1>00:00</h1>
                    <h2>----</h2>                    
                </a>

                <div class="dropdown-menu dropdown-menu-large">
                    <?= View::make('sub-fact-add')->render(); ?>
                </div>
            </li>
        </ul>
    </div>
    <?php } ?>
</nav>