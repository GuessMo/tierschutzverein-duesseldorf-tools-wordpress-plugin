<?php
/**
 * Plugin Name: TSV Tools
 * Description: Werkzeuge für das Tierschutzverein Düsseldorf WordPress-Projekt: JSON-Tierdaten-Sync, Theme-Datenbank-Migration und mehr.
 * Version: 0.4.0
 * Update URI: false
 * Author: Hersteller.io
 * Text Domain: tsv-tools
 */

if (!defined('ABSPATH')) exit;

define('TSVD_TOOLS_VERSION', '0.4.0');
define('TSVD_TOOLS_DIR', plugin_dir_path(__FILE__));
define('TSVD_TOOLS_URL', plugin_dir_url(__FILE__));

require_once TSVD_TOOLS_DIR . 'includes/admin.php';
require_once TSVD_TOOLS_DIR . 'includes/password-reset.php';