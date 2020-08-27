<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class SMMP_DB {
    public function install() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        //  Install table smmposting_accounts
        $table_name = $wpdb->prefix . 'smmposting_accounts';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        `account_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` varchar(64) NOT NULL,
        `account_name` varchar(255) NOT NULL,
        `social` varchar(255) NOT NULL,
        `instagram_login` varchar(255) NOT NULL,
        `instagram_password` varchar(255) NOT NULL,
        `telegram_token` varchar(255) NOT NULL,
        `vk_user_id` varchar(255) NOT NULL,
        `vk_access_token` varchar(255) NOT NULL,
        `ok_user_id` varchar(255) NOT NULL,
        `ok_access_token` varchar(255) NOT NULL,
        `fb_access_token` varchar(255) NOT NULL,
        `fb_user_id` varchar(255) NOT NULL,
        `tw_oauth_token` varchar(255) NOT NULL,
        `tw_oauth_verifier` varchar(255) NOT NULL,
        `tb_oauth_token` varchar(255) NOT NULL,
        `tb_oauth_verifier` varchar(255) NOT NULL,
        `tb_oauth_token_secret` varchar(255) NOT NULL,
        `status` tinyint(1) NOT NULL,
        `date_added` datetime NOT NULL,
		PRIMARY KEY (`account_id`)
	) $charset_collate";

        dbDelta( $sql );

        //  Install table smmposting_image
        $table_name = $wpdb->prefix . 'smmposting_image';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
                                `post_id` int(11) NOT NULL,
                                `image` varchar(255) NOT NULL,
                                `filename` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	) $charset_collate";

        dbDelta( $sql );

        //  Install table smmposting_post
        $table_name = $wpdb->prefix . 'smmposting_post';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                                `post_id` int(11) NOT NULL AUTO_INCREMENT,
                                `project_id` int(11) NOT NULL,
                                `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `status` tinyint(1) NOT NULL,
                                `odnoklassniki` int(11) NOT NULL,
                                `vkontakte` int(11) NOT NULL,
                                `telegram` int(11) NOT NULL,
                                `instagram` int(11) NOT NULL,
                                `facebook` int(11) NOT NULL,
                                `tumblr` int(11) NOT NULL,
                                `twitter` int(11) NOT NULL,
                                `vk_download` int(11) NOT NULL,
                                `ok_download` int(11) NOT NULL,
                                `tg_download` int(11) NOT NULL,
                                `ig_download` int(11) NOT NULL,
                                `fb_download` int(11) NOT NULL,
                                `tb_download` int(11) NOT NULL,
                                `tw_download` int(11) NOT NULL,
                                `vk_success` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `ok_success` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `tg_success` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `ig_success` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `fb_success` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `tb_success` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `tw_success` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `ok_error` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `vk_error` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `tg_error` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `ig_error` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `fb_error` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `tb_error` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `tw_error` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                `ig_like` int(11) NOT NULL,
                                `ig_comment` text NOT NULL,
                                `vk_from_group` int(11) NOT NULL,
                                `vk_comment` text NOT NULL,
                                `date_public` date NOT NULL,
                                `time_public` time NOT NULL,
		PRIMARY KEY (`post_id`)
	) $charset_collate";

        dbDelta( $sql );

        //  Install table smmposting_projects
        $table_name = $wpdb->prefix . 'smmposting_projects';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                                `project_id` int(11) NOT NULL AUTO_INCREMENT,
                                `user_id` varchar(64) NOT NULL,
                                `project_name` varchar(255) NOT NULL,
                                `project_description` varchar(255) NOT NULL,
                                `ok_account_id` int(11) DEFAULT NULL,
                                `vk_account_id` int(11) DEFAULT NULL,
                                `tg_account_id` int(11) DEFAULT NULL,
                                `ig_account_id` int(11) DEFAULT NULL,
                                `fb_account_id` int(11) DEFAULT NULL,
                                `tb_account_id` int(11) DEFAULT NULL,
                                `tw_account_id` int(11) DEFAULT NULL,
                                `telegram_chat_id` varchar(255) NOT NULL,
                                `vk_group_id` varchar(255) NOT NULL,
                                `ok_group_id` varchar(255) NOT NULL,
                                `fb_group_id` varchar(255) NOT NULL,
                                `status` tinyint(1) NOT NULL,
                                `date_added` datetime NOT NULL,
		PRIMARY KEY (`project_id`)
	) $charset_collate";

        dbDelta( $sql );
    }
    public function uninstall() {
        global $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS `smmposting_accounts`');
        $wpdb->query('DROP TABLE IF EXISTS `smmposting_image`');
        $wpdb->query('DROP TABLE IF EXISTS `smmposting_post`');
        $wpdb->query('DROP TABLE IF EXISTS `smmposting_projects`');
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