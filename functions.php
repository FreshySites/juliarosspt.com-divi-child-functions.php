<?php
/* Enqueue necessary CSS and JS files for Child Theme */

function fs_theme_enqueue_stuff() {
	// Divi assigns its style.css with this handle
	$parent_handle = 'divi-style'; 
	// Get the current child theme data
	$current_theme = wp_get_theme(); 
	// get the parent version number of the current child theme
	$parent_version = $current_theme->parent()->get('Version'); 
	// get the version number of the current child theme
	$child_version = $current_theme->get('Version'); 
	// we check file date of child stylesheet and script, so we can append to version number string (for cache busting)
	$style_cache_buster = date("YmdHis", filemtime( get_stylesheet_directory() . '/style.css'));
	$script_cache_buster = date("YmdHis", filemtime( get_stylesheet_directory() . '/script.js'));
	// first we pull in the parent theme styles that it needs
	wp_enqueue_style( $parent_handle, get_template_directory_uri() . '/style.css', array(), $parent_version );
	// then we get the child theme style.css file, which is dependent on the parent theme style, then append string of child version and file date
	wp_enqueue_style( 'fs-child-style', get_stylesheet_uri(), array( $parent_handle ), $child_version .'-'. $style_cache_buster );
	// will grab the script file from the child theme directory, and is reliant on jquery and the divi-custom-script (so it comes after that one)
	wp_enqueue_script( 'fs-child-script', get_stylesheet_directory_uri() . '/script.js', array('jquery', 'divi-custom-script'), $child_version .'-'. $script_cache_buster, true);
}
add_action( 'wp_enqueue_scripts', 'fs_theme_enqueue_stuff' );

/* Divi */

// Creates shortcode to allow placing Divi Library module inside of another module's text area. Creates a shortcode to show the Library module.
// https://www.creaweb2b.com/en/how-to-add-a-divi-section-or-module-inside-another-module/
// example usage: [showmodule id="123"]
function showmodule_shortcode($moduleid) {
	extract(shortcode_atts(array('id' =>'*'),$moduleid));   
	return do_shortcode('[et_pb_section global_module="'.$id.'"][/et_pb_section]');
}
add_shortcode('showmodule', 'showmodule_shortcode');

// Adds new admin column to show the shortcode ID in the Divi Library page table
function fs_create_shortcode_column( $columns ) {
	$columns['fs_shortcode_id'] = 'Library Item Shortcode';
	return $columns;
}
// Display shortcode column info on Divi Library page table
function fs_shortcode_column_content( $column, $id ) {
	if( 'fs_shortcode_id' == $column ) {
		echo '<p>[showmodule id="' . $id . '"]</p>';
	}
}
// create new shortcode column in et_pb_layout screen
add_filter( 'manage_et_pb_layout_posts_columns', 'fs_create_shortcode_column', 5 );
// add the shortcode content to the new column
add_action( 'manage_et_pb_layout_posts_custom_column', 'fs_shortcode_column_content', 5, 2 );

/* Gravity Forms */

// This filter can be used to prevent the page from auto jumping to form confirmation upon form submission
// add_filter( 'gform_confirmation_anchor', '__return_false' );

// use a custom Gravity Forms AJAX spinner
add_filter( 'gform_ajax_spinner_url', 'fs_custom_gforms_spinner', 10, 2 );
function fs_custom_gforms_spinner( $image_src, $form ) {
	$upload_dir = wp_upload_dir();
	$lime_spinner_url = $upload_dir['baseurl'] . '/lime-spinner.png';
	return $lime_spinner_url;
}

