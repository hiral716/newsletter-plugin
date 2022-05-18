<?php
/*
Plugin Name: Newsletter Plugin
Plugin URI: http://example.com
Description: Simple WordPress Newsletter Form
Version: 1.0
Author: Falcon Solutions
Author URI: https://falconsolutions.co/
*/

function html_form_code() {
	// include styles
	wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css'); 
	wp_enqueue_style('custom_css', plugin_dir_url( __FILE__ ).'/css/custom.css'); 
	
	// include scripts
	wp_enqueue_script('jquery-3.3.1-js', 'https://code.jquery.com/jquery-3.3.1.min.js', array('jquery'), false, true);
	wp_enqueue_script('bootstrap-js', 'https://getbootstrap.com/docs/4.1/dist/js/bootstrap.min.js', array('jquery'), false, true);
	wp_enqueue_script('custom-js', plugin_dir_url( __FILE__ ).'/js/custom.js', array('jquery'), false, true);
	wp_localize_script('custom-js', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));	
	
	// html to display newsletter form
	$html = '';
	$html .= '<div id="myModal" class="modal fade newsletter-modal-outer">';
		$html .= '<div class="modal-dialog modal-dialog-centered" role="document">';
			$html .= '<div class="modal-content">';
				$html .= '<div class="modal-body">';
					$html .= '<div class="modal-body">';
						$html .= '<div class="newsletter-form">';
							$html .= '<form method="post" class="newsletter_registration">';
							$html .= '<h2 class="text_postions">Prunderground newsletter</h2>';
							$html .= '<p>';
							$html .= '<input type="text" name="cf-name" id="cf_name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" placeholder="Your Name"/>';
							$html .= '</p>';
							$html .= '<p>';
							$html .= '<input type="email" name="cf-email" id="cf_email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" placeholder="Email Address"/>';
							$html .= '</p>';
							$html .= '<p><input class="btn btn-submit" type="button" name="cf-submitted" id="submitted" value="Signup"></p>';					
							$html .= '<div class="cf-error-message text-center"></div>';
							$html .= '</form>';
						$html .= '</div>';
						$html .= '<div class="cf-success-message text-center" style="display:none;">';
							$html .= '<p>Thanks for subscribing!</p>';
							$html .= '<p>We will keep you posted on the latest updates.</p>';
						$html .= '</div>';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';
	$html .= '</div>';
	echo $html;
}
// create newsletter table in database on plugin activation
register_activation_hook ( __FILE__, 'on_activate' );

function on_activate() {
    global $wpdb;
    $create_table_query = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}newsletter` (
              `id` int NOT NULL AUTO_INCREMENT,
              `name` text NOT NULL,
              `email` text NOT NULL,
			  PRIMARY KEY (id)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
    ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $create_table_query );
}

// delete newsletter table from database on plugin deactivation
register_deactivation_hook( __FILE__, 'on_deactivate' );
function on_deactivate() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'newsletter';
     $sql = "DROP TABLE IF EXISTS $table_name";
     $wpdb->query($sql);
}   

// admin ajax call to save the user details
add_action('wp_ajax_nopriv_my_action', 'my_action_callback' );
add_action('wp_ajax_my_action', 'my_action_callback');
function my_action_callback() {
	global $wpdb;	
	if(isset($_POST['type']) && $_POST['type'] == 'add') {
		global $wpdb;
		
		$name = $_POST['name'];
		$email = $_POST['email'];
		
		$checkIfExists = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}newsletter WHERE email = '$email'");

		if ($checkIfExists == NULL) {

			$userdata = array(
				'name'    =>  $name,
				'email'    =>  $email,
			);
			$wpdb->insert( $wpdb->prefix . 'newsletter', $userdata );
			
			$data['status'] = 200;
		} else {
			$data['status'] = 300;
			$data['message'] = "Already subscribed.";
		}
		echo json_encode($data);
	}
    die(); // this is required to return a proper result
}

// shortcode to display newsletter form to frount end
function cf_shortcode() {
	ob_start();
	
	html_form_code();

	return ob_get_clean();
}

add_shortcode( 'neswletter_form', 'cf_shortcode' );
?>