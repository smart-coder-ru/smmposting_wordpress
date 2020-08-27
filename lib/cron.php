<?php

/*
 * @module SMM-posting for Wordpress > 5.5
 * @author smart-coder.ru
 * @copyright Copyright (c) 2020 SMM-posting.ru
 * @version 1.0
 */

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

Class SMMP_Cron {

    protected $config = [];
    private $smmposting;
    private $smmposting_wordpress;
    private $smmposting_model;

    public function __construct() {

        #	Smmposting Wordpress
        $this->smmposting_wordpress = new SmmpostingWordpress();

        ##  Load Model
        $this->setModel();

        ## Module Settings
        $config = $this->smmposting_model->getSetting('SMMposting');
        $this->config = isset($config['SMMposting']['config']) ? $config['SMMposting']['config'] : [];
    }

    private function setModel()
    {
        $this->smmposting_model = new SmmpostingModel();
    }


    public function start() {
        //  Validate
        if(isset($_GET['api_token']) && isset($this->config['api_token'])) {
            if($_GET['api_token'] != $this->config['api_token']){
                $json['error'] = 'Not found or no valid API Token';
                echo json_encode($json, JSON_UNESCAPED_UNICODE);
                die;
            }
        } else {
            $json['error'] = 'Not found or no valid API Token';
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            die;
        }

        $json = [];

        if (isset($_GET['post_id']) && isset($_GET['social'])) {
            //  One Post
            $data['posts'] = $this->smmposting_wordpress->getPost($_GET['post_id'],$_GET['social']);
        } else if (isset($_GET['products']) && $_GET['project'] && $_GET['social']) {
            //  Products
            $data['posts'] = $this->smmposting_wordpress->getProductsAsPosts($_GET['project'], $_GET['products'], $_GET['social']);
        } else {
            //  Posts
            $data['posts'] = $this->smmposting_wordpress->getPosts();
        }

        $data['api_token'] = $this->config['api_token']; //  Api Token from https://smm-posting.ru/settings

        ##  Sending posts
        if (count($data['posts']) > 0) {
            $json = $this->send($data);
            if (isset($response->result)) {
                $this->smmposting_wordpress->saveResponse($response->result);
            }
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            die;
        } else {
            $json['error'] = 'No posts data or products data to send';
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            die;
        }
    }


    /**
     * @param string       $data['api_token'] - api token from smm-posting.ru/settings
     *
     * @param array        $data['posts'] - array with your publications (posts)
     *
     * @param int          $data['posts'][$key]['odnoklassniki'] - Public in odnoklassniki = 1
     * @param int          $data['posts'][$key]['vkontakte'] - Public in Vkontakte = 1
     * @param int          $data['posts'][$key]['telegram'] - Public in Telegram = 1
     * @param int          $data['posts'][$key]['instagram'] - Public in Instagram = 1
     * @param int          $data['posts'][$key]['facebook'] - Public in Facebook = 1
     * @param int          $data['posts'][$key]['tumblr'] - Public in Tumblr = 1
     * @param int          $data['posts'][$key]['twitter'] - Public in Twitter = 1
     * @param int          $data['posts'][$key]['post_id'] - Post ID
     * @param string       $data['posts'][$key]['content'] - Post Content
     * @param array        $data['posts'][$key]['images'] - Post Images Links
     * @param string       $data['posts'][$key]['instagram_login'] - Login Instagram
     * @param string       $data['posts'][$key]['instagram_password'] - Password Instagram
     * @param string       $data['posts'][$key]['telegram_token'] - Telegram token from Bot's Father
     * @param string       $data['posts'][$key]['telegram_chat'] - @yourchat
     * @param string       $data['posts'][$key]['vk_access_token'] - Vkontakte Access Token
     * @param string       $data['posts'][$key]['vk_user_id'] - Vkontakte user_id
     * @param string       $data['posts'][$key]['vk_group_id'] - Vkontakte group_id
     * @param string       $data['posts'][$key]['ok_access_token'] - Odnoklassniki Access Token
     * @param string       $data['posts'][$key]['ok_group_id'] - Odnoklassniki group_id
     * @param string       $data['posts'][$key]['fb_access_token'] - Facebook access_token
     * @param string       $data['posts'][$key]['fb_user_id'] - Facebook user_id
     * @param string       $data['posts'][$key]['tw_oauth_token'] - Twitter oauth_token
     * @param string       $data['posts'][$key]['tw_oauth_verifier'] - Twitter oauth_verifier
     * @param string       $data['posts'][$key]['tb_oauth_verifier'] - Tumblr oauth_verifier
     * @param string       $data['posts'][$key]['tb_oauth_token_secret'] - Tumblr oauth_token_secret

     *
     * @return mixed response
     */
    private function send($data) {
        #   Response from smm-posting.ru
        $this->smmposting = new Smmposting($this->getApiToken());
        return $this->smmposting->send($data);
    }

    private function getApiToken()
    {
        $config = $this->smmposting_model->getSetting('SMMposting');
        return isset($config['SMMposting']['config']['api_token']) ? $config['SMMposting']['config']['api_token'] : false;
    }


}