<div class="alert alert-success hidden-content col-md-10 m-auto mt-2 mb-3" role="alert">
    <div class="d-flex">
        <span class="alert-message col-11"></span>
        <i class="text-end w-100 hide-alert bi bi-x-circle cursor-pointer" onclick="showAlert(false, '', 'success')"></i>
    </div>
</div>
<div class="alert alert-danger hidden-content col-md-10 m-auto mt-2 mb-3" role="alert">
    <div class="d-flex">
        <span class="alert-message col-11"></span>
        <i class="text-end w-100 hide-alert bi bi-x-circle cursor-pointer" onclick="showAlert(false, '', 'error')"></i>
    </div>
</div>

<script>
    function showAlert(show = true, message = '', type = ''){
        $('.alert').addClass('hidden-content');
        $('.alert').find('.alert-message').text('');
        let el = null;
        switch(type){
            case 'success':
                el = $('.alert-success');
            break;
            case 'error':
                el = $('.alert-danger');
            break;
        }
        if(el != null){
            if(show == true){
                el.find('.alert-message').text(message);
                el.removeClass('hidden-content');
            }else{
                el.addClass('hidden-content');
            }
        }
    }
</script>