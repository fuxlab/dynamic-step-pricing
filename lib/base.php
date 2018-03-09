<?php
/**
 * Plugin Name: Dynamic Step Pricing
 * Text Domain: dynamic-step-pricing
*/

if ( ! defined( 'ABSPATH' ) ) exit; // good bye script kiddies

class DynamicStepPricing {

  const PLUGIN_NAME = 'Dynamic Step Pricing';
  const VERSION = '0.0.1';

  public function fields() {
    $options = get_option( 'dynamic_step_pricing_options' );
    $fields = [];
    if( isset( $options['fields'] ) ){
      $fields = explode( ',', $options['fields'] );
      $fields = array_values(preg_filter('/^/', 'pa_', $fields));
    }
    return $fields;
  }

  public function names() {
    $options = get_option( 'dynamic_step_pricing_options' );
    $names = [];
    if( isset( $options['names'] ) ){
      $names = explode( ',', $options['names'] );
    }
    return $names;
  }

  public function name_for_field( $field_name ){
    $result_index = array_search( $field_name, DynamicStepPricing::fields() );
    if( count( $result_index ) > 0 ){
      $names = DynamicStepPricing::names();
      return $names[$result_index];
    }
    return false;
  }

  public function fields_for_product( $product_id = false ) {
    global $product;

    if( $product_id && !$product ) {
      $product = wc_get_product( $product_id );
    }

    $fields_available = DynamicStepPricing::fields();
    $fields_for_product = array_keys( $product->get_attributes() );
    
    $fields_used = array_intersect( $fields_available, $fields_for_product );
    return $fields_used;
  }

}