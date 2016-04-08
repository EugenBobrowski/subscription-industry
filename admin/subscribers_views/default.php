<?php
/**
 * @vars subscribers
 */



if (!isset($suscribers)) $suscribers = array();




$link_sort_by_name = htmlspecialchars(add_query_arg(array(
    'orderby'=>'name',
    'order'=> ($orderby == 'name' && $order == 'asc') ? 'desc' : 'asc',
), false));
$link_sort_by_email = htmlspecialchars(add_query_arg(array(
    'orderby'=>'email',
    'order'=> ($orderby == 'email' && $order == 'asc') ? 'desc' : 'asc',
), false));
$link_sort_by_status = htmlspecialchars(add_query_arg(array(
    'orderby'=>'status',
    'order'=> ($orderby == 'status' && $order == 'asc') ? 'desc' : 'asc',
), false));
$link_sort_by_lastsend = htmlspecialchars(add_query_arg(array(
    'orderby'=>'last_send',
    'order'=> ($orderby == 'last_send' && $order == 'asc') ? 'desc' : 'asc',
), false));

?>

<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?><a href="#" class="page-title-action">Add New</a></h2>

    <form method="get">

        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text"
                                                                                for="cb-select-all-1">Select All</label><input
                        id="cb-select-all-1" type="checkbox"></td>
                <th id="name" class="manage-column column-name sortable
                <?php echo  ($orderby == 'name') ? 'sorted' : 'sortable'?>
                <?php echo  !($orderby == 'name' && $order == 'asc') ? 'desc' : 'asc'?>"><a
                        href="<?php echo $link_sort_by_name; ?>"><span>Name</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" id="email" class="manage-column column-email sortable
                <?php echo  ($orderby == 'email') ? 'sorted' : 'sortable'?>
                <?php echo  !($orderby == 'email' && $order == 'asc') ? 'desc' : 'asc'?>"><a
                        href="<?php echo $link_sort_by_email; ?>"><span>Email</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" class="manage-column column-status sortable
                <?php echo  ($orderby == 'status') ? 'sorted' : 'sortable'?>
                <?php echo  !($orderby == 'status' && $order == 'asc') ? 'desc' : 'asc'?>" style="width: 10em;"><a
                        href="<?php echo $link_sort_by_status; ?>"><span>Status</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" class="manage-column column-date sortable
                <?php echo  ($orderby == 'last_send') ? 'sorted' : 'sortable'?>
                <?php echo  !($orderby == 'last_send' && $order == 'asc') ? 'desc' : 'asc'?>"><a
                        href="<?php echo $link_sort_by_lastsend; ?>"><span>Last Send</span><span
                            class="sorting-indicator"></span></a></th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:user">

            <?php foreach ($subscribers as $subscriber) { ?>
                <tr id="subscriber-<?php echo $subscriber->id; ?>">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="subscriber_<?php echo $suscriber->id; ?>">
                            Select <?php echo (empty($subscriber->name)) ? $subscriber->email : $subscriber->name; ?>
                        </label>
                        <input type="checkbox" name="subscribers[]" id="subscriber_2" class="subscriber" value="2">
                    </th>
                    <td class="username column-username has-row-actions column-primary" data-colname="Email">
                        <?php echo get_avatar($subscriber->email, 32); ?>
                        <strong>
                            <a href="http://si-subscribtion.loc/wp-admin/user-edit.php?subscriber_id=2&amp;wp_http_referer=%2Fwp-admin%2Fusers.php">
                                <?php echo (empty($subscriber->name)) ? $subscriber->email : $subscriber->name; ?></a>
                        </strong><br>

                        <div class="row-actions"><span class="edit"><a
                                    href="<?php echo htmlspecialchars(add_query_arg(array(
                                        'subscriber' => $subscriber->id,
                                        'action' => 'edit',
                                    ))); ?>">Edit</a> | </span><span
                                class="delete"><a class="submitdelete"
                                                  href="<?php echo htmlspecialchars(add_query_arg(array(
                                                      'subscriber' => $subscriber->id,
                                                      'action' => 'delete',
                                                      '_wpnonce' => wp_create_nonce('delete_subscriber'),
                                                  ))); ?>">Delete</a></span>
                        </div>
                        <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span>
                        </button>
                    </td>
<!--                    <td class="name column-name" data-colname="Name">--><?php //echo $subscriber->name; ?><!--</td>-->
                    <td class="email column-email" ><a href="mailto:<?php echo $subscriber->email; ?>"><?php echo $subscriber->email; ?></a>
                    </td>
                    <td class="status column-status" data-colname="Status" style="width: 10em;">
                        <?php echo (empty($subscriber->activation_key)) ? '<strong style="color: #46b450">Confirmed</strong>' : '<strong style="color: #be3631;">Unconfirmed</strong>'; ?>
                    </td>
                    <td class="last-send column-date" data-colname="Last Send">
                        ----/--/--
                    </td>
                </tr>
            <?php } ?>



            </tbody>

            <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text"
                                                                                for="cb-select-all-1">Select All</label><input
                        id="cb-select-all-1" type="checkbox"></td>
                <th id="name" class="manage-column column-name sortable
                <?php echo  ($orderby == 'name') ? 'sorted' : 'sortable'?>
                <?php echo  !($orderby == 'name' && $order == 'asc') ? 'desc' : 'asc'?>"><a
                        href="<?php echo $link_sort_by_name; ?>"><span>Name</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" id="email" class="manage-column column-email sortable
                <?php echo  ($orderby == 'email') ? 'sorted' : 'sortable'?>
                <?php echo  !($orderby == 'email' && $order == 'asc') ? 'desc' : 'asc'?>"><a
                        href="<?php echo $link_sort_by_email; ?>"><span>Email</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" class="manage-column column-status sortable
                <?php echo  ($orderby == 'status') ? 'sorted' : 'sortable'?>
                <?php echo  !($orderby == 'status' && $order == 'asc') ? 'desc' : 'asc'?>" style="width: 10em;"><a
                        href="<?php echo $link_sort_by_status; ?>"><span>Status</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" class="manage-column column-date sortable
                <?php echo  ($orderby == 'last_send') ? 'sorted' : 'sortable'?>
                <?php echo  !($orderby == 'last_send' && $order == 'asc') ? 'desc' : 'asc'?>"><a
                        href="<?php echo $link_sort_by_lastsend; ?>"><span>Last Send</span><span
                            class="sorting-indicator"></span></a></th>

            </tr>
            </tfoot>

        </table>

    </form>
</div>
