<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 7/6/16
 * Time: 1:35 PM
 */
class Newsletter_Table
{
    private $version;
    protected static $instance;
    public $colms;

    private function __construct($version)
    {
        $this->version = $version;
        add_filter('manage_newsletters_posts_columns', array($this, 'add_columns'));
        add_filter('manage_newsletters_posts_custom_column', array($this, 'column_content'), 10, 2);
        add_action('send_newsletter', array($this, 'cron_send'));
    }

    public function add_columns($columns)
    {
        $end = array_splice($columns, 2);
        $columns = array_merge($columns, array('subscribers' => __('Subscribers & groups'), 'jobs' => __('Jobs')), $end);
        if (isset($columns['language'])) unset($columns['language']);
        $this->colms = $columns;

        return $columns;
    }

    public function column_content($column, $post_id)
    {
        switch ($column) {
            case 'subscribers':
                $groups = wp_get_post_terms($post_id, 'newsletter_groups');
                echo '<em>Groups:</em> ';
                if (!empty($groups)) {

                    $groups_links = array();
                    foreach ($groups as $group) {
                        $link = add_query_arg(array('newsletter_groups' => $group->slug));
                        $groups_links[] = '<a href="' . $link . '">' . $group->name . '</a>';
                    }
                    echo implode(', ', $groups_links);
                }
                $single_receivers = get_post_meta($post_id, 'single_receivers', true);
                if (!empty($single_receivers)) {
                    echo '<br /><em>And:</em> ';

                    $model = Subscribers_Model::get_instance();

                    $singles = $model->get_subscribers(array(
                        'select' => array('name', 'email'),
                        'where' => array(
                            'field' => 'id',
                            'compare' => 'IN',
                            'value' => '(' . implode(', ', $single_receivers) . ')',
                        ),

                    ));
                    $single_pices = array();
                    foreach ($singles as $single) {
                        $name = (empty($single->name)) ? $single->email : $single->name;
                        $single_pices[] = '<a href="mailto:' . $single->email . '">' . $name . '</a>';
                    }
                    echo implode(', ', $single_pices);
                }

                break;
            case 'jobs':
                echo 'job';
                break;

        }
    }
    function cron_send($args) {
        file_put_contents(ABSPATH . '/cron.txt', implode('/', $args));
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function scripts()
    {
        ?>

        <?php
    }


    public static function get_instance($version)
    {
        if (null === self::$instance) {
            self::$instance = new self($version);
        }
        return self::$instance;
    }
}