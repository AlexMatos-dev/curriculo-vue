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
        <script src="{{ asset('js/crypto.js') }}"></script>
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

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/ ;secure; tagname = " + name;
    }
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    function eraseCookie(name) {   
        document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }

    const encryptSecretPass = "<?= $ipUsuario = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']; ?>";
    function encryptText(text = ''){
        if(['',null].includes(text))
            return '';
        return CryptoJS.AES.encrypt(text, encryptSecretPass).toString();
    }
    function decryptText(encryptedText = ''){
        if(['',null].includes(encryptedText))
            return '';
        let result = CryptoJS.AES.decrypt(encryptedText, encryptSecretPass);
        if(result.toString() == '')
            return '';
        return result.toString(CryptoJS.enc.Utf8);
    }
</script>