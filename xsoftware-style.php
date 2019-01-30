<?php
/*
Plugin Name: XSoftware Style
Description: Style management on WordPress.
Version: 1.0
Author: Luca Gasperini
Author URI: https://xsoftware.it/
*/

if(!defined("ABSPATH")) exit;

if (!class_exists("xs_style_plugin")) :

class xs_style_plugin
{
        
        private $default = array(
                'colors' => array(
                        'a' => array(
                                'color' => array( 'text' => '#DDDDDD' , 'bg' => ''), 
                                'hover' => array( 'text' => '#FFFFFF' , 'bg' => ''), 
                                'focus' => array( 'text' => '#FFFFFF' , 'bg' => ''),
                        )
                )
        );
        
        private $options = NULL;
      
        public function __construct()
        {
                ob_start();
                add_action("admin_menu", array($this, "admin_menu"));
                add_action("admin_init", array($this, "section_menu"));
                $this->options = get_option('xs_options_style', $this->default);
        }
        
        function admin_menu()
        {
                add_submenu_page( "xsoftware", "XSoftware Style", "Style", "manage_options", "xsoftware_style", array($this, "menu_page") );
        }
        
        function menu_page()
        {
                if ( !current_user_can( "manage_options" ) )  {
                        wp_die( __( "Exit!" ) );
                }
                
                xs_framework::init_admin_style();
                xs_framework::init_admin_script();
                
                echo "<div class=\"wrap\">";
                echo "<h2>Colors configuration</h2>";

                echo "<form action=\"options.php\" method=\"post\">";
               
                settings_fields('colors_setting');
                do_settings_sections('colors');
                
                submit_button( '', 'primary', 'submit', true, NULL );
                
                echo "</form>";
                
                echo "</div>";
        }
        
        function section_menu()
        {
                register_setting( 'colors_setting', 'xs_options_style', array($this, 'input') );
                add_settings_section( 'colors_section', 'Colors settings', array($this, 'show'), 'colors' );
        }
        
        function input($input)
        {
                $options = $this->options;
                
                if(isset($input['xs_new_color']['name']) && !empty($input['xs_new_color']['name'])) {

                        $name = $input['xs_new_color']['name'];
                        unset($input['xs_new_color']['name']);
                        $options['colors'][$name] = $input['xs_new_color'];
                        unset($input['xs_new_color']);

                }
                if(isset($input['update']) && !empty($input['update'])) {
                        foreach($input['update'] as $name => $prop) {
                                $options['colors'][$name] = $prop;
                        }
                }
                if(isset($input['xs_generate_css'])) {
                        $this->generate_css($options['colors']);
                }
                
                
                return $options;
        }
        
        function generate_css($colors) 
        {
                $xs_dir = WP_CONTENT_DIR . '/xsoftware/';
                if(is_dir($xs_dir) === FALSE)
                        mkdir($xs_dir, 0774);
                $colors_dir = $xs_dir . 'colors/';
                if(is_dir($colors_dir) === FALSE)
                        mkdir($colors_dir, 0774);
                
                $css = '';
                
                foreach($colors as $name => $prop) {
                        foreach($prop as $type => $value) {
                                $class = '';
                                $not_empty = FALSE;
                                
                                if($type === 'color')
                                        $class .= $name . '{';
                                else
                                        $class .= $name . ':' . $type . '{';
                                        
                                if(!empty($value['text'])) {
                                        $class .= 'color:' . $value['text'] . ' !important;';
                                        $not_empty = TRUE;
                                }
                                if(!empty($value['bg'])) {
                                        $class .= 'background-color:' . $value['bg'] . ' !important;';
                                        $not_empty = TRUE;
                                }
                                if(!empty($value['bord'])) {
                                        $class .= 'border-color:' . $value['bord'] . ' !important;';
                                        $not_empty = TRUE;
                                }
                                
                                $class .= '}';
                                if($not_empty == TRUE)
                                        $css .= $class;
                        }
                }
                
                $file_style = fopen($colors_dir.'style.css', 'w') or die('Unable to open file!');
                fwrite($file_style, $css);
                fclose($file_style);
        }
        
        function show()
        {
                $data = array();
                $colors = $this->options['colors'];
                
                foreach($colors as $name => $prop) {
                        $data[$name][] = $name;
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['color']['text'],
                                'name' => 'xs_options_style[update]['.$name.'][color][text]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['text'],
                                'name' => 'xs_options_style[update]['.$name.'][hover][text]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['text'],
                                'name' => 'xs_options_style[update]['.$name.'][focus][text]',
                                'return' => TRUE
                        ));
                        
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['color']['bg'],
                                'name' => 'xs_options_style[update]['.$name.'][color][bg]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['bg'],
                                'name' => 'xs_options_style[update]['.$name.'][hover][bg]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['bg'],
                                'name' => 'xs_options_style[update]['.$name.'][focus][bg]',
                                'return' => TRUE
                        ));
                        
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['color']['bord'],
                                'name' => 'xs_options_style[update]['.$name.'][color][bord]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['bord'],
                                'name' => 'xs_options_style[update]['.$name.'][hover][bord]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['bord'],
                                'name' => 'xs_options_style[update]['.$name.'][focus][bord]',
                                'return' => TRUE
                        ));
                }
                
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][name]',
                        'return' => TRUE
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][color][text]',
                        'return' => TRUE
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][hover][text]',
                        'return' => TRUE
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][focus][text]',
                        'return' => TRUE
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][color][bg]',
                        'return' => TRUE
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][hover][bg]',
                        'return' => TRUE
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][focus][bg]',
                        'return' => TRUE
                ));
                
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][color][bord]',
                        'return' => TRUE
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][hover][bord]',
                        'return' => TRUE
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[xs_new_color][focus][bord]',
                        'return' => TRUE
                ));
               
                $data[] = $new;
                $headers = array(
                        'Name', 
                        'Color', 
                        'Hover', 
                        'Focus', 
                        'Background Color', 
                        'Background Hover', 
                        'Background Focus', 
                        'Border Color',
                        'Border Hover',
                        'Border Focus'
                );
                xs_framework::create_table(array('headers' => $headers, 'data' => $data));
                
                xs_framework::create_button( array(
                        'class' => 'button-primary',
                        'name' => 'xs_options_style[xs_generate_css]',
                        'text' => 'Generate CSS'
                ));
                
        }
        
}

$style_plugin = new xs_style_plugin;

endif;

?>
