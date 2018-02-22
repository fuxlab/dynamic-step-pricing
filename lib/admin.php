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

class DynamicStepPricingAdmin {

  private $options;

  public function __construct()
  {
    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'page_init' ) );
  }

  public function add_plugin_page()
  {
    add_options_page(
      'Settings Admin', 
      DynamicStepPricing::PLUGIN_NAME, 
      'manage_options', 
      'dynamic-step-pricing-admin', 
      array( $this, 'create_admin_page' )
    );
  }

  public function create_admin_page()
  {
    $this->options = get_option( 'dynamic_step_pricing_options' );
    ?>
    <div class="wrap">
      <h1><?= DynamicStepPricing::PLUGIN_NAME ?></h1>
      <form method="post" action="options.php">
      <?php
        settings_fields( 'dynamic_step_pricing_option_group' );
        do_settings_sections( 'dynamic-step-pricing-admin' );
        submit_button();
      ?>
      </form>
    </div>
    <?php
  }

  public function page_init()
  {        
    register_setting(
      'dynamic_step_pricing_option_group',
      'dynamic_step_pricing_options',
      array( $this, 'sanitize' )
    );

    add_settings_section(
      'setting_section_id',
      'Basic Settings',
      array( $this, 'print_section_info' ),
      'dynamic-step-pricing-admin'
    );

    add_settings_field(
      'fields',
      'Field(s)',
      array( $this, 'fields_callback' ),
      'dynamic-step-pricing-admin',
      'setting_section_id'
    );      

    add_settings_field(
      'names', 
      'Name(s)', 
      array( $this, 'names_callback' ), 
      'dynamic-step-pricing-admin', 
      'setting_section_id'
    );      
  }

  /**
   * uses commas only, trims whitespaces from each item.
   * ignores empty items, oes not split an item with internal spaces
   * @param string $string_input Contains all elems as string
   */
  private function clean_values( $string_input )
  {
    $parts = preg_split( '/[\s*,\s*]*,+[\s*,\s*]*/', $string_input );
    return join( ',', array_slice( $parts, 0, 2 ) );
  }

  /**
   * Sanitize each setting field
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sanitize( $input )
  {
    $new_input = array();
    if( isset( $input['fields'] ) ){
      $new_input['fields'] = sanitize_text_field( $input['fields'] );
      $new_input['fields'] = $this->clean_values( $new_input['fields'] );
    }

    if( isset( $input['names'] ) )
      $new_input['names'] = sanitize_text_field( $input['names'] );
      $new_input['names'] = $this->clean_values( $new_input['names'] );
    return $new_input;
  }

  public function print_section_info()
  {
    print 'Enter your settings below (comma separated):';
  }

  public function fields_callback()
  {
    printf(
      '<input type="text" id="fields" name="dynamic_step_pricing_options[fields]" value="%s" />',
      isset( $this->options['fields'] ) ? esc_attr( $this->options['fields'] ) : ''
    );
  }

  public function names_callback()
  {
    printf(
      '<input type="text" id="names" name="dynamic_step_pricing_options[names]" value="%s" />',
      isset( $this->options['names'] ) ? esc_attr( $this->options['names'] ) : ''
    );
  }
}
