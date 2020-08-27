<?php

class SmmpostingController {

    private $auth = false;
    private $smmposting;
    public $smmposting_model;
    public $request;
    public function __construct()
    {
        //  Load Scripts
        $this->smmposting_scripts();
        //  Load Model
        $this->setModel();
        //  Sanitize Request
        $this->setRequest();
    }

    private function setModel()
    {
        $this->smmposting_model = new SmmpostingModel();
    }

    private function setRequest()
    {
        $this->request = new SMMP_Request();
    }

    private function getApiToken()
    {
        $config = $this->smmposting_model->getSetting('SMMposting');

        return isset($config['SMMposting']['config']['api_token']) ? $config['SMMposting']['config']['api_token'] : false;
    }

    public function cron() {
        echo "test"; die;
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
            case 'post':
            case 'posts':
                $data['heading_title'] = $this->getFromLanguage('text_posts');
                break;
            case 'product':
            case 'products':
                $data['heading_title'] = $this->getFromLanguage('text_products');
                break;
            case 'accounts':
                $data['heading_title'] = $this->getFromLanguage('text_accounts');
                break;
            case 'project':
            case 'projects':
                $data['heading_title'] = $this->getFromLanguage('text_projects');
                break;
            case 'settings':
                $data['heading_title'] = $this->getFromLanguage('text_settings');
                break;
            default:
                $data['heading_title'] = $this->getFromLanguage('text_smmposting');
                break;
        }

        $data['group_links'] = Smmposting::getGroupLinks();
        $data['connect_link'] = Smmposting::connectLink();
        $data['cron_link'] = $data['send_link'] = admin_url('') . 'admin.php?page=smmposting&route=cron&api_token='.$this->getApiToken();
        $data['domain'] = $this->request->server['HTTP_HOST'];
        $data['api_token'] = $this->getApiToken();