/*Stripe*/
/*Create customer*/
add_filter( 'gform_stripe_customer_id', function ( $customer_id, $feed, $entry, $form ) {
 
	$feed_name = rgars( $feed, 'meta/feedName' );
   
	gf_stripe()->log_debug( 'gform_stripe_customer_id: Running for Feed: ' . $feed_name );
   
	// Define the names of the feeds you want this code to run.
	$feed_names = array( 'Auth Registration - Summer Tutoring','Auth Test College Coaching', 'Auth Registration - Special Education Coaching', 'Auth test','Auth Registration Academic Coaching','Auth Registration Academic Year Fall Tutoring','Auth Registration - Academic Year Fall Tutoring NEW 2023','Auth Registration - Career Coaching','Auth Registration - College Coaching','Auth Registration - College Student Support Services','Auth Registration - College Transfer Coaching','Auth Registration - Graduate School Coaching','Auth Registration - High School Coaching','Auth Registration - SAT / ACT First Class - 9 Month Preparation Course','Auth Registration - SAT / ACT The Express - 6 Month Preparation Course','Auth Registration - SAT/ACT Boot Camp Summer 2023','Auth Registration - SAT/ACT Preparation Program - Summer - Rising Seniors','Auth Registration - SAT/ACT Preparation Program - Summer/Fall - Rising Seniors','Auth Registration - ACT Jump Start 2023' );
   
	// Abort if the entry was processed by a different feed or it's not a product and services feed.
	if ( rgars( $feed, 'meta/transactionType' ) !== 'product' && ! in_array( $feed_name, $feed_names ) ) {
		return $customer_id;
	}

  if ($feed_name == 'Auth Registration - Special Education Coaching') {
	
	   $customer_name_field_id_fn= rgar( $entry, '114.3');
	   $customer_name_field_id_ln= rgar( $entry, '114.6');
	   $customer_email_stripe=rgar( $entry, '130');
	   
   } else if ( $feed_name === 'Auth test' ) {
    gf_stripe()->log_debug( __METHOD__ . '(): Condition 1 Passed ');
	
	   $customer_name_field_id_fn=rgar( $entry, '86.3');
	   $customer_name_field_id_ln=rgar( $entry, '86.6');
	   $customer_email_stripe=rgar( $entry, '20');
	   
   } else if ($feed_name == 'Auth Registration Academic Coaching') {
	
	   $customer_name_field_id_fn=rgar( $entry, '75.3');
	   $customer_name_field_id_ln=rgar( $entry, '75.6');
	   $customer_email_stripe=rgar( $entry, '84'); 
	   
   } else if ($feed_name == 'Auth Registration Academic Year Fall Tutoring') {
	
	   $customer_name_field_id_fn=rgar( $entry, '72.3');
	   $customer_name_field_id_ln=rgar( $entry, '72.6');
	   $customer_email_stripe=rgar( $entry, '80');
   } 
   else if ($feed_name == 'Auth Registration - Academic Year Fall Tutoring NEW 2023') {
	
	   $customer_name_field_id_fn=rgar( $entry, '72.3');
	   $customer_name_field_id_ln=rgar( $entry, '72.6');
	   $customer_email_stripe=rgar( $entry, '82');
   }
   else if ($feed_name == 'Auth Registration - Career Coaching') {
	   $customer_name_field_id_fn=rgar( $entry, '114.3');
	   $customer_name_field_id_ln=rgar( $entry, '114.6');
	   $customer_email_stripe=rgar( $entry, '4');
   }
   else if ($feed_name == 'Auth Registration - College Coaching') {
	   $customer_name_field_id_fn=rgar( $entry, '114.3');
	   $customer_name_field_id_ln=rgar( $entry, '114.6');
	   $customer_email_stripe=rgar( $entry, '137');
   }
   else if ($feed_name == 'Auth Test College Coaching') {
	   $customer_name_field_id_fn=rgar( $entry, '114.3');
	   $customer_name_field_id_ln=rgar( $entry, '114.6');
	   $customer_email_stripe=rgar( $entry, '4');
	   
   }
   else if ($feed_name == 'Auth Registration - College Student Support Services') {
	   $customer_name_field_id_fn=rgar( $entry, '134.3');
	   $customer_name_field_id_ln=rgar( $entry, '134.6');
	   $customer_email_stripe=rgar( $entry, '140');
   }
   else if ($feed_name == 'Auth Registration - College Transfer Coaching' ) {
	   $customer_name_field_id_fn=rgar( $entry, '132.3');
	   $customer_name_field_id_ln=rgar( $entry, '132.6');
	   $customer_email_stripe=rgar( $entry, '142');
   }
   else if ($feed_name == 'Auth Registration - Graduate School Coaching') {
	   $customer_name_field_id_fn=rgar( $entry, '121.3');
	   $customer_name_field_id_ln=rgar( $entry, '121.6');
	   $customer_email_stripe=rgar( $entry, '131');
   }
   else if ($feed_name == 'Auth Registration - High School Coaching') {
	   $customer_name_field_id_fn=rgar( $entry, '112.3');
	   $customer_name_field_id_ln=rgar( $entry, '112.6');
	   $customer_email_stripe=rgar( $entry, '130');
   }
   else if ($feed_name == 'Auth Registration - SAT / ACT First Class - 9 Month Preparation Course') {
	   $customer_name_field_id_fn=rgar( $entry, '97.3');
	   $customer_name_field_id_ln=rgar( $entry, '97.6');
	   $customer_email_stripe=rgar( $entry, '104');
   }
   else if ($feed_name == 'Auth Registration - SAT / ACT The Express - 6 Month Preparation Course') {
	   $customer_name_field_id_fn=rgar( $entry, '91.3');
	   $customer_name_field_id_ln=rgar( $entry, '91.6');
	   $customer_email_stripe=rgar( $entry, '102');
   }
   else if ($feed_name == 'Auth Registration - SAT/ACT Boot Camp Summer 2023') {
	   $customer_name_field_id_fn=rgar( $entry, '91.3');
	   $customer_name_field_id_ln=rgar( $entry, '91.6');
	   $customer_email_stripe=rgar( $entry, '118');
   }
   else if ($feed_name == 'Auth Registration - SAT/ACT Preparation Program - Summer - Rising Seniors') {
	   $customer_name_field_id_fn=rgar( $entry, '91.3');
	   $customer_name_field_id_ln=rgar( $entry, '91.6');
	   $customer_email_stripe=rgar( $entry, '103');
   }
   else if ($feed_name == 'Auth Registration - SAT/ACT Preparation Program - Summer/Fall - Rising Seniors') {
	   $customer_name_field_id_fn=rgar( $entry, '91.3');
	   $customer_name_field_id_ln=rgar( $entry, '91.6');
	   $customer_email_stripe=rgar( $entry, '101');
	   
   }
   else if ($feed_name == 'Auth Registration - Summer Tutoring') {
    
	   $customer_name_field_id_fn=rgar( $entry, '72.3');
	   $customer_name_field_id_ln=rgar( $entry, '72.6');
	   $customer_email_stripe=rgar( $entry, '91');
   }
   else if ($feed_name == 'Auth Registration - ACT Jump Start 2023') {
    
	   $customer_name_field_id_fn=rgar( $entry, '91.3');
	   $customer_name_field_id_ln=rgar( $entry, '91.6');
	   $customer_email_stripe=rgar( $entry, '112');
   }
	else {
	   gf_stripe()->log_debug( __METHOD__ . '(): Name: Not  a valid feed');
   }
	
   
	if ( empty( $customer_id ) ) {
		$response = gf_stripe()->get_stripe_js_response();
		if ( ! empty( $response->id ) && substr( $response->id, 0, 3 ) === 'pi_' ) {
			try {
				$intent = \Stripe\PaymentIntent::retrieve( $response->id );
				if ( ! empty( $intent->customer ) ) {
					gf_stripe()->log_debug( 'gform_stripe_customer_id: PaymentIntent already has customer; ' . print_r( $intent->customer, true ) );
   
					return is_object( $intent->customer ) ? $intent->customer->id : $intent->customer;
				}
			} catch ( \Exception $e ) {
				gf_stripe()->log_debug( 'gform_stripe_customer_id: unable to get PaymentIntent; ' . $e->getMessage() );
			}
		}
   
		$customer_params = array();
   
		$email_field = rgars( $feed, 'meta/receipt_field' );
		if ( ! empty( $email_field ) && strtolower( $email_field ) !== 'do not send receipt' ) {
			$customer_params['email'] = gf_stripe()->get_field_value( $form, $entry, $email_field );
		}
   // Optional - Customer name using a Name field id 8.
  $customer_params['name'] = $customer_name_field_id_ln . ', ' . $customer_name_field_id_fn;
  gf_stripe()->log_debug( __METHOD__ . '(): Condition Passed'. $customer_name_field_id_fn);

 gf_stripe()->log_debug( __METHOD__ . '(): Name: ' . $customer_params['name'] );
 $customer_params['email'] = $customer_email_stripe;
        gf_stripe()->log_debug( __METHOD__ . '(): Email: ' . $customer_params['email'] );
   
		$customer    = gf_stripe()->create_customer( $customer_params, $feed, $entry, $form );
		$customer_id = $customer->id;
		gf_stripe()->log_debug( 'gform_stripe_customer_id: Returning Customer ID: ' . $customer_id );
	}
   
	return $customer_id;
   
   }, 10, 4 );
