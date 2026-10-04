<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Migration Settings
|--------------------------------------------------------------------------
*/
$config['migration_enabled'] = TRUE;
$config['migration_path']    = APP_DIR . 'migrations' . DIRECTORY_SEPARATOR;
$config['migration_table']   = 'migrations';