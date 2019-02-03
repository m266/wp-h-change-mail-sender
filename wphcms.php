<?php
/*
Plugin Name:       WP H-Change Mail Sender
Plugin URI:        https://github.com/m266/wp-h-change-mail-sender
Description:       Aendert die Adresse und E-Mail bei System-Nachrichten
Author:            Hans M. Herbrand
Author URI:        https://www.web266.de
Version:           1.1
Date:              2019-02-03
Credits:           Fork from https://wordpress.org/plugins/cb-change-mail-sender/
License:           GNU General Public License v2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/m266/wp-h-change-mail-sender
 */
// Externer Zugriff verhindern
defined('ABSPATH') || exit();

// Only Admins
if (is_admin()) {

// Plugin defined, GitHub einbinden (Plugin-Name im Meldungstext anpassen)
require_once 'inc/wphcms_plugin_defined_github.php';

// Definitionen
// Prefix Variable: $wphcms_
// Prefix Function: wphcms_
$title_wphcms = "WP H-Change Mail Sender 1.1" . " > " . "Einstellungen"; // Plugin-Name und Versions-Nummer (Zeile 29)
function wphcms_sender_register() {
    global $title_wphcms;
    global $wphcms_menu_name_version;
    add_settings_section('wphcms_sender_section', $title_wphcms, 'wphcms_sender_text', 'wphcms_sender');
    add_settings_field('wphcms_sender_id', 'Absender', 'wphcms_sender_function', 'wphcms_sender', 'wphcms_sender_section');
    register_setting('wphcms_sender_section', 'wphcms_sender_id');
    add_settings_field('wphcms_sender_email_id', 'E-Mail-Adresse', 'wphcms_sender_email', 'wphcms_sender', 'wphcms_sender_section');
    register_setting('wphcms_sender_section', 'wphcms_sender_email_id');
}
add_action('admin_init', 'wphcms_sender_register');

function wphcms_sender_function() {
    echo '<input name="wphcms_sender_id" type="text" class="regular-text" value="' . get_option('wphcms_sender_id') . '" placeholder="Absender eingeben" required/>';
}
function wphcms_sender_email() {
    echo '<input name="wphcms_sender_email_id" type="email" class="regular-text" value="' . get_option('wphcms_sender_email_id') . '" placeholder="E-Mail-Adresse eingeben" required/>';
}

function wphcms_sender_text() {
    echo '<h4><b>(Das Plugin ist auf <a href="https://web266.de/software/eigene-plugins/wp-h-change-mail-sender/" target="_blank">web266.de</a> detailliert beschrieben)</b></h4>
            <hr>
	<p>Bitte g&uuml;ltige Daten f&uuml;r Absender und E-Mail-Adresse eingeben und die Änderungen speichern:</p>';
}

// Menu title
function wphcms_sender_menu() {
    add_menu_page('WP H-Change Mail Sender Options', 'WP H-Change MS', 'manage_options', 'wphcms_sender', 'wphcms_sender_output', 'dashicons-email');
}
add_action('admin_menu', 'wphcms_sender_menu');

function wphcms_sender_output() {
    ?>
    <?php settings_errors();?>
    <form action="options.php" method="POST">
        <?php do_settings_sections('wphcms_sender');?>
        <?php settings_fields('wphcms_sender_section');?>
        <?php submit_button();?>
    </form>
<?php }

// Change the default wordpress@ email address
add_filter('wp_mail_from', 'wphcms_new_mail_from');
add_filter('wp_mail_from_name', 'wphcms_new_mail_from_name');

function wphcms_new_mail_from($old) {
    return get_option('wphcms_sender_email_id');
}
function wphcms_new_mail_from_name($old) {
    return get_option('wphcms_sender_id');
}

// Daten für Absender und E-Mail-Adresse vorhanden?
$wphcms_adress_empty = get_option('wphcms_sender_id');
$wphcms_email_empty = get_option('wphcms_sender_email_id');
if ($wphcms_adress_empty == "" or $wphcms_email_empty == "") {
	    function wphcms_adress_or_email_empty_notice() {; // Adresse oder E-Mail fehlt
        ?>
    <div class="error notice">  <!-- Wenn ja, Meldung ausgeben -->
        <p><?php _e('Bitte g&uuml;ltige Daten (Absender und E-Mail-Adresse) f&uuml;r das Plugin <b>"WP H-Change Mail Sender"</b> eingeben!');?></p>
    </div>
                        <?php
}
    add_action('admin_notices', 'wphcms_adress_or_email_empty_notice');
}
}	
?>