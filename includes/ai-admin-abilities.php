<?php
// AI Admin — per-ability enable/disable toggle page.
// Same restricted access as the AI status page (tsvd_tools_ai_user_allowed()).

if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'tsvd_tools_ai_abilities_register_page', 20);
function tsvd_tools_ai_abilities_register_page() {
    if (!tsvd_tools_ai_user_allowed()) return;

    add_submenu_page(
        'tsvd-tools',
        __('AI Abilities', 'tsv-tools'),
        __('AI Abilities', 'tsv-tools'),
        'manage_options',
        'tsvd-tools-ai-abilities',
        'tsvd_tools_ai_abilities_render_page'
    );
}

add_action('admin_init', 'tsvd_tools_ai_abilities_register_setting');
function tsvd_tools_ai_abilities_register_setting() {
    register_setting(
        'tsvd_tools_ai_abilities_group',
        TSVD_TOOLS_AI_DISABLED_OPTION,
        array('sanitize_callback' => 'tsvd_tools_ai_sanitize_disabled_abilities', 'default' => array())
    );
}

function tsvd_tools_ai_sanitize_disabled_abilities($input) {
    $known = array_keys(tsvd_tools_ai_get_ability_definitions());
    $submitted = is_array($input) ? array_map('sanitize_text_field', $input) : array();
    return array_values(array_intersect($known, $submitted));
}

function tsvd_tools_ai_abilities_render_page() {
    if (!tsvd_tools_ai_user_allowed()) {
        wp_die(esc_html__('Kein Zugriff.', 'tsv-tools'));
    }

    $disabled = tsvd_tools_ai_disabled_abilities();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('AI Abilities', 'tsv-tools'); ?></h1>
        <p><?php esc_html_e('Jede Ability einzeln aktivieren/deaktivieren. Deaktivierte Abilities werden nicht registriert — sie sind für MCP-Clients unsichtbar und nicht ausführbar.', 'tsv-tools'); ?></p>

        <form method="post" action="options.php">
            <?php settings_fields('tsvd_tools_ai_abilities_group'); ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width:90px"><?php esc_html_e('Deaktiviert', 'tsv-tools'); ?></th>
                        <th style="width:260px"><?php esc_html_e('Ability', 'tsv-tools'); ?></th>
                        <th><?php esc_html_e('Beschreibung', 'tsv-tools'); ?></th>
                        <th style="width:220px"><?php esc_html_e('Verhalten', 'tsv-tools'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (tsvd_tools_ai_get_ability_definitions() as $name => $def) : ?>
                        <tr>
                            <td>
                                <label>
                                    <input type="checkbox" name="<?php echo esc_attr(TSVD_TOOLS_AI_DISABLED_OPTION); ?>[]"
                                        value="<?php echo esc_attr($name); ?>"
                                        <?php checked(in_array($name, $disabled, true)); ?>>
                                    <span class="screen-reader-text"><?php esc_html_e('Deaktiviert', 'tsv-tools'); ?></span>
                                </label>
                            </td>
                            <td>
                                <strong><?php echo esc_html($def['label']); ?></strong><br>
                                <code><?php echo esc_html($name); ?></code>
                            </td>
                            <td><?php echo esc_html($def['description']); ?></td>
                            <td><?php echo tsvd_tools_ai_render_annotation_badges($def); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function tsvd_tools_ai_render_annotation_badges($def) {
    $annotations = $def['meta']['annotations'] ?? array();
    $badges = array();

    if (!empty($annotations['readonly'])) {
        $badges[] = '<span style="color:#2271b1">' . esc_html__('Nur lesend', 'tsv-tools') . '</span>';
    } else {
        $badges[] = '<span>' . esc_html__('Ändert Daten', 'tsv-tools') . '</span>';
    }

    if (!empty($annotations['destructive'])) {
        $badges[] = '<span style="color:#b32d2e">' . esc_html__('Kann überschreiben/löschen', 'tsv-tools') . '</span>';
    }

    if (isset($annotations['idempotent']) && !empty($annotations['readonly']) === false) {
        $badges[] = '<span>' . ($annotations['idempotent']
            ? esc_html__('Wiederholbar ohne Nebenwirkung', 'tsv-tools')
            : esc_html__('Legt bei jedem Aufruf Neues an', 'tsv-tools')) . '</span>';
    }

    return implode('<br>', $badges);
}
