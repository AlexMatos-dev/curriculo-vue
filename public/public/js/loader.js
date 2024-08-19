function openLoaderModal(open = false, id = 'loaderModal'){
    if(open == false){
        $('#'+id).modal('show');
    }else{
        $('#'+id).modal('hide');
    }
}