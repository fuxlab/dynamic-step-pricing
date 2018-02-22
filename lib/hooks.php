<?php
/**
 * Plugin Name: Dynamic Step Pricing
 * Description: 
 * Plugin URI: https://fuxlab.com/
 * Author: fuxlab
 * Version: 0.0.1
 * Author URI: https://fuxlab.com/
 *
 * Text Domain: dynamic-step-pricing
*/

if ( ! defined( 'ABSPATH' ) ) exit; // good bye script kiddies

// add hooks
add_action( 'woocommerce_before_add_to_cart_button', 'dynamic_step_pricing_product_detail_fields' );
add_action( 'woocommerce_add_cart_item_data', 'dynamic_step_pricing_save_fields', 10, 2 );
add_filter( 'woocommerce_get_item_data', 'dynamic_step_pricing_meta_on_cart_and_checkout', 10, 2 );
add_action( 'woocommerce_add_order_item_meta', 'dynamic_step_pricing_order_item_meta', 1, 3 );
add_action( 'woocommerce_add_to_cart_validation', 'dynamic_step_pricing_validation', 10, 3 );

function dynamic_step_pricing_save_fields( $cart_item_data, $product_id ) {
  foreach( DynamicStepPricing::fields_for_product($product_id) as $field ) {
    
    /* make sure every add to cart action as unique line item */
    if( !$cart_item_data['unique_key'] ) {
      $cart_item_data['unique_key'] = md5( microtime().rand() . "dynamic_step_pricing" );
    }
    
    $field_name = $field.'_detail';
    $cart_item_data[$field_name] = $_REQUEST[$field_name];
  }
  return $cart_item_data;
}


function dynamic_step_pricing_meta_on_cart_and_checkout( $cart_data, $cart_item = null ) {
  $custom_items = array();
  
  if( ! empty($cart_data ) ) {
    $custom_items = $cart_data;
  }

  foreach( DynamicStepPricing::fields_for_product($cart_item['product_id']) as $field ) {
    $field_name = $field.'_detail';
    if( isset( $cart_item[ $field_name ] ) ) {
      $custom_items[] = array( 'name' => DynamicStepPricing::name_for_field( $field ), 'value' => $cart_item[$field.'_detail'] );
    }
  }
  return $custom_items;
}

// for saving order in db
function dynamic_step_pricing_order_item_meta( $item_id, $values, $cart_item_key ) {
  foreach( DynamicStepPricing::fields() as $field ) {
    $field_name = $field.'_detail';
    if( isset( $values[$field_name] ) ) {
      wc_add_order_item_meta( $item_id, DynamicStepPricing::name_for_field( $field_name ), $values[$field_name] );
    }
  }
}

function dynamic_step_pricing_product_detail_fields() {
  $fields = DynamicStepPricing::fields_for_product();
  echo '
    <table class="variations" cellspacing="0">
      <tbody>
  ';
  
  foreach( $fields as $field ) { 
    $field_name = $field.'_detail';
    echo '
      <tr>
        <td class="label"><label for="widthmm">'.DynamicStepPricing::name_for_field( $field_name ).'</label></td>
        <td class="value">
          <input id="'.$field_name.'" type="text" name="'.$field_name.'" value="'.$_REQUEST[$fieldname].'" />                      
        </td>
      </tr>                               
    ';
  }
  
  echo '
      </tbody>
    </table>
    <script>
      jQuery( document ).ready(function() {
        DynamicStepPricing.init(' . json_encode( $fields ) . ');
      });
    </script>
  ';
}

function dynamic_step_pricing_validation( $true, $product_id, $quantity ) {
  foreach( DynamicStepPricing::fields_for_product( $product_id ) as $field ) {
    if( empty( $_POST[$field.'_detail'] ) ) {
      wc_add_notice( __( 'Please enter correct value. [' + $field + ']', 'woocommerce' ), 'error' );
      return false;
    }
  }
  return true;
}
