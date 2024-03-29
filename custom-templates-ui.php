<?php

/**
 * Plugin Name: Custom Templates Creator
 * Plugin URI: https://github.com/CalebBarnes
 * Description: UI to create templates through a GUI in a plugin instead of in the theme.
 * Version: 2.3.0
 * Author: Caleb Barnes
 * Author URI: https://github.com/CalebBarnes
 */

function getTemplateFileName($templateName) {	
	if (empty($templateName)) {
		return "";
	}
    return str_replace(' ', '-', strtolower($templateName));
}

add_action('acf/init', 'ctc_acf_op_init');

function ctc_acf_op_init()
{

	// **** We are getting all page templates on acf/init so that it is "warmed up" and ready for when acf locations are loaded when editing field groups 
	// Initialize templates with default placeholder for pages.
	$post_templates         = array();
	$post_templates['page'] = array();

	// Loop over post types and append their templates.
	if ( method_exists( 'WP_Theme', 'get_page_templates' ) ) {
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			$templates = wp_get_theme()->get_page_templates( null, $post_type );
			if ( $templates ) {
				$post_templates[ $post_type ] = $templates;
			}
		}
	}
	

    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => "Custom Template Creator",
            'menu_title' => "Templates",
            'menu_slug'  => "custom-template-creator",
            'show_in_graphql' => true,
        ]);
    }
}

function ctc_templates_callback($templates) {
    if (function_exists("get_field")){
        $custom_templates = get_field("templates", "option");
		if (!is_array($custom_templates)) {
			return $templates;
		}

        if ($custom_templates) {
            foreach ($custom_templates as $custom_template) {
                $custom_template_name = $custom_template["template_name"];
                $template_file_name = getTemplateFileName($custom_template_name);

                $templates["$template_file_name.php"] = $custom_template_name;
            }
        }
    }

    return $templates;
}

add_filter('theme_page_templates', 'ctc_templates_callback');

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_65559d1c6199b',
	'title' => 'Templates',
	'fields' => array(
		array(
			'key' => 'field_65559d1db76ed',
			'label' => 'Templates',
			'name' => 'templates',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'table',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => '',
			'button_label' => 'Add Row',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_65559d32b76ee',
					'label' => 'Template Name',
					'name' => 'template_name',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_65559d1db76ed',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'custom-template-creator',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
) );
} );

