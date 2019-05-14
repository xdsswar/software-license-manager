<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class SLM_List_Licenses extends WP_List_Table {

    function __construct()
    {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular'  => 'item',     //singular name of the listed records
            'plural'    => 'items',    //plural name of the listed records
            'ajax'      => false       //does this table support ajax?
        ));
    }

    public function no_items()
    {
        _e('No licenses avaliable.', 'slm');
    }

    function get_columns()
    {
        $columns = array(
            'cb'                    => '<input type="checkbox" />', //Render a checkbox
            'id'                    => 'ID',
            'license_key'           => 'Key',
            'lic_status'            => 'Status',
            'email'                 => 'Email',
            'max_allowed_domains'   => 'Domains',
            'max_allowed_devices'   => 'Devices',
            'purchase_id_'          => 'Purchase #',
            'date_created'          => 'Created on',
            'date_renewed'          => 'Date Renewed',
            'date_activated'        => 'Date activated',
            'date_expiry'           => 'Expiration',
            'until'                 => 'Until Ver.',
        );
        return $columns;
    }

    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    function column_id($item)
    {
        $row_id = $item['id'];
        $actions = array(
            'edit'      => sprintf('<a class="left" href="admin.php?page=slm_manage_license&edit_record=%s">Edit</a>', $row_id),
            'delete'    => sprintf('<a href="admin.php?page=slm_overview&action=delete_license&id=%s" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a>', $row_id),
        );
        return sprintf(
            ' <span style="color:black"> %1$s </span>%2$s',
            /*$1%s*/
            $item['id'],
            /*$2%s*/
            $this->row_actions($actions)
        );
    }




    function column_active($item)
    {
        if ($item['active'] == 1) {
            return 'active';
        } else {
            return 'inactive';
        }
    }




    function column_cb($item)
    {

        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            $this->_args['singular'],  //Let's simply repurpose the table's singular label
            /*$2%s*/
            $item['id']                //The value of the checkbox should be the record's id
        );
    }


    function get_sortable_columns()
    {
        // $sortable_columns = array(
        //     'id'            => array('id', false),
        //     'license_key'   => array('license_key', false),
        //     'lic_status'    => array('lic_status', false),
        //     'purchase_id_'  => array('purchase_id_', false),
        //     'until'         => array('until', false),
        //     'email'         => array('email', false),
        //     'date_created'  => array('date_created', false),
        //     'date_renewed'  => array('date_renewed', false),
        //     'date_activated'  => array('date_activated', false),
        //     'date_expiry'   => array('date_expiry', false),
        // );
        $sortable_columns = array(
            'id' => array('id', true),
            'email' => array('email', true),
            'until' => array('until', true),
            'lic_status' => array('lic_status', true)
        );

        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete'    => 'Delete',
            'blocked'   => 'Block',
            'expired'   => 'Expire',
            'active'    => 'Activate',
        );
        return $actions;
    }

    function process_bulk_action()
    {
        if ('delete' === $this->current_action()) {
            //Process delete bulk actions
            if (!isset($_REQUEST['item'])) {
                $error_msg = '<p>' . __('Error - Please select some records using the checkboxes', 'slm') . '</p>';
                echo '<div id="message" class="error fade">' . $error_msg . '</div>';
                return;
            } else {
                $nvp_key            = $this->_args['singular'];
                $records_to_delete  = $_GET[$nvp_key];

                foreach ($records_to_delete as $row) {
                    SLM_Utility::delete_license_key_by_row_id($row);
                }

                echo '<div id="message" class="updated fade"><p>Selected records deleted successfully!</p></div>';
            }
        }
        if ('blocked' === $this->current_action()) {
            //Process blocked bulk actions
            if (!isset($_REQUEST['item'])) {
                $error_msg = '<p>' . __('Error - Please select some records using the checkboxes', 'slm') . '</p>';
                echo '<div id="message" class="error fade">' . $error_msg . '</div>';
                return;
            } else {
                $nvp_key            = $this->_args['singular'];
                $licenses_to_block  = $_GET[$nvp_key];

                foreach ($licenses_to_block as $row) {
                    SLM_Utility::block_license_key_by_row_id($row);
                }

                echo '<div id="message" class="updated fade"><p> ' . $row . ' Selected records blocked successfully!</p></div>';
            }
        }
        if ('expired' === $this->current_action()) {
            //Process blocked bulk actions
            if (!isset($_REQUEST['item'])) {
                $error_msg = '<p>' . __('Error - Please select some records using the checkboxes', 'slm') . '</p>';
                echo '<div id="message" class="error fade">' . $error_msg . '</div>';
                return;
            } else {
                $nvp_key            = $this->_args['singular'];
                $licenses_to_expire  = $_GET[$nvp_key];

                foreach ($licenses_to_expire as $row) {
                    SLM_Utility::expire_license_key_by_row_id($row);
                }

                echo '<div id="message" class="updated fade"><p> ' . $row . ' Selected records expired successfully!</p></div>';
            }
        }
        if ('active' === $this->current_action()) {
            //Process blocked bulk actions
            if (!isset($_REQUEST['item'])) {
                $error_msg = '<p>' . __('Error - Please select some records using the checkboxes', 'slm') . '</p>';
                echo '<div id="message" class="error fade">' . $error_msg . '</div>';
                return;
            }
            else {
                $nvp_key                = $this->_args['singular'];
                $liceses_to_activate    = $_GET[$nvp_key];

                // var_dump( $liceses_to_activate);

                foreach ($liceses_to_activate as $row) {
                    SLM_Utility::active_license_key_by_row_id($row);
                }

                echo '<div id="message" class="updated fade"><p> ' . $row . ' Selected records activated successfully!</p></div>';
            }
        }
    }



    /*
     * This function will delete the selected license key entries from the DB.
     */
    function delete_license_key($key_row_id)
    {
        SLM_Utility::delete_license_key_by_row_id($key_row_id);
        $success_msg    = '<div id="message" class="updated"><p><strong>';
        $success_msg    .= 'The selected entry was deleted successfully!';
        $success_msg    .= '</strong></p></div>';
        echo $success_msg;
    }

    function block_license_key($key_row_id)
    {
        SLM_Utility::block_license_key_by_row_id($key_row_id);
        $success_msg    = '<div id="message" class="updated"><p><strong>';
        $success_msg    .= 'The selected entry was blocked successfully!';
        $success_msg    .= '</strong></p></div>';
        echo $success_msg;
    }

    private function sort_data($a, $b){
        // Set defaults
        $orderby = 'id';
        $order = 'desc';
        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }
        $result = strcmp($a[$orderby], $b[$orderby]);
        if ($order === 'asc') {
            return $result;
        }
        return -$result;
    }

    function prepare_items(){

        $per_page       = 24;
        $columns        = $this->get_columns();
        $hidden         = array();
        $sortable       = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        global $wpdb;
        $license_table = SLM_TBL_LICENSE_KEYS;

        $search = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : false;
        $search_term = trim(strip_tags($search));

        $do_search = $wpdb->prepare("SELECT * FROM " . $license_table . " WHERE `license_key` LIKE '%%%s%%' OR `email` LIKE '%%%s%%' OR `txn_id` LIKE '%%%s%%' OR `first_name` LIKE '%%%s%%' OR `last_name` LIKE '%%%s%%'", $search_term, $search_term, $search_term, $search_term, $search_term);

        $data = $wpdb->get_results($do_search, ARRAY_A);

        usort($data, array(&$this, 'sort_data'));

        $current_page   = $this->get_pagenum();
        $total_items    = count($data);
        $data           = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        $this->items    = $data;

        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }
}



