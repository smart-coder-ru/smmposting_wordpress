<?php

class SMMP_Loader {
    public function load() {

        if (!is_admin()) {
            $this->call_public_hooks();
        }
        $this->call_global_hooks();
        if (is_admin()) {
            $this->call_admin_hooks();
        }
    }

    private function call_public_hooks()
    {
        add_action('init', array($this, 'cron'), 1);
    }
    public function call_global_hooks()
    {

    }
    private function call_admin_hooks()
    {


        /*
        |--------------------------------------------------------------------------
        | Add Menu Sidebar Left
        |--------------------------------------------------------------------------
        |
        */
        add_action('admin_menu', function () {
            $SMMP_Language = new SMMP_Language();

            add_menu_page(
                'SMM-posting', // Title of the page
                'SMM-posting', // Text to show on the menu link
                'manage_options', // Capability requirement to see the link
                'smmposting',
                array($this, 'SMMP_start'),
                'dashicons-megaphone'
            );

            add_submenu_page(
                'smmposting',
                $SMMP_Language->getFromLanguage('text_accounts'),
                $SMMP_Language->getFromLanguage('text_accounts'),
                'manage_options',
                'smmposting&route=accounts',
                'accounts'
            );

            add_submenu_page('smmposting',
                $SMMP_Language->getFromLanguage('text_projects'),
                $SMMP_Language->getFromLanguage('text_projects'),
                'manage_options',
                'smmposting&route=projects',
                'projects'
            );

            add_submenu_page('smmposting',
                $SMMP_Language->getFromLanguage('text_posts'),
                $SMMP_Language->getFromLanguage('text_posts'),
                'manage_options',
                'smmposting&route=posts',
                'posts'
            );

            add_submenu_page('smmposting',
                $SMMP_Language->getFromLanguage('text_settings'),
                $SMMP_Language->getFromLanguage('text_settings'),
                'manage_options',
                'smmposting&route=settings',
                'posts'
            );
        });


        /*
        |--------------------------------------------------------------------------
        | Other
        |--------------------------------------------------------------------------
        |
        */
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
    }

    /*
    |--------------------------------------------------------------------------
    | LoadController
    |--------------------------------------------------------------------------
    |
    */
    public function SMMP_start()
    {
        $SmmpostingController = new SmmpostingController();

        if (isset($_GET['route'])) {
            $function = sanitize_text_field($_GET['route']);
            if (method_exists($SmmpostingController,$function)) {
                return $SmmpostingController->$function();
            } else {
                http_response_code(404);
                die();
            }
        } else {
            $SmmpostingController->welcome();
        }
    }

    public function createMenu()
    {
        $subPages = array();
        add_menu_page('SMM-posting', 'SMM-posting', 'smmposting_access', 'smmposting', null, 'dashicons-megaphone');
        $subPages[] = add_submenu_page('smmposting', esc_html__('Accounts', 'smmposting'), esc_html__('Accounts', 'smmposting'), 'smmposting_access', 'smmposting', array($this, 'SMMP_accounts'));

        foreach ($subPages as $var) {
            add_action($var, array($this, 'addAssets'));
        }
    }

    public function SMMP_accounts()
    {
        echo 111;
    }
    public function activatePlugin() {

        $db = new SMMP_DB();
        $db->install();
        $this->initCaps();
    }
    public function deactivatePlugin() {

        $db = new SMMP_DB();
        $db->uninstall();
    }

    public function initCaps() {
        global $wp_roles;
        if (!class_exists('WP_Roles')) {
            wp_die('SMM-posting Plugin needs Wordpress Version 4.7.0 or higher');
        }
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }
        if (!function_exists('get_editable_roles')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        foreach (get_editable_roles() as $role_name => $role_info) {
            $wp_roles->add_cap($role_name, 'smmposting_access');
        }
    }
}