<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'tsvd_tools_register_password_reset_page');
function tsvd_tools_register_password_reset_page() {
    add_submenu_page(
        'tsvd-tools',
        'Passwort-Reset',
        'Passwort-Reset',
        'manage_options',
        'tsvd-tools-password-reset',
        'tsvd_tools_render_password_reset_page'
    );
}

function tsvd_tools_render_password_reset_page() {
    $users = get_users(array(
        'orderby' => 'display_name',
        'fields'  => array('ID', 'display_name', 'user_email'),
    ));
    ?>
    <div class="wrap">
        <h1>Passwort-Reset</h1>
        <p>Sendet der gewählten Person die WordPress-Passwort-Reset-Mail mit Link zum Neusetzen.
            Es wird kein Passwort im Klartext versendet. Der Versand erscheint im
            <a href="<?php echo esc_url(admin_url('admin.php?page=tsvd-mailer-log')); ?>">TSVD-Mailer-Log</a>.</p>

        <?php tsvd_tools_password_reset_notice(); ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="tsvd_tools_send_password_reset">
            <?php wp_nonce_field('tsvd_tools_password_reset'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="tsvd-pr-user">Benutzer</label></th>
                    <td>
                        <select name="user_id" id="tsvd-pr-user" required>
                            <option value="">— Benutzer wählen —</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo (int) $user->ID; ?>">
                                    <?php echo esc_html($user->display_name . ' (' . $user->user_email . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button('Passwort-Reset-Mail senden'); ?>
        </form>
    </div>
    <?php
}

function tsvd_tools_password_reset_notice() {
    if (isset($_GET['pr_sent'])) {
        printf(
            '<div class="notice notice-success"><p>Reset-Mail an %s gesendet — Status im Mailer-Log prüfen.</p></div>',
            esc_html(sanitize_email(wp_unslash($_GET['pr_sent'])))
        );
    }
    if (isset($_GET['pr_error'])) {
        printf(
            '<div class="notice notice-error"><p>Fehler: %s</p></div>',
            esc_html(sanitize_text_field(wp_unslash($_GET['pr_error'])))
        );
    }
}

add_action('admin_post_tsvd_tools_send_password_reset', 'tsvd_tools_handle_password_reset');
function tsvd_tools_handle_password_reset() {
    if (!current_user_can('manage_options')) wp_die('Keine Berechtigung.');
    check_admin_referer('tsvd_tools_password_reset');

    $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $user    = $user_id ? get_userdata($user_id) : false;

    if (!$user) {
        tsvd_tools_password_reset_redirect(array('pr_error' => 'Benutzer nicht gefunden.'));
    }

    $result = tsvd_tools_send_reset_mail($user);
    if (is_wp_error($result)) {
        tsvd_tools_password_reset_redirect(array('pr_error' => $result->get_error_message()));
    }

    tsvd_tools_password_reset_redirect(array('pr_sent' => $user->user_email));
}

function tsvd_tools_send_reset_mail($user) {
    $key = get_password_reset_key($user);
    if (is_wp_error($key)) return $key;

    $reset_url = network_site_url(
        'wp-login.php?action=rp&key=' . rawurlencode($key) . '&login=' . rawurlencode($user->user_login),
        'login'
    );

    $site    = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $subject = sprintf('[%s] Passwort zurücksetzen', $site);
    $message = sprintf(
        "Hallo %s,\n\nfür dein Konto auf %s wurde ein Passwort-Reset angefordert.\n\n"
        . "Setze dein neues Passwort über diesen Link:\n%s\n\n"
        . "Falls du das nicht angefordert hast, ignoriere diese E-Mail.\n",
        $user->display_name,
        $site,
        $reset_url
    );

    if (!wp_mail($user->user_email, $subject, $message)) {
        return new WP_Error('mail_failed', 'wp_mail() lieferte false (siehe Mailer-Log).');
    }
    return true;
}

function tsvd_tools_password_reset_redirect($args) {
    $args['page'] = 'tsvd-tools-password-reset';
    wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
    exit;
}
