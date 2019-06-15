<?php
/*
Plugin Name: XSoftware Style
Description: Style management on WordPress.
Version: 1.0
Author: Luca Gasperini
Author URI: https://xsoftware.it/
*/

if(!defined("ABSPATH")) die;

if (!class_exists("xs_style_plugin")) :

class xs_style_plugin
{

        private $default = array(
                'override' => array(
                        'a' => array(
                                'color' => array( 'text' => '#DDDDDD' , 'bg' => '', 'bord' => ''),
                                'hover' => array( 'text' => '#FFFFFF' , 'bg' => '', 'bord' => ''),
                                'focus' => array( 'text' => '#FFFFFF' , 'bg' => '', 'bord' => ''),
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




                echo "<div class=\"wrap\">";

                echo "<form action=\"options.php\" method=\"post\">";

                settings_fields('style_setting');
                do_settings_sections('style');

                submit_button( '', 'primary', 'submit', true, NULL );

                echo "</form>";

                echo "</div>";
        }

        function section_menu()
        {
                register_setting( 'style_setting', 'xs_options_style', array($this, 'input') );
                add_settings_section( 'style_section', 'Style settings', array($this, 'show'), 'style' );
        }

        function input($input)
        {
                $return = $this->options;

                if(isset($input['override']) && !empty($input['override'])) {
                        $buffer = $input['override'];
                        $options = $this->options['override'];

                        if(isset($buffer['new']['name']) && !empty($buffer['new']['name'])) {

                                $name = $buffer['new']['name'];
                                unset($buffer['new']['name']);
                                $options[$name] = $buffer['new'];
                                unset($buffer['new']);

                        }
                        if(isset($buffer['update']) && !empty($buffer['update'])) {
                                foreach($buffer['update'] as $name => $prop) {
                                        $options[$name] = $prop;
                                }
                        }
                        if(isset($buffer['xs_generate_css'])) {
                                $this->generate_override_css($options);
                        }

                        $return['override'] = $options;
                }

                if(isset($input['colors']) && !empty($input['colors'])) {
                        $buffer = $input['colors'];
                        $options = $this->options['colors'];

                        foreach($buffer as $name => $value) {
                                $options[$name] = $value;
                        }

                        $this->generate_css($options);

                        $return['colors'] = $options;
                }
                return $return;
        }

        function show()
        {
                $tab = xs_framework::create_tabs( array(
                        'href' => '?page=xsoftware_style',
                        'tabs' => array(
                                'colors' => 'Colors Override',
                        ),
                        'home' => 'colors'
                ));

                if($tab === 'colors')
                        $this->show_colors_override();
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
                foreach($colors as $name => $value) {
                        $class = '';
                        $class .= '.'.$name.'{color:'.$value.';}';
                        $class .= '.'.$name.'_bg{background-color:'.$value.';}';
                        $class .= '.'.$name.'_bord{border-color:'.$value.';}';
                        $css .= $class;
                }

                $css .= 'body{background-color:'.$colors['xs_body'].';color:'.$colors['xs_text'].'}';

                $file_style = fopen($colors_dir.'xsoftware.css', 'w') or die('Unable to open file!');
                fwrite($file_style, $css);
                fclose($file_style);
        }

        function show_colors()
        {
        }

        function show_colors_override()
        {
                $data = array();
                $colors = $this->options['override'];

                foreach($colors as $name => $prop) {
                        $data[$name][] = $name;
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['color']['text'],
                                'name' => 'xs_options_style[override][update]['.$name.'][color][text]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['text'],
                                'name' => 'xs_options_style[override][update]['.$name.'][hover][text]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['text'],
                                'name' => 'xs_options_style[override][update]['.$name.'][focus][text]'
                        ));

                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['color']['bg'],
                                'name' => 'xs_options_style[override][update]['.$name.'][color][bg]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['bg'],
                                'name' => 'xs_options_style[override][update]['.$name.'][hover][bg]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['bg'],
                                'name' => 'xs_options_style[override][update]['.$name.'][focus][bg]'
                        ));

                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['color']['bord'],
                                'name' => 'xs_options_style[override][update]['.$name.'][color][bord]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['bord'],
                                'name' => 'xs_options_style[override][update]['.$name.'][hover][bord]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['bord'],
                                'name' => 'xs_options_style[override][update]['.$name.'][focus][bord]'
                        ));
                }

                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][name]'
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][color][text]'
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][hover][text]'
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][focus][text]'
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][color][bg]'
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][hover][bg]'
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][focus][bg]'
                ));

                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][color][bord]'
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][hover][bord]'
                ));
                $new[] = xs_framework::create_input( array(
                        'name' => 'xs_options_style[override][new][focus][bord]'
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
                xs_framework::create_table(array(
                        'class' => 'xs_admin_table',
                        'headers' => $headers,
                        'data' => $data
                ));

                xs_framework::create_button( array(
                        'class' => 'button-primary',
                        'name' => 'xs_options_style[xs_generate_css]',
                        'text' => 'Generate CSS',
                        'echo' => TRUE
                ));
        }

}

$style_plugin = new xs_style_plugin;

endif;

?>
