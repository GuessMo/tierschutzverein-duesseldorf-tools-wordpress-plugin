<?php
if (!defined('ABSPATH')) exit;

add_action('admin_head', 'tsvd_dark_mode_print_early_script', 1);
function tsvd_dark_mode_print_early_script() {
    ?>
<script>
(function() {
    var stored = localStorage.getItem('tsvd_admin_theme') || 'system';
    var root = document.documentElement;
    if (stored === 'dark') {
        root.setAttribute('data-theme', 'dark');
    } else if (stored === 'light') {
        root.setAttribute('data-theme', 'light');
    } else {
        root.removeAttribute('data-theme');
    }
})();
</script>
    <?php
}

function tsvd_dark_mode_asset_version($relative_path) {
    $file = TSVD_TOOLS_DIR . $relative_path;
    return file_exists($file) ? (string) filemtime($file) : TSVD_TOOLS_VERSION;
}

function tsvd_dark_mode_is_native_screen($hook_suffix) {
    $native_screens = array('about.php', 'credits.php', 'freedoms.php', 'privacy.php', 'contribute.php');
    return in_array($hook_suffix, $native_screens, true);
}

add_action('admin_enqueue_scripts', 'tsvd_dark_mode_enqueue_assets');
function tsvd_dark_mode_enqueue_assets($hook_suffix) {
    wp_enqueue_style('tsvd-dark-mode', TSVD_TOOLS_URL . 'assets/dark-mode.css', array(), tsvd_dark_mode_asset_version('assets/dark-mode.css'));
    wp_enqueue_style('tsvd-dark-mode-menu', TSVD_TOOLS_URL . 'assets/dark-mode-menu.css', array(), tsvd_dark_mode_asset_version('assets/dark-mode-menu.css'));
    wp_enqueue_script('tsvd-dark-mode', TSVD_TOOLS_URL . 'assets/dark-mode.js', array(), tsvd_dark_mode_asset_version('assets/dark-mode.js'), true);

    if (tsvd_dark_mode_is_native_screen($hook_suffix)) {
        return;
    }

    wp_enqueue_style('tsvd-dark-mode-chrome', TSVD_TOOLS_URL . 'assets/dark-mode-chrome.css', array(), tsvd_dark_mode_asset_version('assets/dark-mode-chrome.css'));
    wp_enqueue_style('tsvd-dark-mode-tables', TSVD_TOOLS_URL . 'assets/dark-mode-tables.css', array('tsvd-dark-mode-chrome'), tsvd_dark_mode_asset_version('assets/dark-mode-tables.css'));
    wp_enqueue_style('tsvd-dark-mode-notices', TSVD_TOOLS_URL . 'assets/dark-mode-notices.css', array('tsvd-dark-mode-chrome'), tsvd_dark_mode_asset_version('assets/dark-mode-notices.css'));
    wp_enqueue_style('tsvd-dark-mode-dashboard', TSVD_TOOLS_URL . 'assets/dark-mode-dashboard.css', array('tsvd-dark-mode-chrome'), tsvd_dark_mode_asset_version('assets/dark-mode-dashboard.css'));
    wp_enqueue_style('tsvd-dark-mode-editor', TSVD_TOOLS_URL . 'assets/dark-mode-editor.css', array('tsvd-dark-mode-chrome'), tsvd_dark_mode_asset_version('assets/dark-mode-editor.css'));
}

add_filter('tiny_mce_before_init', 'tsvd_dark_mode_tinymce_content_css');
add_filter('teeny_mce_before_init', 'tsvd_dark_mode_tinymce_content_css');
function tsvd_dark_mode_tinymce_content_css($mce_init) {
    $css_url = TSVD_TOOLS_URL . 'assets/dark-mode-editor-iframe.css?ver=' . tsvd_dark_mode_asset_version('assets/dark-mode-editor-iframe.css');

    if (empty($mce_init['content_css'])) {
        $mce_init['content_css'] = $css_url;
    } else {
        $mce_init['content_css'] .= ',' . $css_url;
    }

    return $mce_init;
}

add_action('admin_bar_menu', 'tsvd_dark_mode_admin_bar_node', 0);
function tsvd_dark_mode_admin_bar_node($wp_admin_bar) {
    if (!current_user_can('read')) return;

    $wp_admin_bar->add_node(array(
        'id'     => 'tsvd-dark-mode-switch',
        'parent' => 'top-secondary',
        'title'  => tsvd_dark_mode_switch_markup(),
        'meta'   => array('class' => 'tsvd-dark-mode-node'),
    ));
}

function tsvd_dark_mode_svg_icon($inner) {
    return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
        . ' stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
}

function tsvd_dark_mode_icon($choice) {
    $icons = array(
        'light'  => '<path d="M8 12a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />',
        'dark'   => '<path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454l0 .008" />',
        'system' => '<path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10" /><path d="M7 20h10" /><path d="M9 16v4" /><path d="M15 16v4" />',
    );
    return tsvd_dark_mode_svg_icon($icons[$choice]);
}

function tsvd_dark_mode_switch_markup() {
    ob_start();
    ?>
    <button type="button" class="tsvd-theme-toggle" id="tsvd-theme-toggle" aria-label="<?php esc_attr_e('Farbschema wechseln', 'tsvd-tools'); ?>">
        <span class="tsvd-theme-icon" data-theme-icon="light" hidden><?php echo tsvd_dark_mode_icon('light'); ?></span>
        <span class="tsvd-theme-icon" data-theme-icon="dark" hidden><?php echo tsvd_dark_mode_icon('dark'); ?></span>
        <span class="tsvd-theme-icon" data-theme-icon="system"><?php echo tsvd_dark_mode_icon('system'); ?></span>
    </button>
    <?php
    return (string) ob_get_clean();
}
