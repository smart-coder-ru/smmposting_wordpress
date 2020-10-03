<?php

class SmmpostingController {

    private $auth = false;
    private $smmposting;
    private $smmposting_model;
    private $request;
    private $language;
    private $session;
    private $response;
    private $version = '1.0.4';

    public function __construct()
    {
        //  Scripts
        $this->smmposting_scripts();
        //  Model
        $this->smmposting_model = new SmmpostingModel();
        //  Request
        $this->request = new SMMP_Request();
        //  Language
        $this->language = new SMMP_Language();
        //  Response
        $this->response = new SMMP_Response();
        #	CheckInstall API SMM-posting
        $this->checkInstallApi();

    }
    public function checkInstallApi()
    {
        if (isset($this->request->get['route']) && $this->request->get['route'] != 'welcome') {
            $this->checkApiToken();
        }
    }
    private function checkApiToken($api_token = false)
    {
        if (!$api_token) $api_token = $this->getApiToken();

        $this->smmposting = new Smmposting($api_token);
        $profile = $this->smmposting->api('profile');

        $setData = array(
            'SMMposting' => [
                'config' => ['api_token' => $api_token]
            ]
        );
        $this->smmposting_model->editSetting('SMMposting', $setData);

        if (isset($profile->error) && $profile->error == "Y") {
            $_SESSION['error_warning'] = isset($profile->text) ? $profile->text : $this->language->get('smmposting_error_1');
            $this->response->redirect(admin_url().'admin.php?page=smmposting&route=welcome');
        } else {
            if (isset($profile->result->date_off)) {
                $now = time();
                $your_date = strtotime($profile->result->date_off);
                $date_diff = $your_date - $now;
                $date_diff =  floor($date_diff / (60 * 60 * 24));

                if ($date_diff <= 3) {
                    if (isset($_SESSION['remain_to_pay'])) {
                        if ($_SESSION['remain_to_pay'] == $date_diff) {
                            unset($_SESSION['remain_to_pay']);
                        } else {
                            $_SESSION['remain_to_pay'] = $date_diff;
                        }
                    }
                }
            }
            $this->auth = true;
        }

        return true;
    }

    private function getApiToken()
    {
        $config = $this->smmposting_model->getSetting('SMMposting');

        return isset($config['SMMposting']['config']['api_token']) ? $config['SMMposting']['config']['api_token'] : false;
    }

    public function smmposting_scripts()
    {
        //  jQuery
        add_action( 'wp_enqueue_scripts', function () {
            wp_enqueue_script( 'jquery' );
        } );
        //  Smmposting styles
        wp_enqueue_style( 'smmposting_wordpress', SMMP_PLUGIN_URL . '/view/assets/css/smmposting_wordpress.css');
        wp_enqueue_style( 'smmposting', SMMP_PLUGIN_URL . ('/view/assets/css/smmposting.css'));
        //  Smmposting script
        wp_enqueue_script( 'smmposting', SMMP_PLUGIN_URL .('/view/assets/js/smmposting.js'));
        wp_enqueue_script( 'smmposting_instagram', SMMP_PLUGIN_URL .('/view/assets/js/instagram.js'));
        //  Bootstrap
        wp_enqueue_style( 'bootstrap', SMMP_PLUGIN_URL . ('/view/assets/plugins/bootstrap/css/bootstrap.css'));
        wp_enqueue_script( 'bootstrap', SMMP_PLUGIN_URL .('/view/assets/plugins/bootstrap/js/bootstrap.js'));
        //  FontAwesome
        wp_enqueue_style( 'fontawesome', SMMP_PLUGIN_URL .('/view/assets/plugins/fontawesome/css/all.css'));
        //  SweetAlert
        wp_enqueue_style( 'sweetalert', SMMP_PLUGIN_URL .('/view/assets/plugins/sweetalert2/sweetalert2.css'));
        wp_enqueue_script( 'sweetalert', SMMP_PLUGIN_URL .('/view/assets/plugins/sweetalert2/sweetalert2.min.js'));
        //  Dropzone
        wp_enqueue_style( 'dropzone', SMMP_PLUGIN_URL .('/view/assets/plugins/dropzone/dist/dropzone.css'));
        wp_enqueue_script( 'dropzone', SMMP_PLUGIN_URL .('/view/assets/plugins/dropzone/dist/dropzone.js'));
    }

