@include('config/header')

<section class="content-container">
    <div class="text-left mb-3">
        <h1>Login</h1>
    </div>
    <form id="login-form">
        <div class="col-md-12 m-auto">
            <div class="form-group mb-5">
                <label>Email</label>
                <input type="text" class="form-control" name="email" required>
            </div>
            <div class="form-group mt-3">
                <label>Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="d-flex">
                <a class="btn btn-primary bg-primary-color text-white md-m-auto col-12 col-md-4 mt-5" onclick="login()">Enter</a>
            </div>
        </div>
    </form>
    @include('footer')
</section>
<script>
    const sessionMessage = {
        message: "{{ Session()->get('web_message') }}",
        type: "{{ Session()->get('web_message_type') }}"
    }
    function checkSessionMessage(){
        let hasSessionMessage = sessionMessage.message != '' ? true : false;
        if(hasSessionMessage == true){
            showAlert(true, sessionMessage.message, sessionMessage.type);
            cleanSessionMessage();
        }
    }

    function login(){
        let form = $('#login-form').serializeArray();
        openLoaderModal(false);
        $.ajax({
            url: "{{ url('authenticate') }}",
            data: form,
            method: 'POST',
            dataType: 'JSON',
            success: function(response){
                if(response.success == false){
                    showAlert(true, response.message, 'error');
                    return;
                }
                showAlert(true, response.message, 'success');
                window.location = "{{ url('/swagger') }}"
            },
            complete: function(){
                openLoaderModal(true);
            }
        });
    }
</script>