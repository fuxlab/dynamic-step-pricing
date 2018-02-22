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

var DynamicStepPricing = {
  
  fields: {},

  min_value: 0,
  max_value: 0,
  
  init: function(input_fields){
    if(!input_fields || input_fields.length == 0) {
      return;
    }
    jQuery.each(input_fields, function( index, original_field_name ) {
      var field_value = jQuery('#' + original_field_name).val();
      DynamicStepPricing.fields[original_field_name] = [];

      jQuery('#' + original_field_name + ' option').each(function(index)
      {
        option_value = jQuery(this).val();
        if( DynamicStepPricing.is_number(option_value) ) {
          DynamicStepPricing.fields[original_field_name].push(parseInt(option_value));
        }
      });

      DynamicStepPricing.fields[original_field_name].sort();
      // WIP: Preselction of already filled in values
      // DynamicStepPricing.breiten = $.map($('#'+field+' option') ,function(option) {
      //    if(option.value != "" && parseInt(option.value) > 0){
      //      return parseInt(option.value);  
      //    }
      // });
    });

    this.add_input_fields_actions();
    this.hide_original_selection_boxes();
  },

  add_input_fields_actions: function(){
    jQuery.each(DynamicStepPricing.fields, function( original_field_name, steps ) {
      jQuery('#' + original_field_name + '_detail').on('input', function(e) {
        
        value = jQuery(e.target).val();
        if( DynamicStepPricing.is_number(value) ) {
          value = parseInt(value);
          min = DynamicStepPricing.fields[original_field_name][0];
          max = DynamicStepPricing.fields[original_field_name][DynamicStepPricing.fields[original_field_name].length-1];
          
          if( value >= min && value <= max) {
            jQuery(this).removeClass('error');
            step_value = DynamicStepPricing.fields[original_field_name][0];
            i = 0;
            while(value > DynamicStepPricing.fields[original_field_name][i]) {
              i = i + 1;
              step_value = DynamicStepPricing.fields[original_field_name][i];
            }
            DynamicStepPricing.commit(original_field_name, step_value);
            return;
          }
        } else {
          jQuery(this).addClass('error');
          jQuery('#' + original_field_name).val('');
        }
        DynamicStepPricing.commit(original_field_name, '');
      });
    });
  },
  
  
  hide_original_selection_boxes: function(){
    jQuery.each(DynamicStepPricing.fields, function( original_field_name, steps ) {
      jQuery('#' + original_field_name).parents('.variations').hide();
    });
  },

  is_number: function(value){
    return !isNaN(parseFloat(value)) && isFinite(value);
  },

  commit: function(original_field_name, step_value){
    jQuery('#' + original_field_name).val(step_value);
    jQuery('#' + original_field_name).trigger('change');
  }

};