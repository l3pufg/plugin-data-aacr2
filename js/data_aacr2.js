function data_aacr2_edit_property(elem) {
    if (elem.metas.socialdb_property_is_aproximate_date) {
        $('#socialdb_event_property_is_aproximate_date').prop('checked', true);
    } else {
        $('#socialdb_event_property_is_aproximate_date').prop('checked', false);
    }
    block_multiple_values('#socialdb_event_property_is_aproximate_date');
}

function showContainerApproximate(checkbox, id) {
    if ($(checkbox).is(':checked')) {
        $('#input-date-' + id).hide();
        $('#container-approximate-date-' + id).show();
    } else {
        $('#input-date-' + id).show();
        $('#container-approximate-date-' + id).hide();
    }
}

function block_multiple_values(seletor){
    if($(seletor).is(':checked')){
        $('#meta-date input[value="1"]').attr('checked','checked');
        $('#meta-date input[name="socialdb_property_data_cardinality"]').attr('disabled','disabled');
    }else{
         $('#meta-date input[name="socialdb_property_data_cardinality"]').removeAttr('disabled');
    }
    
}