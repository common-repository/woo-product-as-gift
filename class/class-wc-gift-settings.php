<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}

class WCGR_Gift_Wrapping_Base_Settings {

    protected static $_instance = null;

    /**
     * main init callback loads functions which are required on wordpress init
     */
    public static function instance() {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_menu'));
        add_action('admin_init', array($this, 'register_admin_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'register_media_related_scripts'));
    }

    public function add_settings_menu() {
        add_submenu_page("woocommerce", "Settings", "Gifts Settings", "administrator", "wc-gift-settings", array($this, 'settings_page'));
    }

    public function save_styles($array) {
        $styles_array = [];
        if ( $array["name"][0] != "" ) {
            foreach ( $array["name"] as $kname => $name ) {
                $styles_array[$kname]["name"] = $name;
            }
            foreach ( $array["price"] as $kprice => $price ) {
                $styles_array[$kprice]["price"] = $price;
            }
            foreach ( $array["image"] as $kimage => $image ) {
                $styles_array[$kimage]["image"] = $image;
            }
            update_option("wcgw-wrapping-style", json_encode($styles_array));
        }
    }

    /**
     * loads setting page html
     */
    public function settings_page() {
        
        if ( filter_input(INPUT_POST, "wcgw_submit") ) {
            update_option("wcgw-enable-gift-options", filter_input(INPUT_POST, "wcgw-enable-gift-options"));
            if ( filter_input(INPUT_POST, "wcgw-is-this-gift") ) {
                update_option("wcgw-is-this-gift", filter_input(INPUT_POST, "wcgw-is-this-gift"));
            }
            else {
                update_option("wcgw-is-this-gift", "Is this gift");
            }
            if ( !empty(filter_input(INPUT_POST, "wcgw-gift-detail")) ) {
                update_option("wcgw-gift-detail", filter_input(INPUT_POST, "wcgw-gift-detail"));
            }
            else {
                delete_option("wcgw-gift-detail");
            }
            $wcgw_wrapping_style = (isset($_POST["wcgw-wrapping-style"])) ? filter_var_array(wp_unslash($_POST["wcgw-wrapping-style"])) : false;
            $first_set = (isset($_POST["wcgw-wrapping-style"]["name"][0])) ? wp_kses_post(wp_unslash($_POST["wcgw-wrapping-style"]["name"][0])) : false;

            if ( !empty($first_set) ) {
                $this->save_styles($wcgw_wrapping_style);
            }
            else {
                delete_option("wcgw-wrapping-style");
            }
        }
        if ( filter_input(INPUT_POST, "wcgw_note_submit") ) {
            update_option("wcgw-charge-fee-message", filter_input(INPUT_POST, "wcgw-charge-fee-message"));
            if ( filter_input(INPUT_POST, "wcgw-message-charges") ) {
                update_option("wcgw-message-charges", filter_input(INPUT_POST, "wcgw-message-charges"));
            }
            else {
                delete_option("wcgw-message-charges");
            }
            if ( filter_input(INPUT_POST, "wcgw-add-message") ) {
                update_option("wcgw-add-message", filter_input(INPUT_POST, "wcgw-add-message"));
            }
            else {
                delete_option("wcgw-add-message");
            }
        }
        
        require_once WC_GIFTS_DIR . '/partials/gifts-settings.php';
      
    }

    /**
     * Register all media library related scripts 
     */
    public function register_media_related_scripts() {
        wp_enqueue_media();
    }

    /**
     * register all admin scripts related to this plugin
     */
    public function register_admin_scripts() {
        wp_enqueue_style("wc-gift-admin-style", WC_GIFTS_URL . 'assets/admin/css/style.css', array(), WC_GIFTS_VERSION);
        wp_enqueue_script("wc-gift-admin-script", WC_GIFTS_URL . 'assets/admin/js/script.js', array('jquery'), WC_GIFTS_VERSION);
    }
}

WCGR_Gift_Wrapping_Base_Settings::instance();
if (!class_exists('CodupAds')){
    require_once WC_GIFTS_DIR . '/lib/codupads/codupads.php';
}
new CodupAds();	

