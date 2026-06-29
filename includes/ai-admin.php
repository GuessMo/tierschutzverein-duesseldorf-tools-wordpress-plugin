<?php
// AI Admin — submenu page for the AI/MCP integration.
// Visible ONLY to the user(s) listed in the TSVD_TOOLS_AI_USERS constant
// (comma-separated logins or user IDs), defined in wp-config.php.

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_allowed_users() {
    if (defined('TSVD_TOOLS_AI_USERS') && trim((string) TSVD_TOOLS_AI_USERS) !== '') {
        return array_filter(array_map('trim', explode(',', TSVD_TOOLS_AI_USERS)));
    }
    return array('webmaster');
}

function tsvd_tools_ai_user_allowed() {
    if (!current_user_can('manage_options')) return false;

    $allowed = tsvd_tools_ai_allowed_users();
    $user = wp_get_current_user();
    return in_array((string) $user->ID, $allowed, true) || in_array($user->user_login, $allowed, true);
}

add_action('admin_menu', 'tsvd_tools_ai_register_admin_page', 20);
function tsvd_tools_ai_register_admin_page() {
    if (!tsvd_tools_ai_user_allowed()) return;

    add_submenu_page(
        'tsvd-tools',
        __('AI', 'tsv-tools'),
        __('AI', 'tsv-tools'),
        'manage_options',
        'tsvd-tools-ai',
        'tsvd_tools_ai_render_admin_page'
    );
}

function tsvd_tools_ai_render_admin_page() {
    if (!tsvd_tools_ai_user_allowed()) {
        wp_die(esc_html__('Kein Zugriff.', 'tsv-tools'));
    }

    $abilities_api = function_exists('wp_register_ability');
    $mcp_routes = tsvd_tools_ai_has_mcp_routes();
    $endpoint = esc_url(rest_url('mcp/mcp-adapter-default-server'));
    $ability_ids = array(
        'tsv-tools/list-animals',
        'tsv-tools/get-animal',
        'tsv-tools/create-animal',
        'tsv-tools/update-animal',
    );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('AI / MCP', 'tsv-tools'); ?></h1>

        <h2><?php esc_html_e('Status', 'tsv-tools'); ?></h2>
        <ul>
            <li>
                <?php esc_html_e('Abilities API (WordPress 6.9+):', 'tsv-tools'); ?>
                <strong><?php echo $abilities_api ? '✓' : '✗'; ?></strong>
            </li>
            <li>
                <?php esc_html_e('MCP-Adapter aktiv:', 'tsv-tools'); ?>
                <strong><?php echo $mcp_routes ? '✓' : '✗'; ?></strong>
                <?php if (!$mcp_routes) : ?>
                    <em>(<?php esc_html_e('Plugin wordpress/mcp-adapter installieren und aktivieren', 'tsv-tools'); ?>)</em>
                <?php endif; ?>
            </li>
        </ul>

        <h2><?php esc_html_e('Registrierte Abilities', 'tsv-tools'); ?></h2>
        <ul>
            <?php foreach ($ability_ids as $id) : ?>
                <li><code><?php echo esc_html($id); ?></code></li>
            <?php endforeach; ?>
        </ul>

        <h2><?php esc_html_e('MCP-Endpoint', 'tsv-tools'); ?></h2>
        <p><code><?php echo $endpoint; ?></code></p>

        <h2><?php esc_html_e('Claude Code anbinden', 'tsv-tools'); ?></h2>
        <p><?php esc_html_e('Auth per OAuth oder Application Password — lokal in der MCP-Config, nie im Klartext teilen.', 'tsv-tools'); ?></p>
        <pre style="background:#f6f7f7;padding:12px;overflow:auto;">claude mcp add tsvd-wp --transport http <?php echo $endpoint; ?></pre>
    </div>
    <?php
}

function tsvd_tools_ai_has_mcp_routes() {
    if (!function_exists('rest_get_server')) return false;
    $routes = rest_get_server()->get_routes();
    foreach (array_keys($routes) as $route) {
        if (strpos($route, '/mcp/') === 0) return true;
    }
    return false;
}
