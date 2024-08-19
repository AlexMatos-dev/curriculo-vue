<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ env('APP_NAME') }} - Conectando Profissionais com Empresas Internacionais</title>
        <link rel="icon" href="{{ asset('images/logo.webp') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrapicons.css') }}">

        <script src="{{ asset('js/jquery.js') }}"></script>
        <script src="{{ asset('js/bootstrap.js') }}"></script>
        <script src="{{ asset('js/loader.js') }}"></script>
    </head>
</html>

@include('components.alert')

@include('components.loader', ['hidden' => true])

<script>
    $(document).ready(function(){
        /* Ajax Area */
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

    });

    function cleanViewMessage(){
        $.ajax({
            url: "{{ url('cleanviewmessage') }}",
            method: 'POST'
        });
    }


</script>