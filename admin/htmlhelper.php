<?php
if (!class_exists('AtfHtmlHelper')) {
    class AtfHtmlHelper
    {

        public static function group($args = array())
        {
            ?>


            <table class="form-table atf-options-group">
                <thead>
                <tr>
                    <th class="group-row-id">#</th>
                    <?php

                    foreach ($args['items'] as $key => $item) {
                        echo '<th>' . $item['title'] . '</th>';
                    }

                    ?>
                    <th class="group-row-controls"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($args['value'] as $row_key => $row_val) {
                    echo '<tr class="row">';
                    echo '<td class="group-row-id">' . $i . '</td>';
                    foreach ($args['items'] as $key => $item) {
                        $item['id'] = $key;
                        $item['desc'] = '';
                        $item['uniqid'] = uniqid($item['id']);
                        $item['name'] = $args['name'] . '[' . $row_key . '][' . $item['id'] . ']';


                        if (!isset($row_val[$item['id']])) {
                            $item['value'] = '';
                        } else {
                            $item['value'] = $row_val[$item['id']];
                        }


                        echo '<td '
                            .'data-field-type="' . $item['type'] . '" '
                            .'data-field-name-template="' . $args['name'] . '[#][' . $item['id'] . ']' . '">';
                        $item['id'] = $item['uniqid'];
                        echo self::$item['type']($item);
                        echo '</td>';


                    }
                    echo '<td class="group-row-controls">';
                    echo '<a class="btn-control-group plus" href="#" >+</a>';
                    echo '<a class="btn-control-group minus" href="#" >&times;</a>';
                    echo '</td>';
                    echo '</tr>';
                    $i++;
                }

                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="group-row-id">#</td>
                    <?php

                    foreach ($args['items'] as $key => $item) {

                        echo '<td>';
                        echo (empty($item['desc'])) ? '' : '<p  class="description">' . $item['desc'] . '</p>';
                        echo '</td>';
                    }

                    ?>
                    <th class="group-row-controls"></th>
                </tr>
                </tfoot>
            </table>


        <?php
        }

        public static function text($args = array())
        {
            self::textField($args);
        }

        public static function textField($args = array())
        {
            $default = array(
                'value' => '',
                'class' => 'regular-text',
                'addClass' => '',
            );

            foreach ($default as $key => $value) {
                if (!isset($args[$key])) {
                    $args[$key] = $value;
                }
            }
            $result = '<input type="text" id="' . $args['id'] . '" name="' . $args['name'] . '" value="' . esc_attr($args['value']) . '" class="' . $args['class'] . $args['addClass'] . '" />';
            if (isset($args['desc'])) {
                $result .= '<p class="description">' . $args['desc'] . '</p>';
            }

            echo $result;
        }

        public static function typography($args = array())
        {


        }

        public static function googlefont_redux($args = array())
        {

            wp_enqueue_script(
                'redux-opts-googlefonts-js',
                get_template_directory_uri() . '/atf/options/admin/fields/google_webfonts/jquery.fontselect.js',
                array('jquery'),
                time(),
                true
            );

            $default = array(
                'value' => '',
                'class' => '',
                'addClass' => '',
            );

            foreach ($default as $key => $value) {
                if (!isset($args[$key])) {
                    $args[$key] = $value;
                }
            }

            $result = '<p class="description" style="color:red;">' . __('The fonts provided below are free to use custom fonts from the <a href="http://www.google.com/webfonts" target="_blank">Google Web Fonts directory</a>', 'dfd') . '</p>';

            $result .= '<input type="text" id="' . $args['id'] . '" name="' . $args['name'] . '" class="font"  ' . 'value="' . esc_attr($args['value']) . '" />';

            $result .= '<h3 id="' . $args['id'] . '" class="example">Lorem Ipsum is simply dummy text</h3>';

            $result .= (isset($args['desc']) && !empty($args['desc'])) ? ' <span class="description">' . $args['desc'] . '</span>' : '';

            echo $result;


        }

        public static function googlefont($args = array())
        {

            $default = array(
                'value' => '',
                'class' => '',
                'addClass' => '',
            );
            $listStr = file_get_contents(get_template_directory() . '/atf/options/fontslist.txt');

            $listArray = explode('",' . PHP_EOL . '"', $listStr);


            foreach ($default as $key => $value) {
                if (!isset($args[$key])) {
                    $args[$key] = $value;
                }
            }

            $result = '<div class="google-webfonts">';

            $result .= '';
            $result .= '<select name="' . $args['name'] . '">';

            foreach ($listArray as $value) {
                $result .= '<option value="' . $value . '" ' . selected($value, $args['value'], false) . ' > ' . str_replace('+', ' ', $value) . ' </option>';
            }

            $result .= '</select>';
            $result .= '';
            $result .= '';
            $result .= '<div class="demotext"></div>';
            $result .= '';
            $result .= '<input type="text" class="demotextinput" value="Lorem ipsum dolor sit amet">';
            $result .= '';
            $result .= '</div>';


            $result .= (isset($args['desc']) && !empty($args['desc'])) ? ' <span class="description">' . $args['desc'] . '</span>' : '';

            echo $result;


        }

        public static function addMedia($args = array())
        {

            $default = array(
                'value' => '',
                'class' => 'regular-text',
                'addClass' => '',
            );

            foreach ($default as $key => $value) {
                if (!isset($args[$key])) {
                    $args[$key] = $value;
                }
            }

            $result = '<div class="uploader">';
            $result .= '<input type="hidden" id="' . $args['id'] . '" name="' . $args['name'] . '" value="' . $args['value'] . '" class="' . $args['class'] . $args['addClass'] . '" />';
            $result .= '<img class="atf-options-upload-screenshot" id="screenshot-' . $args['id'] . '" src="' . $args['value'] . '" />';
            if ($args['value'] == '') {
                $remove = ' style="display:none;"';
                $upload = '';
            } else {
                $remove = '';
                $upload = ' style="display:none;"';
            }
            $result .= ' <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);" class="atf-options-upload button-secondary"' . $upload . ' rel-id="' . $args['id'] . '">' . __('Upload', 'atf') . '</a>';
            $result .= ' <a href="javascript:void(0);" class="atf-options-upload-remove  button-secondary"' . $remove . ' rel-id="' . $args['id'] . '">' . __('Remove Upload', 'atf') . '</a>';
            $result .= '</div>';


            if (isset($args['desc'])) {
                $result .= '<p class="description">' . $args['desc'] . '</p>';
            }

            echo $result;
        }

        public static function colorPicker($args = array())
        {
            $default = array(
                'value' => '',
                'class' => 'color-picker-hex',
                'addClass' => '',
            );

            foreach ($default as $key => $value) {
                if (!isset($args[$key])) {
                    $args[$key] = $value;
                }
            }
            $result = '<div class="customize-control-content"><input type="text" id="' . $args['id'] . '" name="' . $args['name'] . '" value="' . $args['value'] . '" class="' . $args['class'] . $args['addClass'] . '" /></div>';
            if (isset($args['desc'])) {
                $result .= '<p class="description">' . $args['desc'] . '</p>';
            }

            echo $result;
        }

        public static function textarea($args = array())
        {
            $default = array(
                'value' => '',
                'class' => 'regular-text',
                'addClass' => '',
                'rows' => 10,
                'cols' => 50,
            );
            foreach ($default as $key => $value) {
                if (!isset($args[$key])) {
                    $args[$key] = $value;
                }
            }
            $result = '<textarea id="' . $args['id'] . '" name="' . $args['name'] . '" rows="' . $args['rows'] . '" cols="' . $args['cols'] . '" class="' . $args['class'] . $args['addClass'] . '" >' . $args['value'] . '</textarea>';
            if (isset($args['desc'])) {
                $result .= '<p class="description">' . $args['desc'] . '</p>';
            }
            echo $result;
        }




        public static function editor($args = array()) {
            self::wysiwyg($args);
        }
        public static function wysiwyg($args = array())
        {
            $default = array(
                'value' => '',
                'class' => 'regular-text',
                'addClass' => '',
                'rows' => 10,
                'cols' => 50,
                'options' => array(
                    'wpautop' => true, // use wpautop?
                    'media_buttons' => false, // show insert/upload button(s)
                    'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
                    'tabindex' => '',
                    'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
                    'editor_class' => '', // add extra class(es) to the editor textarea
                    'teeny' => false, // output the minimal editor config used in Press This
                    'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
                    'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                    'quicktags' => true, // load Quicktags, can be used to pass settings directly to Quicktags using an array()
                    'toolbar1' => 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,wp_fullscreen,wp_adv ',
                    'toolbar2' => 'formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help ',
                    'toolbar3' => '',
                    'toolbar4' => '',
                ),
            );
            foreach ($default as $key => $value) {
                if (!isset($args[$key])) {
                    $args[$key] = $value;
                }
            }
            $args['options']['textarea_name'] = $args['name'];
            wp_editor(stripslashes($args['value']), $args['id'], $args['options']);
            if (isset($args['desc'])) {
                echo '<p class="description">' . $args['desc'] . '</p>';
            }
        }

        public static function radioButtons($args = array())
        {

            $default = array(
                'value' => '',
                'class' => '',
                'addClass' => '',
            );

            foreach ($default as $key => $value) {
                if (!isset($args[$key])) {
                    $args[$key] = $value;
                }
            }

            $result = '';
            $result .= '<fieldset class="' . $args['class'] . $args['addClass'] . '" >';
            foreach ($args['options'] as $value => $label) {
                $checked = '';
                if ($value == $args['value']) {
                    $checked = "checked";
                }


                $result .= '<label class="' . $checked . '" >';
                $result .= '<input type="radio" id="' . $args['id'] . '" name="' . $args['name'] . '" value="' . $value . '" ' . checked($args['value'], $value, false) . ' />';
                $result .= $label;
                $result .= '</label>';
            }
            $result .= '</fieldset>';

            echo $result;
        }

        public static function tumbler($args = array())
        {
            self::onOffBox($args);
        }

        public static function onOffBox($args = array())
        {

            $on = '';
            if ($args['value']) {
                $on = 'on';
            }
            $result = '<a class="on-off-box ' . $on . '" href="#">';
            $result .= '<span class="tumbler"></span>';
            $result .= '<span class="text on">on</span>';
            $result .= '<span class="text off">off</span>';
            $result .= '<input type="radio" class="on" name="' . $args['name'] . '" value="1"  ' . checked($args['value'], '1', false) . ' >';
            $result .= '<input type="radio" class="off" name="' . $args['name'] . '" value="0" ' . checked($args['value'], '0', false) . ' >';
            $result .= '<span class="text off">off</span>';
            $result .= '</a>';

            if (isset($args['desc'])) {
                $result .= '<p class="description">' . $args['desc'] . '</p>';
            }

            echo $result;
        }

        public static function select($args)
        {
            if (isset($args['taxonomy'])) {

                self::selectFromTaxonomy($args);
            } else {
                $result = '<select name="' . $args['name'] . '">';

                if (!isset($args['values'])) {
                    $args['values'] = $args['options'];
                }

                foreach ($args['values'] as $value => $text) {
                    $result .= '<option value="' . $value . '" ' . selected($value, $args['value'], false) . ' > ' . $text . ' </option>';
                }

                $result .= '</select>';

                echo $result;
            }

        }

        public static function taxonomy_select($args)
        {
            self::selectFromTaxonomy($args);
        }

        public static function selectFromTaxonomy($args)
        {
            if (taxonomy_exists($args['taxonomy'])) {
                $args['selected'] = $args['value'];
                wp_dropdown_categories($args);
            } else {
                var_dump(get_taxonomies());
                echo "Taxonomy not exist";
            }
            if (isset($args['desc'])) {
                echo '<p class="description">' . $args['desc'] . '</p>';
            }
        }
    //    public static function


        public static function checkboxTaxonomy($args)
        {

            if (taxonomy_exists($args['taxonomy'])) {
                if (!is_array($args['value'])) {
                    $args['value'] = array($args['value']);
                }

                $cats = get_terms($args['taxonomy'],
                    array(
                        'hide_empty' => $args['hide_empty'],
                    ));

                $result = '';


                foreach ($cats as $cat) {
                    $result .= ' <label><input type="checkbox"'
                        . ' name="' . $args['name'] . '[]"'
                        . ' value="' . $cat->term_id . '" ';
                    $result .= (in_array($cat->term_id, $args['value'])) ? 'checked="checked"' : '';
                    $result .= ' > ' . $cat->name . '</label> ';

                }

                $result .= '';

                if (isset($args['desc'])) {
                    $result .= '<p class="description">' . $args['desc'] . '</p>';
                }

                echo $result;
            } else {
                var_dump(get_taxonomies());
                echo "Taxonomy not exist";
            }

        }

        public static function checkbox($args)
        {
            if (isset($args['taxonomy'])) {
                if (taxonomy_exists($args['taxonomy'])) {
                    $options = self::get_taxonomy_options($args);

                } else {
                    var_dump(get_taxonomies());
                    echo "Taxonomy not exist";
                }
            }

            if (isset($args['options']) && !isset($options)) {
                $options = $args['options'];
            } elseif (isset($args['options']) && isset($options)) {
                $options = $args['options'] + $options;
            } elseif (!isset($args['options']) && !isset($options)) {
                echo 'No options';
                return;
            }


            if (!is_array($args['value'])) {
                $args['value'] = array($args['value']);
            }

            $result = '';

            if (isset($args['vertical']) && $args['vertical']) {
                $vertical = true;
            } else {
                $vertical = false;
            }


            foreach ($options as $val=>$label) {
                $result .= ' <label><input type="checkbox"'
                    . ' name="' . $args['name'] . '[]"'
                    . ' value="' . $val . '" ';
                $result .= (in_array($val, $args['value'])) ? 'checked="checked"' : '';
                $result .= ' > ' . $label . '</label> ';
                if ($vertical) $result .= '<br />';

            }

            $result .= '';

            if (isset($args['desc'])) {
                $result .= '<p class="description">' . $args['desc'] . '</p>';
            }

            echo $result;


        }

        public static function get_taxonomy_options($args = array())
        {
            $args = wp_parse_args($args, array(
                'taxonomy' => 'category',
                'hide_empty' => false,
            ));


            $terms = (array)get_terms($args['taxonomy'], array(
                'hide_empty' => $args['hide_empty'],
            ));
            // Initate an empty array
            $term_options = array();
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $term_options[$term->term_id] = $term->name;
                }
            }

            return $term_options;
        }

        public static function info($args = array())
        {
            echo 'info';
        }
    }

}