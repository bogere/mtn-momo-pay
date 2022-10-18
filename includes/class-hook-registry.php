<?php


/**
 * Hook registry
 *
 * @category   Components
 * @package    papa-site
 * @author     Bogere Goldsoft
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       tupimelab.com
 * @since      1.0.0
 */

namespace Papa\Site;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Geberal hook registry
 */
class Hook_Registry {

    /**
     * Class constructor
     */
    public function __construct() {
        $this->add_hooks();
    }

    /**
     * Add all hooks
     */
    private function add_hooks() {
         //initiate the  custom post type for the book.
        
        //Enqueue Styles and Scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'papa_load_custom_scripts' ] );
        add_action('wp_ajax_reset_password_action', [$this, 'papa_handle_reset_password']);
        //add_action('airi_footer', [$this, 'papa_footer_credits']);
        //add_action('init', [$this, 'activate_book_post_type']);
        add_action('wp_ajax_pay_employees_action', [$this, 'papa_pay_the_employees']);
        add_action('wp_ajax_nopriv_pay_employees_action', [$this, 'papa_pay_the_employees']);
        //add_action('init', [new BookPostType(), 'init']);
        add_action('wp_ajax_remit_action', [$this, 'papa_remit_the_fund']);
        add_action('wp_ajax_nopriv_remit_action', [$this, 'papa_remit_the_fund']);
        //filters
        add_filter( 'woocommerce_checkout_fields' , [new MoMoPAYAPI(), 'papa_remove_checkout_fields'] ); 

    }


    public function papa_load_custom_scripts (){
        wp_enqueue_style('papa-site-css', PAPA_SITE_PLUGIN_URL.'assets/css/papa-style.css' );
        wp_enqueue_script('papa-jquery', PAPA_SITE_PLUGIN_URL.'assets/js/jquery.js');
        wp_enqueue_script('papa-site-js', PAPA_SITE_PLUGIN_URL.'assets/js/papa-site.js', array('papa-jquery'));

        wp_localize_script('papa-site-js', 'siteData',
            array( 
                  'ajaxurl' => admin_url( 'admin-ajax.php' ),
                  'data_var_1' => 'value 1',
                  'data_var_2' => 'value 2',
            )
        );
    }

    //customising the footer of the airi theme.. so that i do not see.
    /**
     * Footer credits... ref --> airi_footer_credits()
    */
    public function papa_footer_credits(){
        ?>
        <div class="site-info col-md-12"><?php
				printf( esc_html__( 'Handcrafted with love  by %s', 'airi' ), 'Bogere Goldsoft' ); ?>
        </div>

        <?php
    }

    public function papa_handle_reset_password(){
        $response = array();
        if (isset($_POST['answer'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            //$repeatPassword = $_POST['repeat-password'];
            $userToken = $_POST['reset-token'];

            if (empty($email) || empty($password)  || empty($userToken)) {
                # code..
                $response = array(
                    'success' => false,
                    'message' => 'Some input field are empty'
                 );
 
                 wp_send_json_error($response);  
            }

            if ($_POST['answer'] != '4') {
                $response = array(
                    'success'=>false,
                    'message' => 'You are not human !!!'
                );

                wp_send_json_error($response);
            }

            if ( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
                $response = array(
                    'success' => false,
                    'message' => 'Invalid email address are not allowed'
                );
                wp_send_json_error($response);
            }

            //make the actual API call to reset password.
            $url = 'http://localhost:3040/resetPassword';
            $data = array(
              'email'=> $email,
              'token'=> $userToken,
              'password'=> $password
            );
            $postResponse = wp_remote_post($url,array(
                'body'=> $data,
                'headers'=> array(
                    'Content-Type' => 'application/json'
                )
            ));

            if (is_wp_error($postResponse)) {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to update the user password'
                );
                wp_send_json_error($response);
            } else {
                $response = array(
                    'success' =>true,
                    'message' => 'Your message sent successfully'
                );
                wp_send_json_success($response);
            }
            

        }
    }

    public function activate_book_post_type(){
        $book = new BookPostType();
        $book->papa_register_book_post_type();
    }

    /**
     * Demo for disbursement API for MTN MoMo PAY
     */

    public function papa_pay_the_employees(){
        $MoMoPay = new MoMoPAYAPI();
        $MoMoPay->depositFundsToEmployees();
    }

    /**
     * Demo for remitting funds for MTN MoMo PAY
     */
    public function papa_remit_the_fund(){
        $MoMoPay = new MoMoPAYAPI();
        $MoMoPay->remitFundsToPayeeAccount();
    }
    

}

new Hook_Registry();
