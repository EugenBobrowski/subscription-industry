<?php
/**
 * @var $subscribers
 */
?>
    <div class="wrap">

    <h2><?php _e('Delete subscriber', 'si'); ?></h2>
    <form method="post">
        <input type="hidden" name="confirm_delete" value="dodelete"/>
        <p>
            You have specified this subscriber for deletion:
        </p>
        <ul>
            <?php foreach ($subscribers as $subscriber) {
                ?>
                <li>
                    <input type="hidden" name="subscribers[]"
                           value="<?php echo $subscriber->id; ?>"><?php echo 'ID #' . $subscriber->id . ': ' . $subscriber->email . ' [' . $subscriber->name . ']'; ?>
                </li>
                <?php
            } ?>

        </ul>

        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                 value="Confirm Deletion"></p>
    </form>

<?php