/*Auth*/

add_filter( 'gform_stripe_charge_authorization_only', 'stripe_charge_authorization_only', 10, 2 );
function stripe_charge_authorization_only( $authorization_only, $feed ) {
    $feed_name  = rgars( $feed, 'meta/feedName' );
    if (str_starts_with( $feed_name, 'Auth' )) {
        return true;
    }
 
    return $authorization_only;
}

// This sets setup_future_usage parameter to off_session to save the payment method.

add_filter( 'gform_stripe_charge_pre_create', function( $charge_meta, $feed, $submission_data, $form, $entry ) {
 
    gf_stripe()->log_debug( __METHOD__ . '(): Adding setup_future_usage for feed ' . rgars( $feed, 'meta/feedName' ) );
    $charge_meta['setup_future_usage'] = 'off_session';
 
    return $charge_meta;
}, 10, 5 );


/* All in One SEO Pack */

// disable the SEO menu in the admin toolbar
add_filter( 'aioseo_show_in_admin_bar', '__return_false' );

// disable the AIOSEO Details column for users that don't have a freshysites.com email address
if ( function_exists( 'aioseo' ) ) {
	// fires after WordPress has finished loading but before any headers are sent.
	add_action( 'init', function() {
		// get current User
		$user = wp_get_current_user(); 
		// get their email address
		$email = $user->user_email;
		// check the email's domain
		$domain = 'freshysites.com';
		// check if email address matches domain list
		$banned = strpos($email, $domain) === false;
		// if current user's email address doesn't match domain list
		if( $user && $banned ) {
			// remove the AIOSEO Details column for users without a particular email address domain
			remove_action( 'current_screen', [ aioseo()->admin, 'addPostColumns' ], 1 );
		}
	} );
}