class SLM_Plugin{

    // class instance
    static $instance;

    // customer WP_List_Table object
    public $licenses_obj;

    // class constructor
    public function __construct(){
        add_filter('set-screen-option', [__CLASS__, 'set_screen'], 10, 3);
        add_action('admin_menu', [$this, 'slm_add_admin_menu']);
    }

    public static function set_screen($status, $option, $value){
        return $value;
    }

    public function slm_add_admin_menu()
    {
        $icon_svg = SLM_ASSETS_URL . 'images/slm_logo_small.svg';

        add_menu_page("SLM", "SLM", SLM_MANAGEMENT_PERMISSION, SLM_MAIN_MENU_SLUG, "slm_manage_licenses_menu", $icon_svg);
        $hook = add_submenu_page(SLM_MAIN_MENU_SLUG, "Manage Licenses", "Manage Licenses", SLM_MANAGEMENT_PERMISSION, SLM_MAIN_MENU_SLUG, "slm_manage_licenses_menu");
        add_submenu_page(SLM_MAIN_MENU_SLUG, "Add License", "Add Licenses", SLM_MANAGEMENT_PERMISSION, 'slm_manage_license', "slm_add_licenses_menu");
        add_submenu_page(SLM_MAIN_MENU_SLUG, "Tools", "Tools", SLM_MANAGEMENT_PERMISSION, 'slm_admin_tools', "slm_admin_tools_menu");
        add_submenu_page(SLM_MAIN_MENU_SLUG, "Settings", "Settings", SLM_MANAGEMENT_PERMISSION, 'slm_settings', "slm_settings_menu");
        add_submenu_page(SLM_MAIN_MENU_SLUG, "Help", "Help", SLM_MANAGEMENT_PERMISSION, 'slm_help', "slm_integration_help_menu");

        add_action("load-$hook", [$this, 'screen_option']);
    }


    /**
     * Screen options
     */
    public function screen_option(){

        $option = 'per_page';
        $args   = [
            'label'   => 'Pagination',
            'default' => 24,
            'option'  => 'licenses_per_page'
        ];

        //add_screen_option($option, $args);

        $this->licenses_obj = new SLM_List_Licenses();
    }


    /** Singleton instance */
    public static function get_instance(){
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


}

add_action('plugins_loaded', function () {
    SLM_Plugin::get_instance();
});




