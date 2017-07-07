<?php
/*
  Plugin Name: Data AACR2
  Version: 1.0
  Description: Funcionalidades extras do Tainacan especialmente para o IBRAM.
  Author: Marcus B. Molinari
  Author URI: https://github.com/medialab-ufg/plugin-data-aacr2
  Plugin URI: https://github.com/medialab-ufg/plugin-data-aacr2
  Text Domain: tainacan
  Domain Path: languages
 */

/*
 *      Copyright 2017 Tainacan <contato@tainacan.org>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 3 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */
load_theme_textdomain("tainacan", dirname(__FILE__) . "/languages");
/* * *************************** */
//Cria o checkbox de para metadados do tipo data
add_action('tainacan_date_aacr2', 'data_aacr2_checkbox', 10, 1);

function data_aacr2_checkbox($type) {
    if ($type == 'date') {
        ?>
        <hr>
        <div class="row col-md-12">
            <input type="checkbox" onchange="block_multiple_values(this)" name="socialdb_event_property_is_aproximate_date" id="socialdb_event_property_is_aproximate_date" value="1" />&nbsp;<?php _e('Allow Proximity Date', 'tainacan'); ?>
        </div>
        <?php
    }
}

/* * *************************** */
//Adiciona um meta para a criação da propriedade de data
add_action('add_new_metas_event_property_data', 'data_aacr2_add_new_metas_event_properties', 10, 2);

function data_aacr2_add_new_metas_event_properties($event_property, $type) {
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_is_aproximate_date', 'socialdb_event_property_is_aproximate_date');
}

/* * *************************** */
add_action('after_event_add_property_data', 'data_aacr2_after_event_add_property_data', 10, 2);
add_action('after_event_update_property_data', 'data_aacr2_after_event_add_property_data', 10, 2);

function data_aacr2_after_event_add_property_data($property_id, $event_id) {
    $proximate = get_post_meta($event_id, 'socialdb_event_property_is_aproximate_date', true);
    if ($proximate) {
        update_term_meta($property_id, 'socialdb_property_is_aproximate_date', true);
    } else {
        update_term_meta($property_id, 'socialdb_property_is_aproximate_date', false);
    }
}

/* * *************************** */
//Adiciona os arquivos JS ao plugin
add_action('wp_enqueue_scripts', 'tainacan_data_aacr2_js');

function tainacan_data_aacr2_js() {
    wp_register_script('tainacan-data-aacr2', plugin_dir_url(__FILE__) . '/js/data_aacr2.js', array('jquery'), '1.11');
    wp_register_script('data-aacr2-masked-input', plugin_dir_url(__FILE__) . '/js/jquery.maskedinput.min.js', array('jquery'), '1.11');
    $js_files = ['tainacan-data-aacr2', 'data-aacr2-masked-input'];
    foreach ($js_files as $js_file):
        wp_enqueue_script($js_file);
    endforeach;
}

/* * *************************** */
add_action('edit_property_metadata', 'data_aacr2_edit_property_metadata');

function data_aacr2_edit_property_metadata() {
    echo 'data_aacr2_edit_property(elem);';
}

/* * ****** Editando o html da propriedade data *********** */
add_action('modificate_insert_item_properties_data', 'data_aacr2_form_item_data_widget', 11, 1);
add_action('modificate_edit_item_properties_data', 'data_aacr2_form_item_data_widget', 11, 1);

