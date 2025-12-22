<?php

defined('ABSPATH') or die;

/*
Plugin Name: Fluent Boards Modes
Description: Productivity modes for Fluent Boards including Focus Mode with Pomodoro timer, task management, and notes, plus Para Mode dashboard
Version: 1.0.0
Author: Your Name
Author URI: 
Plugin URI: 
License: GPLv2 or later
Text Domain: fluent-boards-modes
Domain Path: /language
*/

// Define constants
if (!defined('FLUENT_BOARDS_MODES_VERSION')) {
    define('FLUENT_BOARDS_MODES_VERSION', '1.0.0');
    define('FLUENT_BOARDS_MODES_DB_VERSION', '1.0.0');
    define('FLUENT_BOARDS_MODES_PATH', plugin_dir_path(__FILE__));
    define('FLUENT_BOARDS_MODES_URL', plugin_dir_url(__FILE__));
    define('FLUENT_BOARDS_MODES_FILE_PATH', __FILE__);
}

// Load the application
require_once __DIR__ . '/boot/app.php';
