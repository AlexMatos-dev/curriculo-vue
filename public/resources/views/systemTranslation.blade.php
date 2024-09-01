<section class="content">
    <div class="text-left mb-3">
        <h1>Add system translations</h1>
    </div>
    <form id="translation-form" action="{{ url('updatesystemtranslations') }}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <input type="hidden" name="token" id="user_token" value="" />
        <div class="col-md-12 m-auto">
            <div class="form-group mt-3">
                <label>Translation file</label>
                <input type="file" accept="json" class="form-control" name="translations" id='translations' required>
            </div>

            <div class="d-flex">
                <a onclick="sendTranslationFile()" class="btn btn-primary bg-primary-color text-white md-m-auto col-12 col-md-4 mt-5">Send translations</a>
            </div>
        </div>
    </form>
</section>

<script>
    function sendTranslationFile(){
        var formData = new FormData(document.getElementById('translation-form'));
        openLoaderModal(false);
        $.ajax({
            url: "{{ url('updatesystemtranslations') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            success: function(response) {
                $('#translations').val('');
                if(response.success === false){
                    showAlert(true, response.message, 'error');
                    return;
                }
                showAlert(true, response.message, 'success');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                showAlert(true, 'An error occurred: ' + errorThrown, 'error');
            },
            complete: function(){
                openLoaderModal(true);
            }
        });
    }
</script>
