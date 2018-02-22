<?php
/**
 * Plugin Name: Dynamic Step Pricing
 * Description: dynamic variable products with step pricing for woocommerce
 * Plugin URI: https://github.com/fuxlab/dynamic-step-pricing
 * Author: fuxlab / Frank Cieslik
 * Version: 0.0.1
 * Author URI: https://fuxlab.com/
 *
 * Text Domain: dynamic-step-pricing
*/

if ( ! defined( 'ABSPATH' ) ) exit; // good bye script kiddies

require( 'lib/base.php' );
require( 'lib/admin.php' );
require( 'lib/hooks.php' );

define( 'DYNAMIC_STEP_PRICING_VERSION', DynamicStepPricing::VERSION );

if(is_admin()) {
  $dynamic_step_pricing_admin = new DynamicStepPricingAdmin();
}

// add scripts
function dynamic_step_pricing_script_load() {
  wp_register_script( 'dynamic_step_pricing_script', plugins_url( '/assets/js/dynamic-step-pricing.js', __FILE__ ) );
  wp_enqueue_script('dynamic_step_pricing_script');
}
add_action( 'wp_enqueue_scripts', 'dynamic_step_pricing_script_load' );