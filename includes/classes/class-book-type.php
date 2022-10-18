<?php

/**
 * Book PostType
 *
 * @category   Components
 * @package    papa-site
 * @author     Bogere Goldsoft
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       tupimelab.com
 * @since      1.0.0
 * Reference - https://kinsta.com/blog/wordpress-custom-post-types/
 */

namespace Papa\Site;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 *  Custom Post Type
 */
class BookPostType {
    
    /**
     * Class constructor
     */
    //public function __construct() {
    //    $this->add_hooks();
    //}

    public function init(){
        $this->papa_register_book_post_type();
    }

    /**
     * Register the custom post type for books
     */
    public function  papa_register_book_post_type(){

        /*
        * Defining the Labels for Your Custom Post Type
        *  https://developer.wordpress.org/reference/functions/register_post_type/
        * I’m using internationalization in my labels so they will be translated to the local language for users.
        */
        $labels = array(
            'name' => __( 'Books', 'papa-site' ), 
            'singular_name' => __( 'Book', 'papa-site' ),
            'add_new' => __( 'New Book', 'papa-site' ),
            'add_new_item' => __( 'Add New Book', 'papa-site' ),
            'edit_item' => __( 'Edit Book', 'papa-site' ),
            'new_item' => __( 'New Book', 'papa-site' ),
            'view_item' => __( 'View Books', 'papa-site' ),
            'search_items' => __( 'Search Books', 'papa-site' ),
            'not_found' =>  __( 'No Books Found', 'papa-site' ),
            'not_found_in_trash' => __( 'No Books found in Trash', 'papa-site' ),
        );

        //Defining the Arguments for Your Custom Post Type
        $args = array(
            'labels' => $labels,
            'has_archive' => true, //enables a post type archive for the custom post type. default is false
            'public' => true,//enables the post type to be included in search results and in custom queries. default is false
            // hierrachical is true ==> then the post type will behave like pages, with a hierarchy possible and parent and child posts of any post of your post type
            // hierrachical is false ==>  it’ll behave like posts, without a hierarchy. 
            'hierarchical' => false,
            //defines a number of features of post types that you can have this post type 
            //support. I like to ensure that features such as featured images and custom fields are turned on.
            'supports' => array(
              'title',
              'editor',
              'excerpt',
              'custom-fields',
              'thumbnail',
              'page-attributes'
            ),
            //defines the existing taxonomies that apply to this post type. 
            //if using custom taxonomy like event-type or product, first register the taxonomy before use
            'taxonomies' => 'category',
            //I’m going to give the taxonomy a name that’s different from what I want to use for its slug.
            'rewrite'   => array( 'slug' => 'book' ), //otherwise by default is supposed to be false
            //ensures that the post type is available to the REST API and the Gutenberg interface. 
            //It defaults to false which makes no sense to me – I want all my post types to use the same editing interface!
            'show_in_rest' => true //if someone to edit ur CPT with guntenberg block editor..
        );
        


        //This registers the ‘kinsta_book’ post type, with the arguments we’ve already defined.
        register_post_type('papa_book',$args);
    }


}