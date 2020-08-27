<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class SMMP_DB {

    private $lastVersion = '1.0.0';

    public function install() {
        if (get_option('smmp_plugin_version')) {
            $this->lastVersion = get_option('smmp_plugin_version');
        }

        $SMMP_Migrations = new SMMP_Migrations();

        if (version_compare($this->lastVersion, '1.0.0', '==')) {
            $this->uninstall();
            $SMMP_Migrations->SMMP_migration_1();
            $SMMP_Migrations->SMMP_migration_2();
        }

        if (version_compare($this->lastVersion, '1.0.1', '==')) {
            //$SMMP_Migrations->SMMP_migration_3();
        }

        update_option('smmp_plugin_version', SMMP_PLUGIN_VERSION);
    }
    public function uninstall() {
        update_option('smmp_plugin_version', 0);

        global $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'smmposting_accounts');
        $wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'smmposting_image');
        $wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'smmposting_post');
        $wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'smmposting_projects');

    }

    public function query($sql){
        global $wpdb;
        $result = $wpdb->get_results( $sql, ARRAY_A );

        $obj = new \stdClass();
        $obj->row = isset($result[0]) ? $result[0] : array();
        $obj->rows = $result;

        return $obj;

    }

    public function escape($string) {
        return sanitize_text_field($string);
    }
    public function getLastId()
    {
        global $wpdb;
        return $wpdb->insert_id;
    }
}