function data_aacr2_edit_property(elem) {
    if(elem.metas.socialdb_property_is_aproximate_date){
        $('#socialdb_event_property_is_aproximate_date').prop('checked', true);
    }else{
        $('#socialdb_event_property_is_aproximate_date').prop('checked', false);
    }
}