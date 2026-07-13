<?php
/**
 * Plugin Name: TSV Tools
 * Description: Werkzeuge für das Tierschutzverein Düsseldorf WordPress-Projekt: JSON-Tierdaten-Sync, Theme-Datenbank-Migration und mehr.
 * Version: 0.17.0
 * Update URI: false
 * Author: Hersteller.io
 * Text Domain: tsv-tools
 */

if (!defined('ABSPATH')) exit;

define('TSVD_TOOLS_VERSION', '0.17.0');
define('TSVD_TOOLS_DIR', plugin_dir_path(__FILE__));
define('TSVD_TOOLS_URL', plugin_dir_url(__FILE__));

// Bundled MCP Adapter (Composer) — exposes the registered abilities over MCP,
// so no separate plugin install is required on the server. Boot via Composer's
// autoloader + Plugin::instance() directly (the package's own plugin-entry file
// relies on its internal autoloader, which is unreliable when used as a library).
$tsvd_tools_autoload = TSVD_TOOLS_DIR . 'vendor/autoload.php';
if (is_readable($tsvd_tools_autoload)) {
    require_once $tsvd_tools_autoload;
    if (!defined('WP_MCP_DIR')) {
        define('WP_MCP_DIR', TSVD_TOOLS_DIR . 'vendor/wordpress/mcp-adapter/');
    }
    if (!defined('WP_MCP_VERSION')) {
        define('WP_MCP_VERSION', '0.5.0');
    }
    if (class_exists('WP\\MCP\\Plugin')) {
        \WP\MCP\Plugin::instance();
    }
}

require_once TSVD_TOOLS_DIR . 'includes/admin.php';
require_once TSVD_TOOLS_DIR . 'includes/password-reset.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-admin.php';
require_once TSVD_TOOLS_DIR . 'includes/backups.php';