<?php
/**
 * Plugin Name:       Papa Site
 * Plugin URI:        https://tupimelab.com
 * Description:       Plugin for handling custom changes to Tupimelab 
 * Version:           1.0.0
 * Author:            Bogere Goldsoft
 * Author URI:        https://tupimelab.com
 * Requires PHP:      5.3
 * Text Domain:       papa-site
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 *
 * @package papa-site
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; //deny direct access to the plugin file by hackers
} // End if().

if ( ! class_exists( 'Papa_Site' ) ) {

    /**
     * Main Papa Site class
     */
    final class Papa_Site {



        /**
         * Reference to plugin version
         *
         * @var string
         */
        public $version = '1.0.0';

        /**
         * Plugin options key to store and retrieve settings in WordPress database
         *
         * @var string
         */
        private $options_key = 'papa-site';

        /**
         * Variable that holds the one and only instance of M-Alkhair
         *
         * @var Papa_Site
         */
        private static $_instance = null;

        /**
         * A dependency injection container
         *
         * @var Object
         */
        public $container = null;

        /**
         * A MonoLog log object
         *
         * @var Object
         */
        public $logger = null;

        /**
         * Load the global M-Alkhair instance
         *
         * @return Papa_Site
         */
        public static function get_instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Cloning is forbidden.
         *
         * @since 1.8.0
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'kz-jawards' ), esc_html( $this->version ) );
        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.8.0
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'kz-jawards' ), esc_html( $this->version ) );
        }

        /**
         * Class constructor
         */
        public function __construct() {
            $this->define_constants();
            $this->includes();
        }

        /**
         * Papa Site constants
         */
        private function define_constants() {
            $this->define( 'PAPA_SITE_VERSION', $this->version );
            $this->define( 'PAPA_SITE_SLUG', 'papa-site' );
            $this->define( 'PAPA_SITE_DIR', plugin_dir_path( __FILE__ ) );
            $this->define( 'PAPA_SITE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            $this->define( 'PAPA_SITE_PLUGIN_FILE', __FILE__ );
            $this->define( 'PAPA_SITE_OPTIONS_KEY', $this->options_key );
        }

        /**
         * Define constant if not already set.
         *
         * @param string      $name variable.
         * @param string|bool $value variable.
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * Include required files
         */
        private function includes() {

            require_once PAPA_SITE_DIR . '/vendor/autoload.php';
            //require_once PAPA_SITE_DIR . '/includes/class-autoloader.php';
            //require_once PAPA_SITE_DIR . '/includes/class-installer.php';
            require_once PAPA_SITE_DIR . '/includes/classes/class-book-type.php';
            require_once PAPA_SITE_DIR . '/includes/classes/class-momo-pay-api.php';
            require_once PAPA_SITE_DIR . '/includes/class-hook-registry.php';
        }
    }

    /**
     * Make Papa Site class instance available globally
     */
    function Papa_Site() {
        return Papa_Site::get_instance();
    }

    Papa_Site();
}// End if().
