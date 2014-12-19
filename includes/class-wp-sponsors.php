<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Wp_Sponsors
 * @subpackage Wp_Sponsors/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Sponsors
 * @subpackage Wp_Sponsors/includes
 * @author     Jan Henckens <info@onedge.be>
 */
class Wp_Sponsors {

  /**
   * The loader that's responsible for maintaining and registering all hooks that power
   * the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Wp_Sponsors_Loader    $loader    Maintains and registers all hooks for the plugin.
   */
  protected $loader;

  /**
   * The unique identifier of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $wp_sponsors    The string used to uniquely identify this plugin.
   */
  protected $wp_sponsors;

  /**
   * The current version of the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $version    The current version of the plugin.
   */
  protected $version;

  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the Dashboard and
   * the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function __construct() {

    $this->wp_sponsors = 'wp-sponsors';
    $this->version = '1.0.0';

    $this->load_dependencies();
    $this->set_locale();
    $this->define_admin_hooks();
    $this->define_public_hooks();

  }

  /**
   * Load the required dependencies for this plugin.
   *
   * Include the following files that make up the plugin:
   *
   * - Wp_Sponsors_Loader. Orchestrates the hooks of the plugin.
   * - Wp_Sponsors_i18n. Defines internationalization functionality.
   * - Wp_Sponsors_Admin. Defines all hooks for the dashboard.
   * - Wp_Sponsors_Public. Defines all hooks for the public side of the site.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_dependencies() {

    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-sponsors-loader.php';

    /**
     * The class responsible for defining internationalization functionality
     * of the plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-sponsors-i18n.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-sponsors-widget.php';

    /**
     * The class responsible for defining all actions that occur in the Dashboard.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-sponsors-admin.php';

    /**
     * The class responsible for defining all actions that occur in the public-facing
     * side of the site.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-sponsors-public.php';

    $this->loader = new Wp_Sponsors_Loader();

  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the Wp_Sponsors_i18n class in order to set the domain and to register the hook
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function set_locale() {

    $plugin_i18n = new Wp_Sponsors_i18n();
    $plugin_i18n->set_domain( $this->get_wp_sponsors() );

    $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

  }

  /**
   * Register all of the hooks related to the dashboard functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_admin_hooks() {

    $plugin_admin = new Wp_Sponsors_Admin( $this->get_wp_sponsors(), $this->get_version() );

    $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
    $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

  }

  /**
   * Register all of the hooks related to the public-facing functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_public_hooks() {

    $plugin_public = new Wp_Sponsors_Public( $this->get_wp_sponsors(), $this->get_version() );

    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );


  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   *
   * @since    1.0.0
   */
  public function run() {
    $this->loader->run();
    function sponsors_register() {
            $args = array(
              'public'              => true,
              'label'               => 'Sponsors',
              'public'              => true,
              'exclude_from_search' => true,
              'publicly_queryable'  => false,
              'show_ui'             => true,
              'show_in_menu'        => true,
              'show_in_admin_bar'   => false,
              'menu_position'       => 5,
              'menu_icon'           => 'dashicons-format-image',
              'query_var'           => true,
              'rewrite'             => false,
              'capability_type'     => 'post',
              'has_archive'         => false,
              'hierarchical'        => false,
              'can_export'          => true,
              'query_var'           => false,
              'capability_type'     => 'post',
              'supports'            => array( 'title' ),
              'register_meta_box_cb'=> 'add_sponsor_metabox'
              );
        register_post_type( 'sponsor', $args );
        }
        add_action( 'init', 'sponsors_register' );


        function sponsors_upload_enqueue() {
              wp_enqueue_media();
              wp_localize_script( 'wp_sponsors_img-button', 'wp_sponsors_img',
                  array(
                      'title' => 'Choose or Upload an Image',
                      'button' => 'Use this image',
                  )
              );
              wp_enqueue_script( 'wp_sponsors_img-button' );
            }
        add_action( 'admin_enqueue_scripts', 'sponsors_upload_enqueue' );

        /**
         * Register meta box(es).
         */
        function add_sponsor_metabox() {
            add_meta_box( 'meta-box-id', __( 'Sponsor data', 'wp_sponsors' ), 'sponsor_metabox_url', 'sponsor', 'normal', 'high' );
        }
        add_action( 'add_meta_boxes', 'add_sponsor_metabox' );

        function add_file_meta_box() {
              add_meta_box('meta-box-media', __( 'Media', 'wp_sponsors' ), 'sponsors_metabox_image', 'sponsor', 'normal');
        }
        add_action( 'add_meta_boxes', 'add_file_meta_box' );


        /**
         * Meta box display callback.
         *
         * @param WP_Post $post Current post object.
         */
        function sponsor_metabox_url( $post ) {
            // Display code/markup goes here. Don't forget to include nonces!
            // Noncename needed to verify where the data originated
            echo '<input type="hidden" name="wp_sponsors_nonce" id="wp_sponsors_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
            // Get the url data if its already been entered
            $meta_value = get_post_meta( get_the_ID(), 'wp_sponsors_url', true );
            // Checks and displays the retrieved value
            echo '<input type="url" name="wp_sponsors_url" value="' . $meta_value  . '" class="widefat" />';
        }

        function sponsors_metabox_image( $post ) {
            $meta_value = get_post_meta( $post->ID );
            if(isset($meta_value['wp_sponsors_img'] ) ){ $image = $meta_value['wp_sponsors_img'][0];  }else {$image = "";}
            echo '<input type="hidden" name="wp_sponsors_img_nonce" id="wp_sponsors_img_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
            echo '<div><input type="text" name="wp_sponsors_img" class="widefat" id="wp_sponsors_img" value="'. $image .'" /></div>';
            echo '<div><img src="' . $image .'" width="200px"></div>';
            echo '<input type="button" id="wp_sponsors_img-button" class="button" value="Choose or Upload an Image" /></p>';

        }

        /**
         * Save meta box content.
         *
         * @param int $post_id Post ID
         */
        function sponsors_save_metabox( $post_id ) {
            // verify this came from the our screen and with proper authorization,
            // because save_post can be triggered at other times

            // Checks save status
            $is_autosave = wp_is_post_autosave( $post_id );
            $is_revision = wp_is_post_revision( $post_id );
            $is_valid_nonce = ( isset( $_POST[ 'wp_sponsors_nonce' ] ) && wp_verify_nonce( $_POST[ 'wp_sponsors_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
            // Exits script depending on save status
            if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
                return;
            }
            // Checks for input and sanitizes/saves if needed
            if( isset( $_POST[ 'wp_sponsors_url' ] ) ) {
                update_post_meta( $post_id, 'wp_sponsors_url', sanitize_text_field( $_POST[ 'wp_sponsors_url' ] ) );
            }
            $is_valid_nonce = ( isset( $_POST[ 'wp_sponsors_img_nonce' ] ) && wp_verify_nonce( $_POST[ 'wp_sponsors_img_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
            // Checks for input and sanitizes/saves if needed
            if( isset( $_POST[ 'wp_sponsors_img' ] ) ) {
                update_post_meta( $post_id, 'wp_sponsors_img', $_POST[ 'wp_sponsors_img' ] );
            }
        }
        add_action( 'save_post', 'sponsors_save_metabox' );
  }

  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   *
   * @since     1.0.0
   * @return    string    The name of the plugin.
   */
  public function get_wp_sponsors() {
    return $this->wp_sponsors;
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @since     1.0.0
   * @return    Wp_Sponsors_Loader    Orchestrates the hooks of the plugin.
   */
  public function get_loader() {
    return $this->loader;
  }

  /**
   * Retrieve the version number of the plugin.
   *
   * @since     1.0.0
   * @return    string    The version number of the plugin.
   */
  public function get_version() {
    return $this->version;
  }

}
