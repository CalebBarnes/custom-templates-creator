<?php

/**
 * Plugin Name: Custom Templates Creator
 * Plugin URI: https://github.com/CalebBarnes
 * Description: UI to create templates through a GUI in a plugin instead of in the theme.
 * Version: 1.0.0
 * Author: Caleb Barnes
 * Author URI: https://github.com/CalebBarnes
 */



 function getTemplateFileName($templateName) {
    return str_replace(' ', '-', strtolower($templateName));
 }

add_action('acf/init', 'ctc_acf_op_init');

function ctc_acf_op_init()
{


    $test_post_id = 483;

    $template_slug = get_page_template_slug($test_post_id);
    $set_template  = get_post_meta( $test_post_id, '_wp_page_template', true );
    
    error_log("template_slug");
    error_log(json_encode($template_slug));
    error_log("set_template");
    error_log(json_encode($set_template));
    
    $registered_templates = wp_get_theme()->get_post_templates();
    error_log("registered_templates");
    error_log(json_encode($registered_templates));

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

function ctc_redirect_page_template ($template) {

    $custom_templates = get_field("templates", "option");

    if ($custom_templates) {
        foreach($custom_templates as $custom_template) {
            $fileName = getTemplateFileName($custom_template);
            error_log("filename $fileName");

            if ($fileName == basename($template)) {
                error_log("matched filename $fileName");
                $template = WP_PLUGIN_DIR . "/custom-templates-ui/templates/test-template.php";
                return $template;
            }
        }
    } 

    return $template;
}

add_filter ('page_template', 'ctc_redirect_page_template');