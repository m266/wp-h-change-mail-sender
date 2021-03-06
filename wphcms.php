<?php
/*
Plugin Name:       WP H-Change Mail Sender
Plugin URI:        https://github.com/m266/wp-h-change-mail-sender
Description:       Ändert die Adresse und E-Mail bei System-Nachrichten
Author:            Hans M. Herbrand
Author URI:        https://www.web266.de
Version:           1.5.1
Date:              2021-05-22
License:           GNU General Public License v2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/m266/wp-h-change-mail-sender
 */

// Externer Zugriff verhindern
defined('ABSPATH') || exit();

//////////////////////////////////////////////////////////////////////////////////////////

// Erinnerung an Git Updater
register_activation_hook( __FILE__, 'wphcms_activate' ); // Funktions-Name anpassen
function wphcms_activate() { // Funktions-Name anpassen
$to = get_option('admin_email');
$subject = 'Plugin "WP H-Change Mail Sender"'; // Plugin-Name anpassen
$message = 'Falls nicht vorhanden:
Bitte das Plugin "Git Updater" hier https://herbrand.org/tutorials/github/git-updater/ herunterladen, installieren und aktivieren, um weiterhin Updates zu erhalten!';
wp_mail($to, $subject, $message );
}

//////////////////////////////////////////////////////////////////////////////////////////

class WPHCMS {
    private $wphcms_options;

    public function __construct() {
        add_action('admin_menu', array($this, 'wphcms_add_plugin_page'));
        add_action('admin_init', array($this, 'wphcms_page_init'));
    }

    public function wphcms_add_plugin_page() {
        add_menu_page(
            'WP H-Change Mail Sender', // page_title
            'WP H-Change MS', // menu_title
            'manage_options', // capability
            'wphcms', // menu_slug
            array($this, 'wphcms_create_admin_page'), // function
            'dashicons-email', // icon_url
            81// position
        );
    }

    public function wphcms_create_admin_page() {
        $this->wphcms_options = get_option('wphcms_option_name');?>

        <div class="wrap">
            <h2>
                    <?php
// Plugin-Name und Versions-Nummer ermitteln
        function wphcms_plugin_name_get_data() {
            $wphcms_plugin_data = get_plugin_data(__FILE__);
            $wphcms_plugin_name = $wphcms_plugin_data['Name'];
            $wphcms_plugin_version = $wphcms_plugin_data['Version'];
            $wphcms_plugin_name_version = $wphcms_plugin_name . " " .
                $wphcms_plugin_version;
            return $wphcms_plugin_name_version;
        }
        $wphcms_plugin_name_version = wphcms_plugin_name_get_data();
        echo $wphcms_plugin_name_version . " > " . "Einstellungen"; // Plugin-Name und Versions-Nummer ausgeben
        ?>
            </h2>
            <div class="card">
            <form method="post" action="options.php">
                <?php
settings_fields('wphcms_option_group');
        do_settings_sections('wphcms-admin');
        submit_button();
        ?>
            </form>
        </div>
        </div>
    <?php }

    public function wphcms_page_init() {
        register_setting(
            'wphcms_option_group', // option_group
            'wphcms_option_name', // option_name
            array($this, 'wphcms_sanitize') // sanitize_callback
        );

        add_settings_section(
            'wphcms_setting_section', // id
            '', // title
            array($this, 'wphcms_section_info'), // callback
            'wphcms-admin' // page
        );

        add_settings_field(
            'sender_0', // id
            'Absender', // title
            array($this, 'sender_0_callback'), // callback
            'wphcms-admin', // page
            'wphcms_setting_section' // section
        );

        add_settings_field(
            'email_1', // id
            'E-Mail-Adresse', // title
            array($this, 'email_1_callback'), // callback
            'wphcms-admin', // page
            'wphcms_setting_section' // section
        );
    }

    public function wphcms_sanitize($input) {
        $sanitary_values = array();
        if (isset($input['sender_0'])) { // Keine PHP/HTML-Tags erlaubt
            $sanitary_values['sender_0'] = strip_tags($input['sender_0']);
        }

        if (isset($input['email_1'])) {
            $sanitary_values['email_1'] = sanitize_text_field(
                $input['email_1']);
        }

        return $sanitary_values;
    }

    public function wphcms_section_info() {
    }

    public function sender_0_callback() {
        printf(

            '<input class="regular-text" type="text" name="wphcms_option_name[sender_0]" id="sender_0" value="%s" placeholder="Absender eingeben" required>',
            isset($this->wphcms_options['sender_0']) ? esc_attr($this
                    ->wphcms_options['sender_0']) : ''
            );
        }

        public function email_1_callback() {
            printf(

                '<input class="regular-text" type="email" name="wphcms_option_name[email_1]" id="email_1" value="%s" placeholder="E-Mail-Adresse eingeben" required>',
                isset($this->wphcms_options['email_1']) ? esc_attr($this
                    ->wphcms_options['email_1']) : ''
            );
        }
    }
    if (is_admin()) {
    $wphcms = new WPHCMS();
}

// * Retrieve this value with:
$wphcms_options = get_option('wphcms_option_name'); // Array of All Options
//$sender_0 = $wphcms_options['sender_0']; // sender Original
if(!empty($wphcms_options['sender_0'])) {  // sender
$sender_0 = $wphcms_options['sender_0'];
}
//$email_1 = $wphcms_options['email_1']; // email Original
if(!empty($wphcms_options['email_1'])) {  // sender
$email_1 = $wphcms_options['email_1'];
}
// Daten für Absender und E-Mail-Adresse vorhanden?
//if ($sender_0 == "" or $email_1 == "") { // Original
if (!empty($sender_0) == "" or !empty($email_1) == "") {
    function wphcms_adress_or_email_empty_notice() {; // Adresse oder E-Mail fehlt

        ?>
    <div class="error notice">  <!-- Wenn ja, Meldung ausgeben -->
        <p><?php _e('Bitte g&uuml;ltige Daten (Absender und E-Mail-Adresse) f&uuml;r das Plugin <b>"WP H-Change Mail Sender"</b> eingeben!');?></p>
    </div>
                        <?php
}
    add_action('admin_notices', 'wphcms_adress_or_email_empty_notice');
} else {
    // E-Mail-Absender & Absender ändern
    function wphcms_name() {
        global $sender_0;
        return $sender_0;
    }
    add_filter('wp_mail_from_name', 'wphcms_name');

    function wphcms_adress() {
        global $email_1;
        return $email_1;
    }
    add_filter('wp_mail_from', 'wphcms_adress');
}
?>