function data_aacr2_form_item_data_widget($property) {
    $i = $property['contador'];
    $meta = get_post_meta($property['object_id'], "socialdb_property_{$property['id']}_date", true);
    $type = get_post_meta($property['object_id'], "socialdb_property_{$property['id']}_date_type", true);
    ?>
    <script>
        $(function () {
            $('.exactly_date').mask('00/00/0000', {placeholder: "DD/MM/YYYY"});
            $(".year_year").mask('9999 ou 9999', {placeholder: "YYYY ou YYYY"});
            $(".probably_date").mask('9999?', {placeholder: "YYYY?"});
            $(".between_date").mask('Entre 9999 e 9999', {placeholder: "Entre 9999 e 9999"});
            $(".approximate_date").mask('ca. 9999', {placeholder: "ca. 9999"});
            $(".exactly_decade").mask('999-', {placeholder: "999-"});
            $(".probably_decade").mask('999-?', {placeholder: "999-?"});
            $(".exactly_century").mask('99--', {placeholder: "99--"});
            $(".probably_century").mask('99--?', {placeholder: "99--?"});

            $("#socialdb_property_<?php echo $property['id']; ?>_<?php echo $i; ?>").datepicker({
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                nextText: 'Próximo',
                prevText: 'Anterior',
                showOn: "button",
                buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                buttonImageOnly: true
            });

            $('#select_date_aacr2_<?php echo $property['id']; ?>_<?php echo $i; ?>').change(function () {
                $('#input_date_aacr2_<?php echo $property['id']; ?>_<?php echo $i; ?>').val('');
                $('#input_date_aacr2_<?php echo $property['id']; ?>_<?php echo $i; ?>').removeClass("exactly_date year_year probably_date between_date approximate_date exactly_decade probably_decade exactly_century probably_century").addClass($(this).val());
            });
            //validate
            $(".form_autocomplete_value_" + <?php echo $property['id']; ?>+ '_<?php echo $i; ?>').keyup(function () {
                var cont = 0;
                $(".form_autocomplete_value_" + <?php echo $property['id']; ?> + '_<?php echo $i; ?>').each(function (index, value) {
                    console.log($(this).val(),$(this));
                    if ($(this).val().trim() !== '') {
                        cont++;
                    }
                });
                <?php if(!isset($property['compound_id'])): ?>
                if (cont === 0) {
                    $('#core_validation_' + <?php echo $property['id']; ?>).val('false');
                } else {
                    $('#core_validation_' + <?php echo $property['id']; ?>).val('true');
                }
                set_field_valid(<?php echo $property['id']; ?>, 'core_validation_' + <?php echo $property['id']; ?>);
                <?php else: ?>
                if( cont===0){
                    $('#core_validation_<?php echo $property['compound_id'] ?>_'+<?php echo $property['id']; ?>+ '_<?php echo $i; ?>').val('false');
                    set_field_valid_compounds(<?php echo $property['id']; ?>,'core_validation_<?php echo $property['compound_id'] ?>_'+<?php echo $property['id']; ?>+ '_<?php echo $i; ?>',<?php echo $property['compound_id'] ?>);
                }else{
                    $('#core_validation_<?php echo $property['compound_id'] ?>_'+<?php echo $property['id']; ?>+ '_<?php echo $i; ?>').val('true');
                    set_field_valid_compounds(<?php echo $property['id']; ?>,'core_validation_<?php echo $property['compound_id'] ?>_'+<?php echo $property['id']; ?>+ '_<?php echo $i; ?>',<?php echo $property['compound_id'] ?>)
                }
                <?php endif ?>
            });
            $(".form_autocomplete_value_" + <?php echo $property['id']; ?> + '_<?php echo $i; ?>').change(function () {
                var cont = 0;
                $(".form_autocomplete_value_" + <?php echo $property['id']; ?>+ '_<?php echo $i; ?>').each(function (index, value) {
                    if ($(this).val().trim() !== '') {
                        cont++;
                    }
                });

                <?php if(!isset($property['compound_id'])): ?>
                if (cont === 0) {
                    $('#core_validation_' + <?php echo $property['id']; ?>).val('false');
                } else {
                    $('#core_validation_' + <?php echo $property['id']; ?>).val('true');
                }
                set_field_valid(<?php echo $property['id']; ?>, 'core_validation_' + <?php echo $property['id']; ?>);
                <?php else: ?>
                if( cont===0){
                    $('#core_validation_<?php echo $property['compound_id'] ?>_'+<?php echo $property['id']; ?>+ '_<?php echo $i; ?>').val('false');
                    set_field_valid_compounds(<?php echo $property['id']; ?>,'core_validation_<?php echo $property['compound_id'] ?>_'+<?php echo $property['id']; ?>+ '_<?php echo $i; ?>',<?php echo $property['compound_id'] ?>);
                }else{
                    $('#core_validation_<?php echo $property['compound_id'] ?>_'+<?php echo $property['id']; ?>+ '_<?php echo $i; ?>').val('true');
                    set_field_valid_compounds(<?php echo $property['id']; ?>,'core_validation_<?php echo $property['compound_id'] ?>_'+<?php echo $property['id']; ?>+ '_<?php echo $i; ?>',<?php echo $property['compound_id'] ?>)
                }
                <?php endif ?>
            });
            //se tiver algum valor adicionado
    <?php if ($meta && !empty($meta) && $type): ?>
                $('#socialdb_property_<?php echo $property['id']; ?>_<?php echo $i; ?>').val('');
                $('#container_<?php echo $property['id']; ?>_<?php echo $i; ?>').attr('checked', 'checked');
                $('#input-date-<?php echo $property['id']; ?>').hide();
                $('#container-approximate-date-<?php echo $property['id']; ?>').show();
                $('#socialdb_property_<?php echo $property['id']; ?>_<?php echo $i; ?>').val('<?php echo $meta; ?>');
                $('#select_date_aacr2_<?php echo $property['id']; ?>_<?php echo $i; ?> option[value="<?php echo $type; ?>"]').attr('selected', 'checked');
                $('#select_date_aacr2_<?php echo $property['id']; ?>_<?php echo $i; ?>').trigger('change');
                $('#input_date_aacr2_<?php echo $property['id']; ?>_<?php echo $i; ?>').val('<?php echo $meta; ?>');
    <?php endif; ?>
        });
    </script>    
    <span id="input-date-<?php echo $property['id']; ?>" >
        <input 
            style="margin-right: 5px;" 
            size="13" 
            class="input_date auto-save form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
            value="<?php if ($property['metas']['value']) echo (isset($property['metas']['value'][$i]) ? data_aacr2_get_date_edit($property['metas']['value'][$i]) : ''); ?>"
            type="text" 
            id="socialdb_property_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
            name="<?php if(isset($property['name_field'])): echo $property['name_field'];  else: ?> socialdb_property_<?php echo $property['id']; ?>[] <?php endif; ?>">   
        <br><br>
    </span>
    <?php if (isset($property['metas']['socialdb_property_is_aproximate_date']) && $property['metas']['socialdb_property_is_aproximate_date'] == '1'): ?>
        <input id='container_<?php echo $property['id']; ?>_<?php echo $i; ?>' type="checkbox" onchange="showContainerApproximate(this, '<?php echo $property['id']; ?>')" name="aproximate_date_<?php echo $property['id']; ?>"><?php _e('Allow approximate date', 'tainacan') ?>
        <br>
        <span id="container-approximate-date-<?php echo $property['id']; ?>" class="row" style="display:none;">
            <span class="col-md-2 no-padding">
                <input type="text" 
                       value='<?php echo ($meta) ? $meta : '' ?>'
                       class="form-control data_aproximada form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?> exactly_date" id="input_date_aacr2_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                       name="socialdb_property_<?php echo $property['id']; ?>_approximate_date">
            </span>
            <span class="col-md-3">
                <select id='select_date_aacr2_<?php echo $property['id']; ?>_<?php echo $i; ?>' class="form-control" name="socialdb_property_<?php echo $property['id']; ?>_approximate_date_type">
                    <option value="exactly_date">Data exata- 01/01/1970</option>
                    <option value="year_year">Um ano ou outro - [1971 ou 1972]</option>
                    <option value="probably_date">Data provável - [1969?]</option>
                    <option value="between_date">Entre datas com menos 20 anos de diferença - [entre 1906 e 1912] </option>
                    <option value="approximate_date">Data aproximada -  [ca. 1960] </option>
                    <option value="exactly_decade">Década certa - [197-]</option>
                    <option value="probably_decade">Década provável - [197-?]</option>
                    <option value="exactly_century">Século certo - [18--]</option>
                    <option value="probably_century">Século provável - [18--?]</option>
                </select>
            </span>    
        </span>
    <?php endif; ?>
    <?php
}

