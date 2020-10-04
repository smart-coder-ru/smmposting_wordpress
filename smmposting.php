<?php
/*
Plugin Name: SMMposting
Plugin URI:  https://smm-posting.ru
Description: Posting in social networks
Version:     1.0.4
Author:      smartcoder, vladgaus
License: GPL2+
*/

define('SMMP_PLUGIN_VERSION', '1.0.3');
define('SMMP_PLUGIN_FILE', __FILE__);
define('SMMP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SMMP_PLUGIN_URL', plugin_dir_url(__FILE__));


//SMMP_PLUGIN_LOADER
require_once(SMMP_PLUGIN_DIR . 'lib/wp/Language.php');
require_once(SMMP_PLUGIN_DIR . 'lib/wp/Loader.php');
require_once(SMMP_PLUGIN_DIR . 'lib/wp/Db.php');
require_once(SMMP_PLUGIN_DIR . 'lib/wp/System.php');
require_once(SMMP_PLUGIN_DIR . 'lib/wp/Request.php');
require_once(SMMP_PLUGIN_DIR . 'lib/wp/Pagination.php');
require_once(SMMP_PLUGIN_DIR . 'lib/wp/Response.php');
require_once(SMMP_PLUGIN_DIR . 'lib/smmposting.php');
require_once(SMMP_PLUGIN_DIR . 'controller/SmmpostingController.php');
require_once(SMMP_PLUGIN_DIR . 'model/SmmpostingModel.php');


$SMMP_load = new SMMP_Loader();
register_uninstall_hook(SMMP_PLUGIN_FILE, 'SMMP_uninstallPlugin');
register_activation_hook(SMMP_PLUGIN_FILE, array($SMMP_load, 'activatePlugin'));

$SMMP_Check = new SMMP_System();
if ($SMMP_Check->check() === true) {
    add_action('init', array($SMMP_load, 'load'));
}

/*
|--------------------------------------------------------------------------
| Register Session
|--------------------------------------------------------------------------
|
*/
add_action('init', function () {
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        if(session_id() == '') {session_start();}
    } else  {
        if (session_status() == PHP_SESSION_NONE) {session_start();}
    }
});
