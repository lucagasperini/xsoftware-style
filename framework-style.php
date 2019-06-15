<?php

if(!defined("ABSPATH")) die;

trait style
{
        static function generate_css($style, $filename, $typedef = array())
        {
                $xs_dir = WP_CONTENT_DIR . '/xsoftware/';
                if(is_dir($xs_dir) === FALSE)
                        mkdir($xs_dir, 0755);
                $colors_dir = $xs_dir . 'style/';
                if(is_dir($colors_dir) === FALSE)
                        mkdir($colors_dir, 0755);

                $css = '';

                foreach($style as $class => $values) {
                        foreach($values as $selector => $prop) {
                                $not_empty = FALSE;
                                $class_text = '';

                                if(empty($selector) || $selector == 'default')
                                        $class_text .= $class . '{';
                                else
                                        $class_text .= $class . ':' . $selector . '{';

                                foreach($prop as $type => $v)
                                {
                                        $val = isset($typedef[$v]) ? $typedef[$v] : $v;
                                        if(!empty($val)) {
                                                $class_text .= $type .':' . $val . ';';
                                                $not_empty = TRUE;
                                        }
                                }

                                $class_text .= '}';
                                if($not_empty == TRUE)
                                        $css .= $class_text;
                        }
                }
                $file_style = fopen($colors_dir.$filename, 'w') or die('Unable to open file!');
                fwrite($file_style, $css);
                fclose($file_style);
        }

        static function install_style_pack($style)
        {
                $not_empty = FALSE;
                $options = xs_framework::get_option('style');
                foreach($style as $class => $values)
                {
                        if(!isset($options[$class])) {
                                $options[$class] = $values;
                                $not_empty = TRUE;
                        }
                }
                if($not_empty === TRUE)
                        xs_framework::update_option('style', $options);
        }

        static function remove_style($style)
        {
                $not_empty = FALSE;
                $options = xs_framework::get_option('style');
                foreach($style as $name)
                {
                        if(isset($options[$name])) {
                                unset($options[$name]);
                                $not_empty = TRUE;
                        }
                }

                if($not_empty === TRUE)
                        xs_framework::update_option('style', $style);
        }

}

?>