        //  Connect to SmmPosting
//        unset($_SESSION['smmposting_profile']);
        if (isset($_SESSION['smmposting_profile'])) {
            $data['smmposting_profile'] = $_SESSION['smmposting_profile'];
        } else {
            if ($data['api_token']) {
                $this->smmposting = new Smmposting($data['api_token']);
                $profile = $this->smmposting->profile();
                if (isset($profile->result)) {
                    $data['smmposting_profile'] = $profile->result;
                    $_SESSION['smmposting_profile'] = $profile->result;
                }

                if (isset($profile->error)) {
                    $data['error_connect'] = $profile->error;
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
            'settings' => '/wp-admin/admin.php?page=smmposting&route=settings',
            'cancel' => '/wp-admin/admin.php?page=smmposting&route=posts',

            //	Actions
            'deleteImage'	=> '/wp-admin/admin.php?page=smmposting&route=deleteImage',
            'action_add_telegram'   => '/wp-admin/admin.php?page=smmposting&route=addTelegram',
            'action_add_instagram'   => '/wp-admin/admin.php?page=smmposting&route=addInstagram',
            'action_edit_project'   => '/wp-admin/admin.php?page=smmposting&route=editProject',
            'action_add_project'   => '/wp-admin/admin.php?page=smmposting&route=addProject',
            'action_add_post'        => '/wp-admin/admin.php?page=smmposting&route=addPost',
            'action_edit_post'        => '/wp-admin/admin.php?page=smmposting&route=editPost',
            'action_upload_image'   => '/wp-admin/async-upload.php',

        );
    }

    public function getFromLanguage($param = '')
    {
        $SMMP_Language = new SMMP_Language();
        return $SMMP_Language->getFromLanguage($param);
    }
    public function languages()
    {
        $SMMP_Language = new SMMP_Language();
        return $SMMP_Language->languages();
    }

    public function load_module_data()
    {
        return array_merge(array_merge($this->links(),$this->languages(),$this->config()));
    }
    private function checkApiToken($api_token = false)
    {
        if (!$api_token) $api_token = $this->getApiToken();

        $this->smmposting = new Smmposting($api_token);
        $profile = $this->smmposting->profile();

        $setData = array(
            'SMMposting' => [
                'config' => ['api_token' => $api_token]
            ]
        );
        $this->smmposting_model->editSetting('SMMposting', $setData);


        if (isset($profile->error)) {
            $_SESSION['error_warning'] = isset($profile->error) ? $profile->error : $this->getFromLanguage('smmposting_error_1');

            echo '<script language="javascript">
              window.location.href = "'.admin_url().'admin.php?page=smmposting&route=welcome"
            </script>';

            die;
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

    public function welcome()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (isset($this->request->post['config']['api_token'])) {
                $this->checkApiToken($this->request->post['config']['api_token']);
            }
        }

        extract($this->load_module_data());
        require_once( SMMP_PLUGIN_DIR . 'view/welcome.php');
    }
    public function accounts()
    {

        /*
        |--------------------------------------------------------------------------
        | Connecting Odnoklassniki
        |--------------------------------------------------------------------------
        |
        */

        if (isset($this->request->get['access_token'])  && !isset($this->request->get['user_id']) )  {
            #Response from SMM-posting
            $this->smmposting = new Smmposting($this->getApiToken());
            $response = $this->smmposting->ok_info($this->request->get['access_token']);

            if (isset($response->error)) {
                $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_3') . $response->error;
            } else {
                if (isset($response->user->name) && isset($response->user->id)) {

                    $this->smmposting_model->save_ok($response->user->name, $response->user->id, $this->request->get['access_token']);
                } else {
                    $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_4');
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Connecting Vkontakte
        |--------------------------------------------------------------------------
        |
        */
        if (isset($this->request->get['access_token'])  && isset($this->request->get['user_id']) )  {
            #Response from SMM-posting
            $this->smmposting = new Smmposting($this->getApiToken());
            $response =  $this->smmposting->vk_info($this->request->get['access_token'],$this->request->get['user_id']);
            if (isset($response->error)) {
                $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_5'). $response->error;
            } else {
                if (isset($response->name) && isset($this->request->get['user_id'])) {
                    $this->smmposting_model->save_vk($response->name, $this->request->get['user_id'], $this->request->get['access_token']);
                } else {
                    $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_6');
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Connecting Facebook
        |--------------------------------------------------------------------------
        |
        */
        if (isset($this->request->get['fb_access_token']))  {
            #Response from SMM-posting
            $this->smmposting = new Smmposting($this->getApiToken());
            $response =  $this->smmposting->fb_info($this->request->get['fb_access_token']);
            if (isset($response->error)) {
                $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_7'). $response->error;
            } else {
                if (isset($response->first_name) && isset($response->last_name) && isset($response->id)) {
                    $name = $response->first_name . ' ' . $response->last_name;
                    $fb_user_id = $response->id;
                    $access_token = $this->request->get['fb_access_token'];

                    $this->smmposting_model->save_fb($name, $fb_user_id, $access_token);
                } else {
                    $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_8');
                }
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Connecting Twitter
        |--------------------------------------------------------------------------
        |
        */

        if (isset($this->request->get['tw_auth'])) {
            $oauth_token = $this->request->get['oauth_token'];
            $oauth_verifier = $this->request->get['oauth_verifier'];
            $oauth_token_secret = $this->request->get['oauth_token_secret'];
            #Response from SMM-posting
            $this->smmposting = new Smmposting($this->getApiToken());
            $response = $this->smmposting->tw_info($oauth_token,$oauth_verifier);
            if (isset($response->error)) {
                $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_9'). $response->error;
            } else {
                if (isset($response->screen_name)) {
                    $name = $response->screen_name;
                    $this->smmposting_model->save_tw($name, $oauth_token, $oauth_token_secret);
                } else {
                    $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_10');
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Connecting Tumblr
        |--------------------------------------------------------------------------
        |
        */
        if (isset($this->request->get['tb_auth'])) {
            $oauth_token = $this->request->get['oauth_token'];
            $oauth_verifier = $this->request->get['oauth_verifier'];
            $oauth_token_secret = $this->request->get['oauth_token_secret'];
            #Response from SMM-posting
            $this->smmposting = new Smmposting($this->getApiToken());
            $response = $this->smmposting->tb_info($oauth_token,$oauth_verifier, $oauth_token_secret);

            if (isset($response->error)) {
                $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_11'). $response->error;
            } else {
                if (isset($response->user->name)) {
                    $name = $response->user->name;
                    $this->smmposting_model->save_tb($name, $oauth_token, $oauth_verifier,$oauth_token_secret);
                } else {
                    $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_11');
                }
            }

        }

        /*
         * End Tumblr
         */

        extract($this->load_module_data());

        $data['accounts'] = $this->smmposting_model->getAccounts();
        $data['auth_links'] = Smmposting::getAuthLinks();

        //	for account redirect uri
        $data['server_link'] = get_site_url() . '/wp-admin/admin.php?page=smmposting&route=accounts';

        extract($data);
        require_once( SMMP_PLUGIN_DIR . 'view/accounts.php');
    }
    public function deleteAccount() {


        if( isset($this->request->get['account_id']) ) {
            $res = $this->smmposting_model->deleteAccount($this->request->get['account_id']);
            if ($res) {
                $_SESSION['success'] = $this->getFromLanguage('account_deleted');
            }
        }
        $this->accounts();

    }
    public function addTelegram() {

        if( isset($this->request->post['telegram_token'])) {
            #Response from SMM-posting
            $this->smmposting = new Smmposting($this->getApiToken());
            $response = $this->smmposting->tg_info($this->request->post['telegram_token']);

            if (isset($response->error)) {
                $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_13'). $response->error;
            } else {
                if (isset($response->name)) {
                    $this->smmposting_model->save_tg($response->name,$this->request->post['telegram_token']);
                } else {
                    $_SESSION['error_warning'] = $this->getFromLanguage('smmposting_error_14');
                }
            }
        }

        $this->accounts();
    }
    public function addInstagram() {
        if( isset($this->request->post['instagram_login']) && isset($this->request->post['instagram_password'])) {
            #Response from SMM-posting
            $this->smmposting = new Smmposting($this->getApiToken());
            $response =  $this->smmposting->ig_info($this->request->post['instagram_login'],$this->request->post['instagram_password']);
            if (isset($response->success)) {
                $this->smmposting_model->save_ig($this->request->post['instagram_login'], $this->request->post['instagram_password']);
            } else {
                $_SESSION['error_warning'] = isset($response->error) ? $response->error : $this->getFromLanguage('smmposting_error_15');
            }
        }

        $this->accounts();
    }

    /*
     * Posts Page
     */
    public function posts(){
        $results = $this->smmposting_model->getPosts();
        $posts = [];
        foreach ($results as $result) {
            $posts[] = array(
                'post_id' => $result['post_id'],
                'project_id' => $result['project_id'],
                'project_name' => $this->smmposting_model->getProjectName($result['project_id']),
                'image' => $this->smmposting_model->getFirstImage($result['post_id']),
                'content' => nl2br(substr(html_entity_decode($result['content']), 0, 250)),
                'status' => $result['status'],
                'vkontakte' => $result['vkontakte'],
                'telegram' => $result['telegram'],
                'instagram' => $result['instagram'],
                'odnoklassniki' => $result['odnoklassniki'],
                'facebook' 	  => $result['facebook'],
                'tg_download' => $result['tg_download'],
                'vk_download' => $result['vk_download'],
                'ok_download' => $result['ok_download'],
                'ig_download' => $result['ig_download'],
                'fb_download' => $result['fb_download'],
                'date_public' => date('d.m.y', strtotime($result['date_public'])),
                'time_public' => date('H:i', strtotime($result['time_public'])),
            );
        }

        extract($this->load_module_data());
        require_once( SMMP_PLUGIN_DIR . 'view/posts.php');
    }

    public function post()
    {
        extract($this->load_module_data());

        $data['post'] = isset($this->request->get['id']) ? $this->smmposting_model->getPost( (int)$this->request->get['id'] ) : null;
        $data['images'] = isset($this->request->get['id']) ? $this->smmposting_model->getImages($this->request->get['id']) : [];
        $data['project_info'] = isset($data['post']['project_id']) ? $this->smmposting_model->getProject($data['post']['project_id']) : null;
        $data['show_ok'] = isset($data['project_info']['ok_account_id']) ? $data['project_info']['ok_account_id'] : false;
        $data['show_vk'] = isset($data['project_info']['vk_account_id']) ? $data['project_info']['vk_account_id'] : false;
        $data['show_tg'] = isset($data['project_info']['tg_account_id']) ? $data['project_info']['tg_account_id'] : false;
        $data['show_ig'] = isset($data['project_info']['ig_account_id']) ? $data['project_info']['ig_account_id'] : false;
        $data['show_fb'] = isset($data['project_info']['fb_account_id']) ? $data['project_info']['fb_account_id'] : false;
        $data['show_tb'] = isset($data['project_info']['tb_account_id']) ? $data['project_info']['tb_account_id'] : false;
        $data['show_tw'] = isset($data['project_info']['tw_account_id']) ? $data['project_info']['tw_account_id'] : false;

        $data['projects'] = $this->smmposting_model->getProjects();

        if (!isset($this->request->get['id'])) {
            $data['action'] = $action_add_post;
        } else {
            $data['action'] = $action_edit_post . '&id=' . $this->request->get['id'];
        }

        $data['date_today'] = date("Y-m-d");
        $data['date_tomorrow'] = date('Y-m-d', strtotime("+1 day"));
        $data['date_after_tommorrow'] = date('Y-m-d', strtotime("+2 day"));
        $data['status']  = isset($data['post']['status']) ? $data['post']['status'] : 1;

        extract($data);

        require_once( SMMP_PLUGIN_DIR . 'view/post.php');

    }

    public function addPost(){


        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $id = $this->smmposting_model->savePost( $this->request->post );
            $_SESSION['success'] = $this->getFromLanguage('text_success');
        }
        echo '<script language="javascript"> 
          window.location.href = "'.admin_url().'admin.php?page=smmposting&route=posts"
        </script>';
        exit;
    }

    public function editPost(){

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['id'] = $this->request->get['id'];
            $this->smmposting_model->savePost( $this->request->post );
            $_SESSION['success'] = $this->getFromLanguage('text_success');
        }

        echo '<script language="javascript"> 
          window.location.href = "'.admin_url().'admin.php?page=smmposting&route=posts"
        </script>';
        exit;
    }

    public function deletePost() {
        $post_id = isset($this->request->get['id']) ? $this->request->get['id'] : false;
        if ($post_id) {
            $this->smmposting_model->deletePost( $post_id );
        }

        echo '<script language="javascript"> 
          window.location.href = "'.admin_url().'admin.php?page=smmposting&route=posts"
        </script>';
        exit;
    }
    public function project()
    {
        extract($this->load_module_data());
        if (isset($this->request->get['project_id'])) {
            $data['action'] = $action_edit_project . '&project_id='.$this->request->get['project_id'];
            $data['project'] = $this->smmposting_model->getProject($this->request->get['project_id']);
        } else {
            $data['action'] = $action_add_project;
            $data['project'] = false;
        }

        $data['accounts'] = [
            'odnoklassniki'	=> $this->smmposting_model->getAccounts('odnoklassniki'),
            'vkontakte'		=> $this->smmposting_model->getAccounts('vkontakte'),
            'telegram'		=> $this->smmposting_model->getAccounts('telegram'),
            'instagram'		=> $this->smmposting_model->getAccounts('instagram'),
            'facebook'		=> $this->smmposting_model->getAccounts('facebook'),
            'tumblr'		=> $this->smmposting_model->getAccounts('tumblr'),
            'twitter'		=> $this->smmposting_model->getAccounts('twitter'),
        ];

        extract($data);
        require_once( SMMP_PLUGIN_DIR . 'view/project.php');
    }

    public function addProject() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->smmposting_model->addProject( $this->request->post );
            $_SESSION['success'] = $this->getFromLanguage('text_success_project');
        }
        echo '<script language="javascript"> 
          window.location.href = "'.admin_url().'admin.php?page=smmposting&route=projects"
        </script>';
        exit;
    }
    public function editProject() {

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['project_id'] = $this->request->get['project_id'];
            $this->smmposting_model->editProject( $this->request->post );
        }
        echo '<script language="javascript"> 
          window.location.href = "'.admin_url().'admin.php?page=smmposting&route=projects"
        </script>';
        exit;
    }

    public function deleteProject() {
        $project_id = isset($this->request->get['project_id']) ? $this->request->get['project_id'] : false;
        if ($project_id) {
            $this->smmposting_model->deleteProject( $project_id );
        }

        echo '<script language="javascript"> 
          window.location.href = "'.admin_url().'admin.php?page=smmposting&route=projects"
        </script>';
        exit;
    }
    public function projects()
    {
        extract($this->load_module_data());
        $smm_projects = $this->smmposting_model->getProjects();
        require_once( SMMP_PLUGIN_DIR . 'view/projects.php');
    }

    public function settings()
    {
        extract($this->load_module_data());
        require_once( SMMP_PLUGIN_DIR . 'view/settings.php');
    }

    protected function validate() {
        return true;
    }

    private function sanitize($string)
    {
        return sanitize_text_field($string);
    }

}