function data_aacr2_get_date_edit($value) {
    if (strpos($value, '-') !== false) {
        return explode('-', $value)[2] . '/' . explode('-', $value)[1] . '/' . explode('-', $value)[0];
    } else {
        return $value;
    }
}

add_filter('alter_update_item_property_value', 'update_date_value', 10, 2);

/**
 * 
 * @param type $property
 * @param type $all_data
 */
function update_date_value($property, $all_data) {
    if (isset($property->type) &&
            $property->type == 'date' &&
            isset($all_data['aproximate_date_' . $property->id]) &&
            $all_data['aproximate_date_' . $property->id] == 'on' &&
            $all_data["socialdb_property_" . $property->id . "_approximate_date"] != '') {
        $object_id = $all_data['object_id'];
        delete_post_meta($object_id, "socialdb_property_$property->id");
        if (!empty(trim($all_data["socialdb_property_" . $property->id . "_approximate_date"]))) {
            delete_post_meta($object_id, "socialdb_property_{$property->id}_date");
            add_post_meta($object_id, "socialdb_property_{$property->id}_date", $all_data["socialdb_property_" . $property->id . "_approximate_date"]);
            delete_post_meta($object_id, "socialdb_property_{$property->id}_date_type");
            add_post_meta($object_id, "socialdb_property_{$property->id}_date_type", $all_data["socialdb_property_" . $property->id . "_approximate_date_type"]);
        }
        switch ($all_data['socialdb_property_' . $property->id . '_approximate_date_type']) {
            case 'exactly_date':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $value = explode('/', $date)[2] . '-' . explode('/', $date)[1] . '-' . explode('/', $date)[0];
                add_post_meta($object_id, "socialdb_property_$property->id", $value);
                return true;
            case 'year_year':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $values = explode('ou', $date);
                $first_value = str_replace(' ', '', $values[0]);
                $second_value = str_replace(' ', '', $values[1]);
                if ((int) $first_value < (int) $second_value) {
                    $first_value = $first_value . '-01-01';
                    $second_value = $second_value . '-12-31';
                } else {
                    $second_value = $second_value . '-01-01';
                    $first_value = $first_value . '-12-31';
                }
                add_post_meta($object_id, "socialdb_property_$property->id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property->id", $first_value);
                return true;
            case 'between_date':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $date = str_replace('Entre ', '', $date);
                $values = explode('e', $date);
                $first_value = str_replace(' ', '', $values[0]);
                $second_value = str_replace(' ', '', $values[1]);
                if ((int) $first_value < (int) $second_value) {
                    $first_value = $first_value . '-01-01';
                    $second_value = $second_value . '-12-31';
                } else {
                    $second_value = $second_value . '-01-01';
                    $first_value = $first_value . '-12-31';
                }
                add_post_meta($object_id, "socialdb_property_$property->id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property->id", $first_value);
                return true;
            case 'probably_date':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $year = str_replace('?', '', $date);
                $second_value = $year . '-01-01';
                $first_value = $year . '-12-31';
                add_post_meta($object_id, "socialdb_property_$property->id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property->id", $first_value);
                return true;
            case 'approximate_date':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $year = str_replace('ca. ', '', $date);
                $second_value = $year . '-01-01';
                $first_value = $year . '-12-31';
                add_post_meta($object_id, "socialdb_property_$property->id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property->id", $first_value);
                return true;
            case 'exactly_decade':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $year = str_replace('-', '', $date);
                $second_value = $year . '0-01-01';
                $first_value = $year . '9-12-31';
                add_post_meta($object_id, "socialdb_property_$property->id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property->id", $first_value);
                return true;
            case 'probably_decade':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $year = str_replace('-?', '', $date);
                $second_value = $year . '0-01-01';
                $first_value = $year . '9-12-31';
                add_post_meta($object_id, "socialdb_property_$property->id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property->id", $first_value);
                return true;
            case 'exactly_century':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $year = str_replace('--', '', $date);
                $second_value = $year . '00-01-01';
                $first_value = $year . '99-12-31';
                add_post_meta($object_id, "socialdb_property_$property->id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property->id", $first_value);
                return true;
            case 'probably_century':
                $date = $all_data["socialdb_property_" . $property->id . "_approximate_date"];
                $year = str_replace('--?', '', $date);
                $second_value = $year . '00-01-01';
                $first_value = $year . '99-12-31';
                add_post_meta($object_id, "socialdb_property_$property->id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property->id", $first_value);
                return true;
        }
    }
    return false;
}

