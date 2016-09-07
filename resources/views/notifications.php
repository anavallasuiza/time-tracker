<?php if ($notifications->isEmpty()) { ?>
<p>There are no notifications.</p>
<?php } ?>

<?php foreach ($notifications as $notification) { ?>
<div class="alert alert-warning" role="alert">
    <form method="post" action="<?= action('Home@notificationRead', ['id' => $notification->id]); ?>">
        <?= Form::token(); ?>
        <button type="submit" class="close" aria-label="Mark as read"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
    </form>

    <strong>[<?= $notification->created_at ?>] <?= $notification->title ?></strong> <?= $notification->description ?>
</div>
<?php } ?>