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


