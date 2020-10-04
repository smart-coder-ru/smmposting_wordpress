<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class SMMP_DB {

    private $lastVersion = '1.0.0';

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