################################################################################
//NOVA VERSAO 
add_action('alter_input_date', 'aacr2_alter_input_date', 11, 1);

function aacr2_alter_input_date($array) {
    $compound_id = $array['compound']['id'];
    $property_id = $array['property_id'];
    $index_id = $array['index'];
    $item_id = $array['item_id'];
    if ($property_id == 0) {
        $property = $array['compound'];
    }
    $isRequired = ($property['metas'] && $property['metas']['socialdb_property_required'] && $property['metas']['socialdb_property_required'] != 'false') ? true : false;
    $value_before = $array['value'];
    $hasValue = get_post_meta($item_id, "socialdb_property_{$compound_id}_{$property_id}_date", true);
    $hasType = get_post_meta($item_id, "socialdb_property_{$compound_id}_{$property_id}_date_type", true);
    ?>
        <span id="input-date-<?php echo $property['id']; ?>" style="<?php echo ($hasValue && $hasValue!=='') ? 'display:none;': '' ?>">
        <input 
            style="margin-right: 5px;" 
            size="13" 
            class="input_date auto-save form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
            value="<?php echo ($value_before && $value_before !== '' ? $value_before : ''); ?>"
            type="text" 
            id="date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>" 
         >   
        <br><br>
    </span>
    <?php if ((isset($property['metas']['socialdb_property_is_aproximate_date']) && $property['metas']['socialdb_property_is_aproximate_date'] == '1') || ($hasValue && $hasValue!=='')): ?>
        <input <?php echo ($hasValue && $hasValue !== '' ? 'checked' : ''); ?> id='container_<?php echo $property['id']; ?>_<?php echo $i; ?>' type="checkbox" onchange="showContainerApproximate(this, '<?php echo $property['id']; ?>')" name="aproximate_date_<?php echo $property['id']; ?>"><?php _e('Allow approximate date', 'tainacan') ?>
        <br>
        <span id="container-approximate-date-<?php echo $property['id']; ?>" class="row" style="<?php echo ($hasValue && $hasValue!=='') ? '': 'display:none;' ?>">
            <span class="col-md-2 no-padding">
                <input type="text" 
                       value=""
                       id="date-approximate-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                       value=""
                       class="form-control data_aproximada form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?> exactly_date" 
                       name="socialdb_property_<?php echo $property['id']; ?>_approximate_date">
            </span>
            <span class="col-md-3">
                <select id="date-select-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                        class="form-control" 
                        name="socialdb_property_<?php echo $property['id']; ?>_approximate_date_type">
                    <option <?php echo ($hasType && $hasType == 'exactly_date') ? 'selected':'' ?> value="exactly_date">Data exata- 01/01/1970</option>
                    <option <?php echo ($hasType && $hasType == 'year_year') ? 'selected':'' ?> value="year_year">Um ano ou outro - [1971 ou 1972]</option>
                    <option <?php echo ($hasType && $hasType == 'probably_date') ? 'selected':'' ?> value="probably_date">Data provável - [1969?]</option>
                    <option <?php echo ($hasType && $hasType == 'between_date') ? 'selected':'' ?> value="between_date">Entre datas com menos 20 anos de diferença - [entre 1906 e 1912] </option>
                    <option <?php echo ($hasType && $hasType == 'approximate_date') ? 'selected':'' ?> value="approximate_date">Data aproximada -  [ca. 1960] </option>
                    <option <?php echo ($hasType && $hasType == 'exactly_decade') ? 'selected':'' ?> value="exactly_decade">Década certa - [197-]</option>
                    <option <?php echo ($hasType && $hasType == 'probably_decade') ? 'selected':'' ?> value="probably_decade">Década provável - [197-?]</option>
                    <option <?php echo ($hasType && $hasType == 'exactly_century') ? 'selected':'' ?> value="exactly_century">Século certo - [18--]</option>
                    <option <?php echo ($hasType && $hasType == 'probably_century') ? 'selected':'' ?> value="probably_century">Século provável - [18--?]</option>
                </select>
            </span>    
        </span>
    <?php endif; ?>   
    <?php
    initScriptsDate($compound_id,$property_id,$index_id,$item_id,$isRequired);
    if($hasValue && $hasValue !== ''): 
    ?> 
    <script>
        $('#date-select-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').trigger('change');
        $('#date-approximate-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('<?php echo $hasValue ?>');
        <?php if($isRequired):  ?>
            validateFieldsMetadataPlugin('<?php echo $hasValue ?>','<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
        <?php endif; ?>
    </script>
    <?php    
    endif;    
}


function initScriptsDate($compound_id,$property_id,$index_id,$item_id,$isRequired){
    ?>
    <script>
        $(function () {
            $('.exactly_date').mask('00/00/0000', {placeholder: "DD/MM/YYYY"});
            $(".year_year").mask('9999 ou 9999', {placeholder: "YYYY ou YYYY"});
            $(".probably_date").mask('9999?', {placeholder: "YYYY?"});
            $(".between_date").mask('Entre 9999 e 9999', {placeholder: "Entre 9999 e 9999"});
            $(".approximate_date").mask('ca. 9999', {placeholder: "ca. 9999"});
            $(".exactly_decade").mask('999-', {placeholder: "999-"});
            $(".probably_decade").mask('999-?', {placeholder: "999-?"});
            $(".exactly_century").mask('99--', {placeholder: "99--"});
            $(".probably_century").mask('99--?', {placeholder: "99--?"});

            $("#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>").datepicker({
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                nextText: 'Próximo',
                prevText: 'Anterior',
                showOn: "button",
                buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                buttonImageOnly: true
            });
            
            $('#date-select-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').change(function () {
                $('#date-approximate-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                <?php if($isRequired):  ?>
                    validateFieldsMetadataPlugin('','<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
                <?php endif; ?>
                $('#date-approximate-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').removeClass("exactly_date year_year probably_date between_date approximate_date exactly_decade probably_decade exactly_century probably_century").addClass($(this).val());
            });
            
            $('#date-approximate-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').keyup(function () {
                <?php if($isRequired):  ?>
                    validateFieldsMetadataPlugin($(this).val(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
                <?php endif; ?>
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveValue',
                        type: 'data',
                        plugin:'aacr2',
                        value_plugin:$(this).val(),
                        type_plugin:$('#date-select-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val(),
                        value: '',
                        item_id: '<?php echo $item_id ?>',
                        compound_id: '<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $property_id ?>',
                        index: <?php echo $index_id ?>,
                        indexCoumpound: 0
                    }
                }).done(function (result) {
                    <?php //if($this->isKey): ?>
//                     var json =JSON.parse(result);
//                     if(json.value){
//                        $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
//                            toastr.error(json.value+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
//                     }
                    <?php //endif; ?>
                });
            });
        }); 
        
         function validateFieldsMetadataPlugin(val,compound_id,property_id,index_id){
                if(val == ''){
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id).removeClass('has-success has-feedback');
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id).addClass('has-error has-feedback');
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id+' .glyphicon-remove').show();
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id+' .glyphicon-ok').hide();
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id+' .validate-class').val('false');
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id).val('false');
                }else{
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id).removeClass('has-error has-feedback');
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id).addClass('has-success has-feedback');
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id+' .glyphicon-remove').hide();
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id+' .glyphicon-ok').show();
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id+' .validate-class').val('true');
                    $('#validation-'+compound_id+'-'+property_id+'-'+index_id).val('true');
                    setTimeout(function(){
                        if( $('#validation-'+compound_id+'-'+property_id+'-'+index_id+' .form-control').val()!=''){
                            $('#validation-'+compound_id+'-'+property_id+'-'+index_id).removeClass('has-success has-feedback');
                            $('#validation-'+compound_id+'-'+property_id+'-'+index_id+' .glyphicon-ok').hide();
                        }
                    }, 2000);
                    //mostro a mensagem do proprio metadado
                    console.log( $('#validation-'+compound_id+'-'+property_id+'-'+index_id).parent().parent().find('p .alert-compound-'+property_id));
                    if( $('#validation-'+compound_id+'-'+property_id+'-'+index_id).parent().parent().find('p .alert-compound-'+property_id).length>0)
                         $('#validation-'+compound_id+'-'+property_id+'-'+index_id).parent().parent().find('p .alert-compound-'+property_id).hide();
                    else
                        $('.alert-compound-'+compound_id).hide();
                }
            }
    </script>   
    <?php    
}


