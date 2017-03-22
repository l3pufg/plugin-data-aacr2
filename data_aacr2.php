<?php
/*
  Plugin Name: Data AACR2
  Version: 1.0
  Description: Tainacan extra plugin.
  Author: Tainacan TI Team
  Author URI: http://site.gi.fic.ufg.br/
  Plugin URI: http://site.gi.fic.ufg.br/
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

/* * *************************** */
//Cria o checkbox de para metadados do tipo data
add_action('tainacan_date_aacr2', 'data_aacr2_checkbox', 10, 1);

function data_aacr2_checkbox($type) {
    if ($type == 'date') {
        ?>
        <hr>
        <div class="row col-md-12">
            <input type="checkbox" name="socialdb_event_property_is_aproximate_date" id="socialdb_event_property_is_aproximate_date" value="1" />&nbsp;<?php _e('Allow Proximity Date', 'tainacan'); ?>
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
    $js_files = ['tainacan-data-aacr2'];
    foreach ($js_files as $js_file):
        wp_enqueue_script($js_file);
    endforeach;
}

/* * *************************** */
add_action('edit_property_metadata', 'data_aacr2_edit_property_metadata');

function data_aacr2_edit_property_metadata() {
    echo 'data_aacr2_edit_property(elem);';
}

/******** Editando o html da propriedade data ************/
add_action('modificate_insert_item_properties_data', 'data_aacr2_form_item_data_widget', 11, 1);
add_action('modificate_edit_item_properties_data', 'data_aacr2_form_item_data_widget', 11, 1);
function data_aacr2_form_item_data_widget($property) {
    $i = $property['contador'];
    ?>
           <script>
            $(function() {
                $( "#socialdb_property_<?php echo $property['id']; ?>_<?php echo $i; ?>" ).datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    showOn: "button",
                    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                    buttonImageOnly: true
                });
            });
        </script>    
        <span id="input-date-<?php echo $property['id']; ?>" >
            <input 
                style="margin-right: 5px;" 
                size="13" 
                class="input_date auto-save form_autocomplete_value_<?php echo $property['id']; ?>" 
                value="<?php if ($property['metas']['value']) echo (isset($property['metas']['value'][$i]) ? data_aacr2_get_date_edit($property['metas']['value'][$i]) :''); ?>"
                type="text" 
                id="socialdb_property_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                name="socialdb_property_<?php echo $property['id']; ?>[]">   
            <br><br>
        </span>
        <?php if(isset($property['metas']['socialdb_property_is_aproximate_date']) && $property['metas']['socialdb_property_is_aproximate_date'] == '1'): ?>
            <input type="checkbox" onchange="showContainerApproximate(this,'<?php echo $property['id']; ?>')" name="aproximate_date"><?php _e('Allow approximate date','tainacan')  ?>
            <br>
            <span id="container-approximate-date-<?php echo $property['id']; ?>" class="row" style="display:none;">
                <span class="col-md-2 no-padding">
                    <input type="text" class="form-control">
                </span>
                <span class="col-md-3">
                    <select class="form-control">
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
function data_aacr2_get_date_edit($value){
    if(strpos($value, '-')!==false){
         return explode('-', $value)[2].'/' .explode('-',$value)[1].'/' .explode('-',$value)[0];
    }else{
        return $value;
    }
}


