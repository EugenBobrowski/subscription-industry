<?php
/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 23.06.16
 * Time: 17:51
 */ ?>

    <div class="wrap">


        <h2><?php _e('Import Subscribers', 'si'); ?>
            <a href="<?php echo admin_url('users.php?page=subscribers'); ?>"
               class="page-title-action">Subscribers</a>
            <span class="others-parts" style="float: right; margin-right: -15px;">
            <a href="<?php echo admin_url('edit.php?post_type=newsletters'); ?>"
               class="page-title-action">Newsletters</a>
            <a href="<?php echo admin_url('options-general.php?page=si-options'); ?>"
               class="page-title-action">Options</a>
        </span>
        </h2>
        <form action="post" id="import_form">
            <table id="import_table" class="wp-list-table widefat fixed striped subscribers">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">Select All</label><input
                            id="cb-select-all-1" type="checkbox"></td>
                    <th id="name" class="manage-column column-name
                                desc">Name
                    </th>
                    <th scope="col" id="email" class="manage-column column-email
                                desc">Email
                    </th>
                    <th scope="col" class="manage-column column-group">Group</th>
                    <th scope="col" class="manage-column column-status
                                desc" style="width: 10em;">Status
                    </th>
                    <th scope="col" class="manage-column column-date
                                desc">Last Send
                    </th>
                </tr>
                </thead>

                <tbody id="the-list" data-wp-lists="list:user">

                <tr class="empty-line">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text">
                            Select </label>
                        <input type="checkbox" name="subscribers[]" class="subscriber" disabled="disabled" value="">
                    </th>
                    <td class="name column-username has-row-actions column-primary" data-colname="Email">
                    </td>
                    <td class="email column-email">
                    </td>
                    <td class="group column-group">
                    </td>
                    <td class="status column-status" data-colname="Status" style="width: 10em;">
                    </td>
                    <td class="last-send column-date" data-colname="Last Send">
                    </td>
                </tr>

                </tbody>

                <tfoot>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">Select All</label><input
                            id="cb-select-all-1" type="checkbox"></td>
                    <th id="name" class="manage-column column-name
                                desc">Name
                    </th>
                    <th scope="col" id="email" class="manage-column column-email
                                desc">Email
                    </th>
                    <th scope="col" class="manage-column column-group">Group</th>
                    <th scope="col" class="manage-column column-status
                                desc" style="width: 10em;">Status
                    </th>
                    <th scope="col" class="manage-column column-date
                                desc">Last Send
                    </th>

                </tr>
                </tfoot>

            </table>
            <input type="hidden" name="action" value="doedit"/>
            <table class="form-table atf-fields">

                <tr class="form-required">
                    <th scope="row"><label for="import_csv"><?php _e('Import text'); ?></label></th>
                    <td><?php AtfHtmlHelper::upload(array(
                            'id' => 'import_csv',
                            'name' => 'import_csv',
                            'multiple' => true,
                            'label' => __('Choose CSV'),
                            'accept' => 'text/csv',
                        )); ?>
                        <p class="desc">Avaliable fields: </p>
                    </td>
                </tr>
                <tr class="form-required">
                    <th scope="row"><label for="name"><?php _e('Import text'); ?></label></th>
                    <td><?php AtfHtmlHelper::textarea(array(
                            'id' => 'import_text',
                            'name' => 'import_text',
                            'rows' => 3,
                            'value' => '',
                            'desc' => __('', 'si'),
                        )); ?></td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row"><label>Set uncofirmed for all importing emails</label></th>
                    <td><?php AtfHtmlHelper::tumbler(array('id' => 'set_unconfirm', 'name' => 'set_unconfirm', 'value' => 1)); ?></td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row"><label>Send confirmations to all unconfirmed</label></th>
                    <td><?php AtfHtmlHelper::tumbler(array('id' => 'confirm', 'name' => 'confirm', 'value' => 1)); ?></td>
                </tr>
            </table>


            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                     value="Submit"></p>
        </form>
    </div>


<?php
