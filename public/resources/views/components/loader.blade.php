<div class="modal fade" id="loaderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loaderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="text-center m-auto pt-3 pb-3">
            <img class="col-3" src="{{ asset('images/preloader.gif') }}" alt="loading icon">
            <span class="ms-2 h3">carregando...</span>
        </div> 
      </div>
    </div>
</div>

<script>
    var preloader       = "{{ asset('images/preloader.gif') }}";
    var loaderComponent = "<div class='text-center m-auto'><img src='"+preloader+"' alt='loading icon'></div>";
    function getLoaderComponent(){
        return loaderComponent;
    }
</script>