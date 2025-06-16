<?php
namespace WOOCPANamespaceAccordion;
if (!defined( 'ABSPATH')) {
    exit;
}
class woocpaSupport {
    private $api_url = 'https://app.bwdplugins.com/way-of-api/get-api.php?show_key=true';
    private $api_key;
    private $audience_id = 'https://app.bwdplugins.com/way-of-api/get-api.php?show_audience=true';
    private $list_id;
    // For Offer
    private $offer_api_url = 'https://app.bwdplugins.com/way-of-api/offer-api.php?show_alt_text=true&show_url=true&show_img=true&show_is_active=true';  
    private $alt_text;
    private $promotion_url;
    private $promotion_img;
    private $is_active;

    public function __construct() {
        $this->woocpa_fetch_api_key();
        $this->woosb_fetch_offer_notice_data(); // For offer
        add_action( 'admin_notices', [$this,'woocpa_admin_updates_plugin_notice'] );
        add_action('admin_post_handle_woocpa_email_subscription', [$this, 'handle_woocpa_email_subscription']);
		add_action('admin_enqueue_scripts', [$this, 'woosb_all_assets_for_dashboard_admin']);
    }
    
    public function woosb_all_assets_for_dashboard_admin($hook){
		wp_enqueue_style( 'woosb-bwd-plugins-offer-notice',  'https://bwd-globals.netlify.app/bwd-plugins-offer-notice/style.css', null, 'text-domain', 'all' );
		wp_enqueue_script( 'woosb-bwd-plugins-offer-notice',  'https://bwd-globals.netlify.app/bwd-plugins-offer-notice/script.js', ['jquery'], 'text-domain', true );
    }

    public function woocpa_admin_updates_plugin_notice() {
        if ($this->is_active == '1') {
            echo '<div class="bwd-offer-notice notice">';
            echo '<a href="' . esc_url($this->promotion_url) . '" target="_blank">';
            echo '<img src="' . esc_url($this->promotion_img) . '" alt="' . esc_html($this->alt_text) . '" srcset="">';
            echo '</a>';
            echo '<button class="custom-dismiss-button">&times;</button>';
            echo '</div>';
        }
        if (!get_option('woocpa_email_subscription_notice_shown', false)) {
            $admin_email = get_option('admin_email');
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>Thank you for choosing our plugin! We appreciate your trust. <a href="https://bestwpdeveloper.com" target="_blank">Find us..</a></p>';
            echo '<form class="newsletter-form" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="email" name="email" value="' . esc_attr($admin_email) . '" style="display:none" required>';
            echo '<input type="hidden" name="action" value="handle_woocpa_email_subscription">';
            echo '<button type="submit" class="button button-primary woocpa-notice-btn">Hide Notice</button>';
            echo '</form>';
            echo '</div>';
        }
    }
    
    private function woocpa_fetch_api_key() {
        $response = file_get_contents($this->api_url);
        $data = json_decode($response, true);
        if (isset($data['api_key'])) {
            $this->api_key = $data['api_key'];
        } else {
            // echo "Error: API key not found.";
        }
        $responseID = file_get_contents($this->audience_id);
        $dataID = json_decode($responseID, true);
        if (isset($dataID['audience_id'])) {
            $this->list_id = $dataID['audience_id'];
        } else {
            // echo "Error: Audience id not found.";
        }
    }

    private function woosb_fetch_offer_notice_data() {
        $response = wp_remote_get($this->offer_api_url);
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return;
        }
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (isset($data['alt_text'], $data['promotion_url'], $data['promotion_img'], $data['is_active'])) {
            $this->alt_text = $data['alt_text'];
            $this->promotion_url = $data['promotion_url'];
            $this->promotion_img = $data['promotion_img'];
            $this->is_active = $data['is_active'];
        } else {
            // error_log("Invalid data received from API. Expected keys are missing.");
        }
    }

    public function handle_woocpa_email_subscription() {
        if (isset($_POST['email']) && is_email($_POST['email'])) {
            $email = sanitize_email($_POST['email']);
            $this->add_to_mailchimp($email);
            update_option('woocpa_email_subscription_notice_shown', true);
            wp_safe_redirect(admin_url('#'));
            exit;
        } else {
            wp_safe_redirect(admin_url('#'));
            exit;
        }
    }

    private function add_to_mailchimp($email) {
        $data_center = substr($this->api_key, strpos($this->api_key, '-') + 1);
        
        $url = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $this->list_id . '/members/';
        
        $data = array(
            'email_address' => $email,
            'status'        => 'subscribed',
        );
        
        $json_data = json_encode($data);
        
        $args = array(
            'body'        => $json_data,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode('user:' . $this->api_key),
                'Content-Type'  => 'application/json',
            ),
        );
        
        $response = wp_remote_post($url, $args);
        
        if (is_wp_error($response)) {
            error_log('Mailchimp error: ' . $response->get_error_message());
        }
    }

}
new woocpaSupport();
