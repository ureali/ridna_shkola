<?php
/**
 * Plugin Name: Simple Contact Form
 * Description: A lightweight contact form plugin
 * Version: 1.0
 * Author: Stan
 */

// so user can't access directly
if (!defined('ABSPATH')) {
    exit;
}

class Simple_Contact_Form {
    public function __construct() {
        add_action('init', array($this, 'init'));

        add_shortcode('simple_contact', array($this, 'render_contact_form'));

        add_action('admin_menu', array($this, 'add_admin_menu'));

        add_action('admin_init', array($this, 'register_settings'));

        // CSS :D
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function init() {
        if (isset($_POST['simple_contact_submit'])) {
            $this->handle_form_submission();
        }
    }

    public function handle_form_submission() {

        if (!wp_verify_nonce($_POST['contact_nonce'], 'simple_contact_form')) {
            wp_die('Security check failed');
        }

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);
        $appointment = isset($_POST['appointment']) ? sanitize_text_field($_POST['appointment']) : '';

        if (empty($name) || empty($email) || empty($message)) {
            wp_die($this->translate_to_locale('Please fill in all fields', 'Будь ласка, заповніть усі поля'));
        }

        $to = get_option('simple_contact_recipient_email', get_option('admin_email'));
        $subject = get_option('simple_contact_email_subject', 'New Contact Form Submission');

        $body = $this->translate_to_locale("Name", "Ім'я") . ": $name\n";
        $body .= $this->translate_to_locale("Email", "Електронна пошта") . ": $email\n\n";
        $body .= $this->translate_to_locale("Message", "Повідомлення") . ":\n$message";
        $body .= "\n" . $this->translate_to_locale("Preferred Appointment Date", "Бажана дата зустрічі") . ": $appointment\n";

        $headers = array('Content-Type: text/plain; charset=UTF-8');

        wp_mail($to, $subject, $body, $headers);

        if (get_option('simple_contact_store_submissions', '1')) {
            $this->store_submission($name, $email, $message, $appointment);
        }

        wp_redirect(add_query_arg('contact_submitted', 'true', wp_get_referer()));
        exit;
    }

    public function store_submission($name, $email, $message, $appointment=null) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'simple_contact_submissions';

        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'appointment' => $appointment,
                'date' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s')
        );
    }

    public function render_contact_form() {
        ob_start();

        if (isset($_GET['contact_submitted'])) {
            echo '<div class="success-message">' .
                esc_html($this->translate_to_locale(
                    get_option('simple_contact_success_message', 'Thank you for your message. We\'ll be in touch soon!'),
                    'Дякуємо за ваше повідомлення. Ми скоро зв\'яжемось з вами!'
                )) .
                '</div>';
        }

        ?>
        <div class="simple-contact-form">
            <form method="post">
                <?php wp_nonce_field('simple_contact_form', 'contact_nonce'); ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name"><?php echo esc_html($this->translate_to_locale(
                                get_option('simple_contact_name_label', 'Name:'),
                                'Ім\'я:'
                            )); ?></label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><?php echo esc_html($this->translate_to_locale(
                                get_option('simple_contact_email_label', 'Email:'),
                                'Електронна пошта:'
                            )); ?></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="message"><?php echo esc_html($this->translate_to_locale(
                            get_option('simple_contact_message_label', 'Message:'),
                            'Повідомлення:'
                        )); ?></label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>

                <div class="form-group">
                    <label for="appointment"><?php echo esc_html($this->translate_to_locale(
                            get_option('simple_contact_appointment_label', 'Preferred Appointment Date (Optional):'),
                            'Бажана дата зустрічі (необов\'язково):'
                        )); ?></label>
                    <input type="datetime-local" id="appointment" name="appointment">
                </div>


                <div class="submit-button-container">
                    <button type="submit" name="simple_contact_submit" class="submit-button has-primary-background-color has-background wp-element-button">
                        <?php echo esc_html($this->translate_to_locale(
                            get_option('simple_contact_submit_text', 'Send Message'),
                            'Надіслати повідомлення'
                        )); ?>
                    </button>
                </div>

            </form>
        </div>
        <?php

        return ob_get_clean();
    }

    public function add_admin_menu() {
        add_menu_page(
            'Simple Contact Form',
            'Contact Form',
            'manage_options',
            'simple-contact-form',
            array($this, 'render_admin_page'),
            'dashicons-email'
        );
    }

    public function register_settings() {
        register_setting('simple_contact_options', 'simple_contact_recipient_email');
        register_setting('simple_contact_options', 'simple_contact_email_subject');
        register_setting('simple_contact_options', 'simple_contact_success_message');
        register_setting('simple_contact_options', 'simple_contact_store_submissions');
        register_setting('simple_contact_options', 'simple_contact_name_label');
        register_setting('simple_contact_options', 'simple_contact_email_label');
        register_setting('simple_contact_options', 'simple_contact_message_label');
        register_setting('simple_contact_options', 'simple_contact_submit_text');
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h2><?php echo $this->translate_to_locale('Simple Contact Form Settings', 'Налаштування простої контактної форми'); ?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('simple_contact_options');
                do_settings_sections('simple_contact_options');
                ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo $this->translate_to_locale('Recipient Email', 'Електронна пошта отримувача'); ?></th>
                        <td>
                            <input type="email" name="simple_contact_recipient_email"
                                   value="<?php echo esc_attr(get_option('simple_contact_recipient_email', get_option('admin_email'))); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo $this->translate_to_locale('Email Subject', 'Тема листа'); ?></th>
                        <td>
                            <input type="text" name="simple_contact_email_subject"
                                   value="<?php echo esc_attr(get_option('simple_contact_email_subject', 'New Contact Form Submission')); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo $this->translate_to_locale('Success Message', 'Повідомлення про успіх'); ?></th>
                        <td>
                            <textarea name="simple_contact_success_message"><?php
                                echo esc_textarea(get_option('simple_contact_success_message', 'Thank you for your message. We\'ll be in touch soon!'));
                                ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo $this->translate_to_locale('Store Submissions', 'Зберігати подання'); ?></th>
                        <td>
                            <input type="checkbox" name="simple_contact_store_submissions" value="1"
                                <?php checked(1, get_option('simple_contact_store_submissions', '1')); ?>>
                            <?php echo $this->translate_to_locale('Store form submissions in database', 'Зберігати подання форми в базі даних'); ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button($this->translate_to_locale('Save Changes', 'Зберегти зміни')); ?>
            </form>
        </div>
        <?php
    }

    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'simple_contact_submissions';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    message text NOT NULL,
    appointment datetime DEFAULT NULL,
    date datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (id)
) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            'simple-contact-form',
            plugins_url('assets/css/contact-form.css', __FILE__),
            array(),
            '1.0.0'
        );
    }

    // Helper function to check the language and translate if needed
    private function translate_to_locale($english_text, $ukrainian_text) {
        $current_locale = get_locale();
        if ($current_locale === 'ua' || $current_locale === 'uk' || $current_locale === 'uk_UA') {
            return $ukrainian_text;
        }
        return $english_text;
    }
}

$simple_contact_form = new Simple_Contact_Form();

register_activation_hook(__FILE__, array('Simple_Contact_Form', 'activate'));