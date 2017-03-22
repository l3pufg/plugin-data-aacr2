function data_aacr2_edit_property(elem) {
    if(elem.metas.socialdb_property_is_aproximate_date){
        $('#socialdb_event_property_is_aproximate_date').prop('checked', true);
    }else{
        $('#socialdb_event_property_is_aproximate_date').prop('checked', false);
    }
}


            
function showContainerApproximate(checkbox,id){
     if($(checkbox).is(':checked')){
         $('#input-date-'+id).hide();
         $('#container-approximate-date-'+id).show();
     }else{
          $('#input-date-'+id).show();
         $('#container-approximate-date-'+id).hide();
     }
}