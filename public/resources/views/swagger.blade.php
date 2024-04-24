<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InfoIJobs API-Swagger</title>
</head>
<body>
    <div id="swagger-api"></div>
    @vite('resources/js/swagger.js')
</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        let interval = setInterval(function(){
            let titleEl = document.getElementsByClassName('title');
            if(titleEl.length > 0){
                titleEl[0].nextSibling.remove();
                clearInterval(interval);
            }
        },1000);
    });
</script>