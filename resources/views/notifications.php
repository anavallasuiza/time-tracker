<?php if ($notifications->isEmpty()) { ?>
<p>There are no notifications.</p>
<?php } ?>

<?php foreach ($notifications as $notification) { ?>
<div class="alert alert-warning" role="alert">
    <form method="post" action="<?php echo action('Home@notificationRead', ['id' => $notification->id]); ?>">
        <?php echo Form::token(); ?>
        <button type="submit" class="close" aria-label="Mark as read"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
    </form>

    <strong>[<?php echo $notification->created_at ?>] <?php echo $notification->title ?></strong> <?php echo $notification->description ?>
</div>
<?php } ?>