    public function config()
    {
        $data['version'] = SMMP_PLUGIN_VERSION;
        $data['route'] = isset($this->request->get['route']) ? $this->request->get['route'] : '';

        switch ($data['route']) {
            case 'marketing/smmposting/post':
            case 'marketing/smmposting/posts':
                $heading_title = $this->language->get('text_posts');
                break;
            case 'marketing/smmposting/product':
            case 'marketing/smmposting/products':
                $heading_title = $this->language->get('text_products');
                break;
            case 'marketing/smmposting/accounts':
                $heading_title = $this->language->get('text_accounts');
                break;
            case 'marketing/smmposting/project':
            case 'marketing/smmposting/projects':
                $heading_title = $this->language->get('text_projects');
                break;
            case 'marketing/smmposting/settings':
                $heading_title = $this->language->get('text_settings');
                break;
            default:
                $heading_title = $this->language->get('text_smmposting');
                break;
        }

        $data['heading_title'] = $heading_title;
        $data['route'] = isset($this->request->get['route']) ? $this->request->get['route'] : '';
        $data['version'] = $this->version;
        $data['domain'] = $_SERVER['HTTP_HOST'];

        #	USER CONFIG
        $config = $this->smmposting_model->getSetting('SMMposting');
        $data['config'] = isset($config['SMMposting']['config']) ? $config['SMMposting']['config'] : [];
        $data['api_token'] = isset($config['SMMposting']['config']['api_token']) ? $config['SMMposting']['config']['api_token'] : null;

        if (isset($this->request->get['error'])) {
            $_SESSION['error_warning'] = $this->request->get['error'];
        }

        unset($_SESSION['smmposting_profile']);
        if (isset($_SESSION['smmposting_profile'])) {
            $data['smmposting_profile'] = $_SESSION['smmposting_profile'];
        } else {
            if ($data['api_token']) {
                $this->smmposting = new Smmposting($data['api_token']);
                $profile = $this->smmposting->api('profile');
                if (isset($profile->result)) {
                    $data['smmposting_profile'] = $profile->result;
                    $_SESSION['smmposting_profile'] = $profile->result;
                }

                if (isset($profile->error) && $profile->error == "Y" && isset($profile->text)) {
                    $data['error_connect'] = $profile->text;
                }
            }
        }

        if (isset($_SESSION['remain_to_pay'])) {
            $data['remain_to_pay'] = $_SESSION['remain_to_pay'];
        }


        $data['error_warning'] = isset($_SESSION['error_warning']) ? $_SESSION['error_warning'] : false;
        unset($_SESSION['error_warning']);
        $data['success'] = isset($_SESSION['success']) ? $_SESSION['success'] : null;
        unset($_SESSION['success']);


        return $data;
    }

