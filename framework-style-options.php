<?php

        function show_style()
        {
                $tab = xs_framework::create_tabs( array(
                        'href' => '?page=xsoftware&main_tab=style',
                        'tabs' => array(
                                'color' => 'Colors'
                        ),
                        'home' => 'color',
                        'name' => 'style_tab'
                ));

                switch($tab)
                {
                        case 'color':
                                $this->show_style_colors();
                                return;
                }
        }

        function show_style_colors()
        {
                $style = $this->settings['style'];

                foreach($style as $name => $prop) {
                        $data[$name][] = $name;
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop[0]['color'],
                                'name' => 'xs_framework_options[style]['.$name.'][0][color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['color'],
                                'name' => 'xs_framework_options[style]['.$name.'][hover][color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['color'],
                                'name' => 'xs_framework_options[style]['.$name.'][focus][color]'
                        ));

                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop[0]['background-color'],
                                'name' =>
'xs_framework_options[style]['.$name.'][0][background-color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['background-color'],
                                'name' =>
'xs_framework_options[style]['.$name.'][hover][background-color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['background-color'],
                                'name' =>
'xs_framework_options[style]['.$name.'][focus][background-color]'
                        ));

                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop[0]['border-color'],
                                'name' => 'xs_framework_options[style]['.$name.'][0][border-color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['border-color'],
                                'name' =>
'xs_framework_options[style]['.$name.'][hover][border-color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['border-color'],
                                'name' =>
'xs_framework_options[style]['.$name.'][focus][border-color]'
                        ));
                }
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

                $colors = $this->settings['colors'];

                $settings = array(
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][primary]',
                        'value' => $colors['primary'],
                        'echo' => TRUE
                );

                add_settings_field(
                        $settings['name'],
                        'Primary Color:',
                        'xs_framework::create_input',
                        'framework',
                        'section_framework',
                        $settings
                );

                $settings = array(
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][secondary]',
                        'value' => $colors['secondary'],
                        'echo' => TRUE
                );

                add_settings_field(
                        $settings['name'],
                        'Secondary Color:',
                        'xs_framework::create_input',
                        'framework',
                        'section_framework',
                        $settings
                );

                $settings = array(
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][background]',
                        'value' => $colors['background'],
                        'echo' => TRUE
                );

                add_settings_field(
                        $settings['name'],
                        'Background Color:',
                        'xs_framework::create_input',
                        'framework',
                        'section_framework',
                        $settings
                );

                $settings = array(
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][text]',
                        'value' => $colors['text'],
                        'echo' => TRUE
                );

                add_settings_field(
                        $settings['name'],
                        'Text Color:',
                        'xs_framework::create_input',
                        'framework',
                        'section_framework',
                        $settings
                );
        }

?>