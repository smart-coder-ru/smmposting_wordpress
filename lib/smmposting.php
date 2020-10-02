<?php
/**
 * @package  : SMM-Posting API
 * @version 2.0
 * @author smartcoder & vladgaus
 * @copyright https://smm-posting.ru
 */

class Smmposting
{
    public static $domain = 'https://smm-posting.ru/';
    public static $api_version = 2;
    private $api_token;

    /**
     * Smmposting constructor.
     * @param $api_token
     * @param int $api_version
     */
    public function __construct($api_token, $api_version = 2)
    {
        $this->api_token    = $api_token;
        self::$api_version  = $api_version;
    }


    public function api($api_method, $params = array(), $http_method = "GET") {
        $args = array(
            'method'      => $http_method,
            'body'        => $params,
            'timeout'     => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'cookies'     => array(),
        );
        $url = self::$domain."api/v".self::$api_version."/smmposting/$api_method?api_token=$this->api_token&".http_build_query($params);
        $response = wp_remote_request( $url, $args );
        $response = wp_remote_retrieve_body( $response );
        return json_decode($response);
    }

    /**
     * Auth links to socials
     * @return array
     */
    public static function getAuthLinks()
    {
        return [
            'ok_auth_link' => self::$domain . 'api/v'.self::$api_version.'/smmposting/ok_auth', // Odnoklassniki
            'vk_auth_link' => self::$domain . 'api/v'.self::$api_version.'/smmposting/vk_auth', // Vkontakte
            'tg_auth_link' => self::$domain . 'api/v'.self::$api_version.'/smmposting/tg_auth', // Telegram
            'fb_auth_link' => self::$domain . 'api/v'.self::$api_version.'/smmposting/fb_auth', // Facebook
            'tb_auth_link' => self::$domain . 'api/v'.self::$api_version.'/smmposting/tb_auth', // Tumblr
            'tw_auth_link' => self::$domain . 'api/v'.self::$api_version.'/smmposting/tw_auth', // Twitter
            'ig_auth_link' => self::$domain . 'api/v'.self::$api_version.'/smmposting/ig_auth', // Instagram
        ];
    }

}