    public function links()
    {
        return array(
            //Menu links
            'add_post_link' => '/wp-admin/admin.php?page=smmposting&route=post',
            'edit_post_link' => '/wp-admin/admin.php?page=smmposting&route=post',
            'copy_post_link' => '/wp-admin/admin.php?page=smmposting&route=copyPost',
            'delete_post_link' => '/wp-admin/admin.php?page=smmposting&route=deletePost',
            'posts_link' => '/wp-admin/admin.php?page=smmposting&route=posts',
            'products_link' => '/wp-admin/admin.php?page=smmposting&route=products',
            'accounts_link' => '/wp-admin/admin.php?page=smmposting&route=accounts',
            'welcome_link' => '/wp-admin/admin.php?page=smmposting&route=welcome',

            //Projects
            'edit_project_link' => '/wp-admin/admin.php?page=smmposting&route=project',
            'deleteproject_link' => '/wp-admin/admin.php?page=smmposting&route=deleteProject',
            'project_list' => '/wp-admin/admin.php?page=smmposting&route=projects',
            'add_project_link' => '/wp-admin/admin.php?page=smmposting&route=project',

            //Settings
            'contact_link' => '/wp-admin/admin.php?page=smmposting&route=contact',
            'cancel' => '/wp-admin/admin.php?page=smmposting&route=posts',

            //	Actions
            'deleteImage'	=> '/wp-admin/admin.php?page=smmposting&route=deleteImage',
            'action_edit_project'   => '/wp-admin/admin.php?page=smmposting&route=editProject',
            'action_add_project'   => '/wp-admin/admin.php?page=smmposting&route=addProject',
            'action_add_post'        => '/wp-admin/admin.php?page=smmposting&route=addPost',
            'action_edit_post'        => '/wp-admin/admin.php?page=smmposting&route=editPost',
            'action_upload_image'   => '/wp-admin/async-upload.php',

        );
    }

    public function load_module_data()
    {
        return array_merge(array_merge($this->links(),$this->language->all(),$this->config()));
    }

