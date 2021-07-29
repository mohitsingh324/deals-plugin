<?php
/**
 * Plugin Name: Deals
 * Plugin URI: http://example.com
 * Description: The very first plugin that I have ever created.
 * Version: 1.0.0
 * Author: Mohit Singh
 * Author URI: http://example.com
 */

defined( 'ABSPATH' ) or die;

define( 'DL_PLUGIN_FILE', __FILE__ );
define( 'DL_PATH', dirname( DL_PLUGIN_FILE ) . '/' );

/**
 * Enqueue scripts and styles.
 */
function enqueue_deals_scripts() {
	wp_enqueue_style( 'bootstrap-4.5.2', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2' );
	wp_enqueue_style( 'deals-style',plugin_dir_url( __FILE__) .'assets/css/style.css',false, '1.1.2');

	wp_enqueue_script( 'deals', plugin_dir_url( __FILE__) .'assets/js/script.js', array('jquery'), '1.0.5', true );
	wp_enqueue_script( 'bootstrap-4.5.2', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_deals_scripts' );

/**
 * Register deals post type
 */
add_action( 'init', 'create_deals_posttype' );
function create_deals_posttype() {
	/**
	 * Admin only includes.
	 */
	if ( is_admin() ) {			
		include DL_PATH .'lib/CMB2/init.php'; // Metaboxes.
	}
	register_post_type( 'deals',
		array(
			'labels' => array(
				'name' => __( 'Deals' ),
				'singular_name' => __( 'Deal' ),
				'add_new_item' => __( 'Add New Deal' ),
	            'add_new' => __( 'Add New Deal' ),
	            'edit_item' => __( 'Edit Deal' ),
	            'featured_image' => __( 'Company Logo' ),
	            'set_featured_image' => __( 'Upload Company Logo' ),
	            'remove_featured_image' => __( 'Remove Company Logos' ),
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'deals'),
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-welcome-learn-more',
			'supports' => array('title',  'thumbnail','view')
		)
	);
}

function change_default_title( $title ){
	$screen = get_current_screen();
	if ( 'deals' == $screen->post_type ){
		$title = 'Company name';
	}
	return $title;
}
add_filter( 'enter_title_here', 'change_default_title' );


add_action( 'cmb2_admin_init', 'deals_metaboxes' );
/**
 * Define the metabox and field configurations.
 */
function deals_metaboxes() {
 
    $cmb = new_cmb2_box( array(
        'id'            => 'company_details',
        'title'         => __( 'Company Details', 'cmb2' ),
        'object_types'  => array( 'deals' ), // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true, // Show field names on the left
    ) );

    $cmb->add_field( array(
		'name'         => esc_html__( 'Launch Year', 'sermon-manager-for-wordpress' ),
		'id'           => 'launch-year',
		'type'         => 'text_date',
		//'date_format'  => 'Y',
		'autocomplete' => 'off',
        'show_on_cb' => 'cmb2_hide_if_no_cats',
	) );
   
    $cmb->add_field( array(
        'name'       => __( 'Sectors', 'cmb2' ),
        'desc'       => __( 'Valid Sectors: E-commerce, FinTech, Consumer Services, HealthTech, EdTech, AgriTech,
                            Logistics', 'cmb2' ),
        'id'         => 'compnay_sectors',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
    ) );
    $cmb->add_field( array(
        'name'       => __( 'Founders', 'cmb2' ),
        
        'id'         => 'compnay_founders',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
    ) );
    $cmb->add_field( array(
        'name'       => __( 'Deal Stage', 'cmb2' ),
        'desc'       => __( 'Valid stage: Seed, Pre-Series A, Series A, Pre-Series B, Series B, Series C, Series D,
                        Series E, Series F, Late Stage, Debt Financing, Acquisition, IPO', 'cmb2' ),
        'id'         => 'deal-stage',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
    ) );

    $cmb->add_field( array(
        'name'       => __( 'Funding Amount', 'cmb2' ),
        'id'         => 'funding_amount',
        'type'       => 'text', 
        'attributes' => array(
            'type' => 'number',
            'pattern' => '\d*',
            ),
        // 'sanitization_cb' => 'intval',
        // 'escape_cb'       => 'intval', 
    ) );

    $cmb->add_field( array(
        'name'       => __( 'Investors', 'cmb2' ),
        'id'         => 'company_investor',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
    ) );

    $cmb->add_field( array(
        'name'       => __( 'Article Title', 'cmb2' ),
        'id'         => 'article-title',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
    ) );

    $cmb->add_field( array(
        'name' => __( 'Link to Article', 'cmb2' ),
        'desc' => __( 'field description (optional)', 'cmb2' ),
        'id'   => 'article_link_url',
        'type' => 'text_url'        
    ) );

}


add_action('admin_menu' , 'deal_settings');
function deal_settings() {
add_submenu_page('edit.php?post_type=deals', 'Deals Settings',   'Settings', 'edit_posts', basename(__FILE__),'display_deal_settings');

}

function display_deal_settings(){?>
    <h1>Deals Display Settings</h1>

    <form method="POST" >
      <label for="backcolor">Select Card Background Color:</label>
      <input type="color" id="backcolor" name="backcolor" value="<?=(isset($_POST['backcolor']))?$_POST['backcolor']:get_option('deal_card_bg_color')?>"><br><br>
      <?php submit_button(); ?> 
    </form
    <?php
    if(isset($_POST['backcolor'])){
        update_option('deal_card_bg_color',$_POST['backcolor']);
    }
}


add_action( 'init', 'genrate_dynamic_shortcode' );
function genrate_dynamic_shortcode() {
    global $wpdb;    
    $deals = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts where post_type='deals' AND post_status='publish'") );    
    foreach($deals as $deal){
        $id=$deal->ID;
        $cb = function() use ($id) {
        return '<div class="deal-card" style="border: 1px solid;background-color:'.get_option("deal_card_bg_color").' ">
            <div class="row">
                <div class="col-md-3">
                    <img class="company-logo" src="'.get_the_post_thumbnail_url($id).'" alt="logo">
                </div>
                <div class="col-md-9">
                    <h3>'.get_the_title( $id ).'</h3>
                    <p>'.get_post_meta($id,'compnay_sectors',true).'</p>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <p>Launch year: '.get_post_meta($id, 'launch-year', true).'</p>
                    <p>Founders: '.get_post_meta($id,'compnay_founders',true).'</p>
                    <p>Inverstor: '.get_post_meta($id,'company_investor',true).'</p>
                    <p>News: <a href="'.get_post_meta($id,'article_link_url',true).'">'.get_post_meta($id,'article-title',true).'</a></p>
                </div>
            </div>
        </div>';
    };
    add_shortcode( "deal-card-$id", $cb );
    }
    wp_reset_query();
}

add_shortcode( "all-deals", 'display_all_deals');
function display_all_deals(){
    $posts=query_posts(
        array(  'post_type' => 'deals',
                'order'     => 'ASC',
                'meta_key' => (isset($_GET['sort']))?$_GET['sort']:'compnay_sectors',
                'orderby'   => 'meta_value', //or 'meta_value_num'
                'meta_query' => array(
                                    array('key' => (isset($_GET['sort']))?$_GET['sort']:'compnay_sectors'
                                    )
                                )
        )
    );
    echo "<script>jQuery( function() {
  jQuery('#requestFilter').val('".$_GET['sort']."');
})</script>";

    $response='<div class="row">
          <div class="col-md-9">    
          </div> 
          <div class="col-md-3">
            <div class="form-group">
              <label >Sort by:</label>
              <select class="requestFilter" id="requestFilter" >
                <option value="compnay_sectors">Sectors</option>
                <option value="launch-year">Launch Year</option>
                <option value="deal-stage">Deal Stage</option>       
              </select>
            </div>
          </div> 
        </div>
        <div class="row">';
    foreach ($posts as $post) {
        $id=$post->ID;

        $response.='<div class="col-md-4"><div class="deal-card" style="border: 1px solid;background-color:'.get_option("deal_card_bg_color").' ">
            <div class="row">
                <div class="col-md-3">
                    <img class="company-logo" src="'.get_the_post_thumbnail_url($id).'" alt="logo">
                </div>
                <div class="col-md-9">
                    <h3>'.get_the_title( $id ).'</h3>
                    <p>'.get_post_meta($id,'compnay_sectors',true).'</p>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <p>Launch year: '.get_post_meta($id,'launch-year',true).'</p>
                    <p>Founders: '.get_post_meta($id,'compnay_founders',true).'</p>
                    <p>Inverstor: '.get_post_meta($id,'company_investor',true).'</p>
                    <p>News: <a href="'.get_post_meta($id,'article_link_url',true).'">'.get_post_meta($id,'article-title',true).'</a></p>
                </div>
            </div>
        </div></div>';
    }
    $response.="</div>";
    wp_reset_query();
    return $response;
}
