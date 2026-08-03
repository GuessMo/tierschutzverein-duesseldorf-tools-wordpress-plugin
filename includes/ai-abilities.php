<?php
// AI Abilities — registers WordPress Abilities (WP 6.9+) for the animals CPT
// so MCP clients can list, read, create and update animals.
// Requires the WordPress MCP Adapter plugin to expose them over MCP.
// Ability definitions live in ai-abilities-definitions.php so the admin
// toggle page (ai-admin-abilities.php) can list them without registering.

if (!defined('ABSPATH')) exit;

require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-callbacks.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-callbacks-extra.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-callbacks-maintenance.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-callbacks-image-sizes.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-callbacks-focus-crops.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-callbacks-seo.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-definitions.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-definitions-extra.php';
require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-definitions-seo.php';

function tsvd_tools_ai_get_all_ability_definitions() {
    return array_merge(
        tsvd_tools_ai_get_ability_definitions(),
        tsvd_tools_ai_get_ability_definitions_extra(),
        tsvd_tools_ai_get_ability_definitions_seo()
    );
}

const TSVD_TOOLS_AI_DISABLED_OPTION = 'tsvd_tools_ai_disabled_abilities';

function tsvd_tools_ai_disabled_abilities() {
    return (array) get_option(TSVD_TOOLS_AI_DISABLED_OPTION, array());
}

function tsvd_tools_ai_is_ability_enabled($name) {
    return !in_array($name, tsvd_tools_ai_disabled_abilities(), true);
}

// Default server capability gate is 'read' (any logged-in user). Tighten to the
// broadest capability our abilities actually need — individual permission_callbacks
// remain the real per-ability gate (tsvd_tools_ai_can_manage_animals/_settings).
add_filter('mcp_adapter_discover_abilities_capability', function () {
    return 'edit_posts';
});
add_filter('mcp_adapter_get_ability_info_capability', function () {
    return 'edit_posts';
});
add_filter('mcp_adapter_execute_ability_capability', function () {
    return 'edit_posts';
});

add_action('wp_abilities_api_categories_init', 'tsvd_tools_ai_register_category');
function tsvd_tools_ai_register_category() {
    if (!function_exists('wp_register_ability_category')) {
        return;
    }
    wp_register_ability_category('tsv-tools-animals', array(
        'label'       => __('TSV Tiere', 'tsv-tools'),
        'description' => __('Lesen und Verwalten von Tieren des Tierschutzvereins.', 'tsv-tools'),
    ));
}

add_action('wp_abilities_api_init', 'tsvd_tools_ai_register_abilities');
function tsvd_tools_ai_register_abilities() {
    if (!function_exists('wp_register_ability')) {
        return;
    }
    foreach (tsvd_tools_ai_get_all_ability_definitions() as $name => $args) {
        if (tsvd_tools_ai_is_ability_enabled($name)) {
            wp_register_ability($name, $args);
        }
    }
}