    public function welcome()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (isset($this->request->post['config']['api_token'])) {
                $res = $this->checkApiToken($this->request->post['config']['api_token']);
                if ($res) $this->response->redirect(admin_url().'admin.php?page=smmposting&route=posts');
            }
        }

        extract($this->load_module_data());
        require_once( SMMP_PLUGIN_DIR . 'view/welcome.php');
    }
    public function accounts()
    {

        if (isset($this->request->get['s'])) {
            $_SESSION['success'] = $this->language->get('account_added');
        }

        extract($this->load_module_data());

        $page = isset($this->request->get['p']) ? $this->request->get['p'] : 1;
        $limit = isset($this->request->get['limit']) ? $this->request->get['limit'] : 10;

        $connected_accounts = $this->smmposting->api('connected_accounts',['page'=>$page, 'limit'=>$limit]);

        $data['count'] = $count = isset($connected_accounts->count) ? $connected_accounts->count : 0;
        $connected_accounts = isset($connected_accounts->result) ? $connected_accounts->result : [];
        $data['accounts'] = json_decode(json_encode($connected_accounts), true);

        //	Redirect Link
        $data['server_link'] = get_site_url() . '/wp-admin/admin.php?page=smmposting&route=accounts';
        $result_auth_links = $this->smmposting->api('socials', ['redirect_url' => $data['server_link']]);
        $data['allowed_socials'] = isset($result_auth_links->result) ? $result_auth_links->result : [];
        $data['auth_links'] = $this->smmposting->getAuthLinks();

        //  Pagination
        $pagination 		= new SMMP_Pagination();
        $pagination->total 	= $count;
        $pagination->page 	= isset($this->request->get['p']) ? $this->request->get['p'] : 1;
        $pagination->limit 	= $limit;
        $pagination->url 	= get_site_url() . '/wp-admin/admin.php?page=smmposting&route=accounts';
        $data['pagination']	= $pagination->render();


        $data['results'] = '';

        extract($data);
        require_once( SMMP_PLUGIN_DIR . 'view/accounts.php');
    }
    public function deleteAccount() {

        if( isset($this->request->get['account_id']) ) {
            //	Send to SMMposting
            $res = $this->smmposting->api('account_delete/'.$this->request->get['account_id'],[],'DELETE');

            //	Response
            if (isset($res->result->success) && $res->error == "N") {
                $_SESSION['success'] = $this->language->get('account_deleted');
            } else {
                $_SESSION['error_warning'] = $this->language->get('account_not_deleted');
            }
        }

        $this->accounts();

    }
    /*
     * Posts Page
     */
    public function posts(){

        $data = $this->load_module_data();

        $data['delete_link'] = admin_url().'admin.php?page=smmposting&route=deletePost&id=%s&';

        //	Send to SMMposting
        $page = isset($this->request->get['p']) ? $this->request->get['p'] : 1;
        $limit = isset($this->request->get['limit']) ? $this->request->get['limit'] : 10;
        $results = $this->smmposting->api('list_posts',  ['page'=>$page, 'limit'=>$limit]);

        //	Response
        $count = isset($results->count) ? $results->count : 0;
        $results = isset($results->result) ? $results->result : [];
        $results = json_decode(json_encode($results), true);

        foreach ($results as $result) {
            $data['posts'][] = array(
                'post_id' 		=> $result['id'],
                'project_id' 	=> $result['project_id'],
                'project_name' 	=> $result['project_name'],
                'image' 		=> isset($result['media'][0]) ? $result['media'][0] : null,
                'content' 		=> nl2br(substr(html_entity_decode($result['content']), 0, 250)),
                'status' 		=> $result['status'],
                'vk'			=> isset($result['socials']) && in_array("vk",$result['socials']),
                'ok'			=> isset($result['socials']) && in_array("ok",$result['socials']),
                'tg'			=> isset($result['socials']) && in_array("tg",$result['socials']),
                'ig'			=> isset($result['socials']) && in_array("ig",$result['socials']),
                'fb'			=> isset($result['socials']) && in_array("fb",$result['socials']),
                'tb'			=> isset($result['socials']) && in_array("tb",$result['socials']),
                'tw'			=> isset($result['socials']) && in_array("tw",$result['socials']),
                'date_public' 	=> date('d.m.y', strtotime($result['date_public'])),
                'time_public' 	=> date('H:i', strtotime($result['time_public'])),
            );
        }

        $pagination 		= new SMMP_Pagination();
        $pagination->total 	= $count;
        $pagination->page 	= isset($this->request->get['p']) ? $this->request->get['p'] : 1;
        $pagination->limit 	= $limit;
        $pagination->url 	= get_site_url() . '/wp-admin/admin.php?page=smmposting&route=posts';
        $data['pagination']	= $pagination->render();
        $data['results'] = '';

        extract($data);
        require_once( SMMP_PLUGIN_DIR . 'view/posts.php');
    }

    public function post()
    {

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatePermission()) {

            $request = $this->request->request;
            if (isset($request['page'])) { unset($request['page']); }
            $_SESSION['old'] = array('post'=>$request);
            /*
            |--------------------------------------------------------------------------
            | Validate
            |--------------------------------------------------------------------------
            |
            */
            if (empty($request['content'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_3');
            }
            if (isset($request['media']) && count($request['media']) > 5) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_4');
            }
            if (!isset($request['media'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_15');
            }
            if (!is_numeric($request['project_id'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_5');
            }
            if (empty($request['time_public'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_6');
            }
            if (empty($request['date_public'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_7');
            }

            if (isset($_SESSION['error_warning'])) {
                if (isset($request['id'])) {
                    $this->response->redirect(admin_url().'admin.php?page=smmposting&route=post&id=' . (int)$request['id']);
                } else {
                    $this->response->redirect(admin_url().'admin.php?page=smmposting&route=post');
                }
            }

            /////////////////////////////////////////

            if (isset($request['media'])) {
                $request['media'] = json_encode($request['media']);
            }
            if (isset($request['socials'])) {
                $request['socials'] = json_encode($request['socials']);
            }
            if (isset($request['allowed'])) {
                unset($request['allowed']);
            }

            if (isset($request['id'])) {
                //	Send to SMMposting
                $results = $this->smmposting->api('update_post/'.(int)$request['id'], $request, 'PATCH');
            } else {
                //	Send to SMMposting
                $results = $this->smmposting->api('add_post', $request, 'POST');
            }

            //	Response
            if (isset($results->result->success) && $results->result->success == "Y") {
                unset($_SESSION['old']);
                $_SESSION['success'] = $this->language->get('text_success');
                $this->response->redirect(admin_url().'admin.php?page=smmposting&route=posts');
            } else {
                $_SESSION['error_warning'] = isset($results->result) ? $results->result : $this->language->get('smmposting_error_14');
                if (isset($request['id'])) {
                    $this->response->redirect(admin_url().'admin.php?page=smmposting&route=post&id=' . (int)$request['id']);
                } else {
                    $this->response->redirect(admin_url().'admin.php?page=smmposting&route=post');
                }
            }
            $this->posts();
        }

        $data = $this->load_module_data();

        $data['post'] = [];
        if (isset($this->request->get['id'])) {
            $data['action'] = admin_url().'admin.php?page=smmposting&route=post&id='.$this->request->get['id'];
            //	Send to SMMposting Get Post
            $results = $this->smmposting->api("get_post/".$this->request->get['id']);
            //	Response
            $results = isset($results->result) ? $results->result : [];
            $results = json_decode(json_encode($results), true);
            $data['post'] = $results;

            //	Send to SMMposting Get Project
            $project_info = $this->smmposting->api("get_project/".$results['project_id']);
            //	Response
            $project_info = isset($project_info->result) ? $project_info->result : [];
            $project_info = json_decode(json_encode($project_info), true);


        } else {
            $data['action'] = admin_url().'admin.php?page=smmposting&route=post';
            $data['post'] = [];
        }

        if (!isset($_SESSION['old'])) {
            $data['post']['allowed'] = isset($project_info['allowed']) ? $project_info['allowed'] : [];
        }

        //	Send to SMMposting
        $results = $this->smmposting->api('list_projects', ['limit'=>100]);
        //	Response
        $smm_projects = isset($results->result) ? $results->result : [];
        $data['projects'] = json_decode(json_encode($smm_projects), true);

        $data['cancel'] = admin_url().'admin.php?page=smmposting&route=post';

        $data['post']['date_public'] = isset($data['post']['date_public']) ? $data['post']['date_public'] : date("Y-m-d");
        ## Time
        $data['date_today'] = date("Y-m-d");
        $data['date_tomorrow'] = date('Y-m-d', strtotime("+1 day"));
        $data['date_after_tomorrow'] = date('Y-m-d', strtotime("+2 day"));


        //	OLD DATA
        if (isset($_SESSION['old'])) {
            $data = array_replace($data, $_SESSION['old']);
            unset($_SESSION['old']);
        }

        //	HIDE SOCIALS AFTER OLD DATA
        $data['hide_ok'] = (isset($data['post']['allowed']) && !in_array("ok",$data['post']['allowed']));
        $data['hide_vk'] = (isset($data['post']['allowed']) && !in_array("vk",$data['post']['allowed']));
        $data['hide_tg'] = (isset($data['post']['allowed']) && !in_array("tg",$data['post']['allowed']));
        $data['hide_ig'] = (isset($data['post']['allowed']) && !in_array("ig",$data['post']['allowed']));
        $data['hide_fb'] = (isset($data['post']['allowed']) && !in_array("fb",$data['post']['allowed']));
        $data['hide_tw'] = (isset($data['post']['allowed']) && !in_array("tw",$data['post']['allowed']));
        $data['hide_tb'] = (isset($data['post']['allowed']) && !in_array("tb",$data['post']['allowed']));


        extract($data);
        require_once( SMMP_PLUGIN_DIR . 'view/post.php');

    }
    public function deletePost() {
        if( isset($this->request->get['id']) && $id=$this->request->get['id'] ){
            //	Send to SMMposting
            $res = $this->smmposting->api('delete_post/'.$this->request->get['id'],[],'DELETE');
            //	Response
            if ((isset($res->result->was_deleted) && $res->result->was_deleted == "Y") || (isset($res->result->success) && $res->result->success == "Y")) {
                $_SESSION['success'] = $this->language->get('text_success');
            } else {
                $_SESSION['error_warning'] = $this->language->get('error_warning');
            }
        }

        $this->response->redirect(admin_url().'admin.php?page=smmposting&route=posts');
    }
    public function project()
    {

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatePermission()) {

            $request = $this->request->request;
            if (isset($request['page'])) { unset($request['page']); }
            $_SESSION['old'] = array('project'=>$request);

            /*
            |--------------------------------------------------------------------------
            | Validate
            |--------------------------------------------------------------------------
            |
            */
            if (empty($request['name'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_8');
            }

            //	OK
            if ($request['ok_account_id'] && !isset($request['ok_group_id'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_9');
            }

            //	VK
            if ($request['vk_account_id'] && !isset($request['vk_group_id'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_10');
            }

            //	TG
            if ($request['tg_account_id'] && empty($request['tg_chat_id'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_11');
            }

            //	FB
            if ($request['fb_account_id'] && !isset($request['fb_group_id'])) {
                $_SESSION['error_warning'] = $this->language->get('smmposting_error_12');
            }


            if (isset($_SESSION['error_warning'])) {
                if (isset($request['id'])) {
                    $this->response->redirect(get_site_url() . '/wp-admin/admin.php?page=smmposting&route=project&id='. (int)$request['id']);
                } else {
                    $this->response->redirect(get_site_url() . '/wp-admin/admin.php?page=smmposting&route=project');
                }
            }

            /////////////////////////////////////////



            if (!$request['ok_account_id'] && !isset($request['ok_group_id'])) {
                unset($request['ok_account_id']);
            }

            if (!$request['vk_account_id'] && !isset($request['vk_group_id'])) {
                unset($request['vk_account_id']);
            }

            if (!$request['tg_account_id'] && isset($request['tg_chat_id'])) {
                unset($request['tg_account_id']);
                unset($request['tg_chat_id']);
            }

            if (!$request['fb_account_id'] && !isset($request['fb_group_id'])) {
                unset($request['fb_account_id']);
            }

            if (isset($request['ig_account_id']) && !$request['ig_account_id']) {
                unset($request['ig_account_id']);
            }

            if (isset($request['tb_account_id']) && !$request['tb_account_id']) {
                unset($request['tb_account_id']);
            }

            if (isset($request['tw_account_id']) && !$request['tw_account_id']) {
                unset($request['tw_account_id']);
            }

            if (isset($request['id'])) {
                //	Send to SMMposting
                $results = $this->smmposting->api('update_project/'.(int)$request['id'], $request, 'PATCH');
            } else {
                //	Send to SMMposting
                $results = $this->smmposting->api('add_project', $request, 'POST');
            }

            //	Response
            if (isset($results->result->success) && $results->result->success == "Y") {
                unset($_SESSION['old']);
                $_SESSION['success'] = $this->language->get('text_success');
                $this->response->redirect(get_site_url() . '/wp-admin/admin.php?page=smmposting&route=projects');
            } else {

                $_SESSION['error_warning'] = isset($results->result) ? $results->result : $this->language->get('smmposting_error_13');
                if (isset($request['id'])) {
                    $this->response->redirect(get_site_url() . '/wp-admin/admin.php?page=smmposting&route=project&id=' . (int)$request['id']);
                } else {
                    $this->response->redirect(get_site_url() . '/wp-admin/admin.php?page=smmposting&route=project');
                }


            }

            $this->projects();
        }

        extract($this->load_module_data());
        if (isset($data)) extract($data);

        if (isset($this->request->get['id'])) {
            $data['action'] = get_site_url() . '/wp-admin/admin.php?page=smmposting&route=project&id='. $this->request->get['id'];
            //	Send to SMMposting Get Post
            $results = $this->smmposting->api("get_project/".$this->request->get['id']);
            //	Response
            $results = isset($results->result) ? $results->result : [];
            $results = json_decode(json_encode($results), true);
            $data['project'] = $results;
        } else {
            $data['action'] = get_site_url() . '/wp-admin/admin.php?page=smmposting&route=project';
            $data['project'] = [];
        }

        //	Send to SMMposting Get Connected Accounts
        $connected_accounts = $this->smmposting->api('connected_accounts',['limit'=>100]);
        $connected_accounts = isset($connected_accounts->result) ? $connected_accounts->result : [];
        $connected_accounts = json_decode(json_encode($connected_accounts), true);

        $data['accounts'] = [
            "ok" => [], "vk" => [], "tg" => [], "ig" => [], "fb" => [], "tw" => [], "tb" => []
        ];
        foreach ($connected_accounts as $account)
        {
            switch ($account['social']) {
                case "ok":
                    $data['accounts']["ok"][] = $account;
                    break;
                case "vk":
                    $data['accounts']["vk"][] = $account;
                    break;
                case "tg":
                    $data['accounts']["tg"][] = $account;
                    break;
                case "ig":
                    $data['accounts']["ig"][] = $account;
                    break;
                case "fb":
                    $data['accounts']["fb"][] = $account;
                    break;
                case "tw":
                    $data['accounts']["tw"][] = $account;
                    break;
                case "tb":
                    $data['accounts']["tb"][] = $account;
                    break;
            }
        }

        //	OLD DATA
        if (isset($data['project']['socials']['ok']['id'])) {
            $data['project']['ok_account_id'] = $data['project']['socials']['ok']['id'];
        }
        if (isset($data['project']['socials']['vk']['id'])) {
            $data['project']['vk_account_id'] = $data['project']['socials']['vk']['id'];
        }
        if (isset($data['project']['socials']['tg']['id'])) {
            $data['project']['tg_account_id'] = $data['project']['socials']['tg']['id'];
        }
        if (isset($data['project']['socials']['fb']['id'])) {
            $data['project']['fb_account_id'] = $data['project']['socials']['fb']['id'];
        }

        if (isset($_SESSION['old'])) {
            $data = array_replace($data, $_SESSION['old']);
            unset($_SESSION['old']);
        }

        extract($data);
        require_once( SMMP_PLUGIN_DIR . 'view/project.php');
    }


    public function projects()
    {
        extract($this->load_module_data());

        $page = isset($this->request->get['p']) ? $this->request->get['p'] : 1;
        $limit = isset($this->request->get['limit']) ? $this->request->get['limit'] : 10;
        $results = $this->smmposting->api('list_projects', ['page'=>$page, 'limit'=>$limit]);
        $count = isset($results->count) ? $results->count : 0;
        $smm_projects_results = isset($results->result) ? $results->result : [];
        $data['smm_projects'] = json_decode(json_encode($smm_projects_results), true);

        $pagination 		= new SMMP_Pagination();
        $pagination->total 	= $count;
        $pagination->page 	= isset($this->request->get['p']) ? $this->request->get['p'] : 1;
        $pagination->limit 	= $limit;
        $pagination->url 	= get_site_url() . '/wp-admin/admin.php?page=smmposting&route=projects';
        $data['pagination']	= $pagination->render();
        $data['results']    = '';

        extract($data);
        require_once( SMMP_PLUGIN_DIR . 'view/projects.php');
    }

    public function deleteProject() {
        if (isset($this->request->get['id'])) {
            //	Send to SMMposting
            $res = $this->smmposting->api('delete_project/'.$this->request->get['id'],[],'DELETE');
            //	Response
            if (isset($res->result->success) && $res->result->success == "Y") {
                $_SESSION['success'] = $this->language->get('text_success');
            } else {
                $_SESSION['error_warning'] = $this->language->get('error_warning');
            }
        }

        $this->response->redirect(admin_url().'admin.php?page=smmposting&route=projects');
    }
    public function contact()
    {
        extract($this->load_module_data());
        require_once( SMMP_PLUGIN_DIR . 'view/contact.php');
    }



    ## VALIDATION
    ####################################################################
    protected function validatePermission() {
        return 1;
    }


}