add_action('action_save_item', 'aacr2_action_save_item', 11, 1);
function aacr2_action_save_item($data){
    $object_id = $data['item_id'];
    $compound_id = $data['compound_id'];
    $property_children_id = $data['property_children_id'];
    if ($property_children_id == 0) {
        $property_id = $compound_id;
    }else{
        $property_id = $property_children_id;
    }
    $index_id = $data['index_id'];
    if(isset($data['plugin'])){
        if (!empty(trim($data["value_plugin"]))) {
            delete_post_meta($object_id, "socialdb_property_{$compound_id}");
            delete_post_meta($object_id, "socialdb_property_{$compound_id}_{$property_children_id}_date");
            add_post_meta($object_id, "socialdb_property_{$compound_id}_{$property_children_id}_date", $data["value_plugin"]);
            delete_post_meta($object_id, "socialdb_property_{$compound_id}_{$property_children_id}_date_type");
            add_post_meta($object_id, "socialdb_property_{$compound_id}_{$property_children_id}_date_type",$data["type_plugin"]);
        }
        switch ($data["type_plugin"]) {
            case 'exactly_date':
                $date = $data["value_plugin"];
                $value = explode('/', $date)[2] . '-' . explode('/', $date)[1] . '-' . explode('/', $date)[0];
                add_post_meta($object_id, "socialdb_property_$property_id", $value);
                return true;
            case 'year_year':
                $date = $data["value_plugin"];
                $values = explode('ou', $date);
                $first_value = str_replace(' ', '', $values[0]);
                $second_value = str_replace(' ', '', $values[1]);
                if ((int) $first_value < (int) $second_value) {
                    $first_value = $first_value . '-01-01';
                    $second_value = $second_value . '-12-31';
                } else {
                    $second_value = $second_value . '-01-01';
                    $first_value = $first_value . '-12-31';
                }
                add_post_meta($object_id, "socialdb_property_$property_id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property_id", $first_value);
                return true;
            case 'between_date':
                $date = $data["value_plugin"];
                $date = str_replace('Entre ', '', $date);
                $values = explode('e', $date);
                $first_value = str_replace(' ', '', $values[0]);
                $second_value = str_replace(' ', '', $values[1]);
                if ((int) $first_value < (int) $second_value) {
                    $first_value = $first_value . '-01-01';
                    $second_value = $second_value . '-12-31';
                } else {
                    $second_value = $second_value . '-01-01';
                    $first_value = $first_value . '-12-31';
                }
                add_post_meta($object_id, "socialdb_property_$property_id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property_id", $first_value);
                return true;
            case 'probably_date':
                $date = $data["value_plugin"];
                $year = str_replace('?', '', $date);
                $second_value = $year . '-01-01';
                $first_value = $year . '-12-31';
                add_post_meta($object_id, "socialdb_property_$property_id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property_id", $first_value);
                return true;
            case 'approximate_date':
                $date = $data["value_plugin"];
                $year = str_replace('ca. ', '', $date);
                $second_value = $year . '-01-01';
                $first_value = $year . '-12-31';
                add_post_meta($object_id, "socialdb_property_$property_id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property_id", $first_value);
                return true;
            case 'exactly_decade':
                $date = $data["value_plugin"];
                $year = str_replace('-', '', $date);
                $second_value = $year . '0-01-01';
                $first_value = $year . '9-12-31';
                add_post_meta($object_id, "socialdb_property_$property_id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property_id", $first_value);
                return true;
            case 'probably_decade':
                $date = $data["value_plugin"];
                $year = str_replace('-?', '', $date);
                $second_value = $year . '0-01-01';
                $first_value = $year . '9-12-31';
                add_post_meta($object_id, "socialdb_property_$property_id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property_id", $first_value);
                return true;
            case 'exactly_century':
                $date = $data["value_plugin"];
                $year = str_replace('--', '', $date);
                $second_value = $year . '00-01-01';
                $first_value = $year . '99-12-31';
                add_post_meta($object_id, "socialdb_property_$property_id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property_id", $first_value);
                return true;
            case 'probably_century':
                $date = $data["value_plugin"];
                $year = str_replace('--?', '', $date);
                $second_value = $year . '00-01-01';
                $first_value = $year . '99-12-31';
                add_post_meta($object_id, "socialdb_property_$property_id", $second_value);
                add_post_meta($object_id, "socialdb_property_$property_id", $first_value);
                return true;
        }
    }
}
