<?php
/**
 * Plugin Name: WooCommerce Product as Gift
 * Description: This plugin gives you the ability to add gift wrapping on your order.
 * Version: 1.1.1.8
 * Author: Codup.io
 * Author URI: http://codup.io
 * Requires at least: 4.4
 * Tested up to: 6.0
 * 
 * Text Domain: product-as-gift
 * Domain Path: /languages
 * 
 * @package WooCommerce
 * @category Core
 * @author Codup.io
 */
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}
if ( !defined('WC_GIFTS_NAME') )
    define('WC_GIFTS_NAME', 'WooCommerce Product as Gift');
    
if ( !defined('WC_GIFTS_SLUG') )
    define('WC_GIFTS_SLUG', 'product-as-gift');

if ( !defined('WC_GIFTS_VERSION') )
    define('WC_GIFTS_VERSION', '1.1.1.3');

if ( !defined('WC_GIFTS_DIR') )
    define('WC_GIFTS_DIR', dirname(__FILE__));

if ( !defined('WC_GIFTS_PATH') )
    define('WC_GIFTS_PATH', __FILE__);

if ( !defined('WC_GIFTS_URL') )
    define('WC_GIFTS_URL', plugin_dir_url(__FILE__));


if ( !class_exists('WCGR_Gifts_Wrapping_Main') ) {

    class WCGR_Gifts_Wrapping_Main {

        protected $session = false;

        public function __construct() {
            add_action('plugins_loaded', array($this, 'init'));
            add_action('wp_head', array($this, 'admin_url_init'));
            add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
            add_action('woocommerce_checkout_update_order_meta', array($this, 'update_order_meta'));
            add_action('woocommerce_before_checkout_form', array($this, 'add_giftwrap_to_checkout'), 10, 0);
            add_action('woocommerce_cart_calculate_fees', array($this, 'cal_giftwrap_fees'), 10, 0);
            add_action('wp_ajax_is_order_is_gift', array($this, 'is_order_is_gift'));
            add_action('wp_ajax_nopriv_is_order_is_gift', array($this, 'is_order_is_gift'));
        }

        public function init_session() {
            if ( session_id() == '' ) {
                ob_start();
                session_start();
                ob_clean();
            }
        }

        public function init() {
            // Checks if WooCommerce is active.
            if ( in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
                if ( !class_exists('WC_Gift_Settings') ) {
                    include_once( 'class/class-wc-gift-settings.php' );
                }
            }
        }

        public function update_order_meta($order_id) {
            $order = wc_get_order($order_id);
            $wrap_data = $this->wc_gift_input_validator($_SESSION['wcgw_wrap_content']);

            if ( isset($wrap_data["wrap"]) ) {
                $wrap = $this->wc_gift_input_validator( $wrap_data["wrap"] );
                update_post_meta($order_id, "wrapping", $wrap);
            }
            if ( isset($wrap_data["message"]) ) {
                $message = $this->wc_gift_input_validator( $wrap_data["message"] );
                update_post_meta($order_id, "message", $message);
            }
        }

        /**
         * add script to admin panel head section
         */
        public function admin_url_init() {
            ?>
            <script language="javascript">
                if (!admin_url) {
                    var admin_url = "<?php echo admin_url('admin-ajax.php'); ?>";
                }
            </script>
            <?php
        }

        public function cal_giftwrap_fees() {
            $this->init_session();
            if(isset($_SESSION['is_this_gift'])){
                if ($_SESSION['is_this_gift'] == "1") {
                    if ( isset($_SESSION['wcgw_cart_fees']) ) {
                        WC()->cart->fees_api()->set_fees($_SESSION['wcgw_cart_fees']);
                    }
                }
                else {
                    unset($_SESSION['wcgw_cart_fees']);
                    unset($_SESSION['wcgw_wrap_content']);
                }
            }
        }

        public function get_style_by_key($key) {
            if ( $key !== "" ) {
                $all_styles = get_option("wcgw-wrapping-style");
                $all_styles = json_decode($all_styles, true);
                if ( !empty($all_styles) ) {
                    return $all_styles[$key];
                }
            }
            return false;
        }

        public function get_styles_names() {
            $styles_names = [];
            $styles = get_option("wcgw-wrapping-style");
            $styles = json_decode($styles, true);
            foreach ( $styles as $key => $style ) {
                array_push($styles_names, $style["name"]);
            }
            return $styles_names;
        }

        public function is_order_is_gift() {
            $this->init_session();
            if ( $_SESSION['is_this_gift'] == "1" ) {
                $_SESSION['is_this_gift'] = "0";
                $this->remove_old_style_from_cart();
                unset($_SESSION['wcgw_cart_fees']);
                unset($_SESSION['wcgw_wrap_content']);
            }
            else {
                $_SESSION['is_this_gift'] = "1";
            }
            wp_die();
        }

        public function remove_old_style_from_cart() {
            $WC_Cart = new WC_Cart();
            $fees = $WC_Cart->get_fees();
            $styles_names = $this->get_styles_names();
            if ( !empty($fees) ) {
                foreach ( $fees as $key => $fee ) {
                    if ( in_array($fee->name, $styles_names) ) {
                        unset($fees[$key]);
                    }
                }
            }
            return WC()->cart->fees_api()->set_fees($fees);
        }

        /**
         * register all scripts related to this plugin
         */
        public function register_scripts() {
            wp_enqueue_script("wcgw-gift-script", WC_GIFTS_URL . 'assets/js/script.js', array('jquery'), WC_GIFTS_VERSION);
            wp_enqueue_style("wcgw-gift-style", WC_GIFTS_URL . 'assets/css/style.css', array(), WC_GIFTS_VERSION);
        }

        public function add_giftwrap_to_checkout() {
            $this->init_session();
            if ( !isset($_SESSION['is_this_gift']) ) {
                $_SESSION['is_this_gift'] = "0";
            }
            if ( get_option("wcgw-enable-gift-options") == "on" ) {
                $selected = false;
                $WC_Cart = new WC_Cart();
                if ( isset($_SESSION["wcgw_notice"]) ) {
                    ?>
                    <p class="wcgw-success-notice"><?php echo esc_attr($_SESSION["wcgw_notice"]); ?></p>
                    <?php
                    unset($_SESSION["wcgw_notice"]);
                }
                ?>
                <div class="woocommerce-info"><input type="checkbox"<?php echo ($_SESSION['is_this_gift'] == "1") ? " checked" : ""; ?> name="wcgw-apply-gift-options" /> <?php echo esc_attr(get_option("wcgw-is-this-gift")); ?></div>
                <?php
                if ( $_SESSION['is_this_gift'] == "1" ) {
                    if ( filter_input(INPUT_POST, "wcgw_select_style") != NULL ) {
                        $_SESSION['wcgw_notice'] = "Gift wrap successfully added into the cart.";
                        $quantity = WC()->cart->get_cart_contents_count();
                        $selected_style_data = $this->get_style_by_key(filter_input(INPUT_POST, "wcgw_select_style"));
                        $this->remove_old_style_from_cart();
                        if ( get_option("wcgw-charge-fee-message") == "on" ) {
                            $WC_Cart->add_fee($selected_style_data["name"], ($selected_style_data["price"] * $quantity) + get_option("wcgw-message-charges"));
                        }
                        else {
                            $WC_Cart->add_fee($selected_style_data["name"], ($selected_style_data["price"] * $quantity));
                        }
                        $_SESSION['wcgw_cart_fees'] = $WC_Cart->get_fees();
                        $_SESSION['wcgw_wrap_content'] = [
                              "wrap" => $selected_style_data["name"],
                              "message" => filter_input(INPUT_POST, "wcgw_add_message_text"),
                        ];
                        wp_redirect(wc_get_checkout_url());
                    }
                    ?>
                    <form method="post" class="wcgw_giftwrap_products">
                        <?php
                        if ( !empty($_SESSION['wcgw_cart_fees']) ) {
                            $selected = reset($_SESSION['wcgw_cart_fees']);
                            $this->giftwrap_products($selected->name);
                        }
                        else {
                            $this->giftwrap_products();
                        }
                        if ( get_option("wcgw-add-message") ) {
                            ?>
                            <p><?php echo esc_attr(get_option("wcgw-add-message")); ?><span class="wcgw_message_charges_message"><?php
                                    if ( get_option("wcgw-message-charges") != "" && get_option("wcgw-message-charges") != "0" && get_option("wcgw-charge-fee-message") ) {
                                        echo " - (" . get_woocommerce_currency_symbol() . get_option("wcgw-message-charges") . " message charges will be charged)";
                                    }
                                    ?></span></p>
                            <?php
                        }
                        else {
                            ?>
                            <p>Add a message with gift<span class="wcgw_message_charges_message"><?php
                                    if ( get_option("wcgw-message-charges") != "" && get_option("wcgw-message-charges") != "0" && get_option("wcgw-charge-fee-message") ) {
                                        echo " - (" . get_woocommerce_currency_symbol() . get_option("wcgw-message-charges") . " message charges will be charged)";
                                    }
                                    ?></span></p>
                            <?php
                        }
                        ?>
                        <textarea name="wcgw_add_message_text" required maxlength="500"><?php echo (filter_input(INPUT_POST, "wcgw_add_message_text")) ? esc_attr(filter_input(INPUT_POST, "wcgw_add_message_text")) : ""; ?></textarea>
                        <br><br>
                        <button type="submit" id="wcgw_giftwrap_submit" class="button btn">Add Gift Wrap</button>
                    </form>
                    <?php
                }
            }
            else {
                $_SESSION['is_this_gift'] = "0";
            }
        }

        public function giftwrap_products($selected = false) {
            $styles = get_option("wcgw-wrapping-style");
            $styles = json_decode($styles);
            if ( !empty($styles) ) {
                ?>
                <ul>
                    <?php
                    foreach ( $styles as $key => $style ) {
    
                        ?>
                        <li>
                            <p><input type="radio" required name="wcgw_select_style"<?php echo ($selected == $style->name) ? " checked" : ""; ?> value="<?php echo esc_attr($key); ?>" /> <?php echo get_woocommerce_currency_symbol() . $style->price; ?> - <?php echo esc_attr($style->name); ?></p>
                            <div class="wcgw_image">
                                <img src="<?php echo wp_get_attachment_image_src($style->image, ["300", "300"])[0]; ?>" width="100%" />
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <br><br>
                <?php
            }
        }
        /**
         * validate and sanitize input field
         */
    public function wc_gift_input_validator($input) {
        if (empty($input)) {
            return;
        }
        $input = sanitize_text_input($input);
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        return $input;
        }

    }

    $WCGR_Gifts_Wrapping_Main = new WCGR_Gifts_Wrapping_Main();
}