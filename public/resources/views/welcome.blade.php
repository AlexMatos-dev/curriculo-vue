<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ env('APP_NAME') }} - Conectando Profissionais com Empresas Internacionais</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container">
            <header>
                <h1>Bem-vindo ao Jobifull</h1>
            </header>
            <section class="intro">
                <p>Conectando profissionais com empresas internacionais.</p>
                <p>Você pode encontrar vagas de empresas do exterior ou no seu país. O site traz vários dados que podem ser preenchidos, melhorando a experiência do profissional que busca vagas.</p>
                <p>Para as empresas, o sistema possibilita encontrar profissionais qualificados e relevantes para as suas vagas, assim como possuir recrutadores e poder gerar uma rede de profissionais como o LinkedIn.</p>
            </section>
            <section class="access">
                <h2>Como acessar o sistema</h2>
                <p>O sistema Jobifull pode ser acessado através do seguinte link: <a href="https://frontend.jobifull.com/" target="_blank">https://frontend.jobifull.com/</a>.</p>
                <p>A documentação da API pode ser acessada através do link <a href="swagger" target="_blank">Swagger</a>.</p>
            </section>
            <footer>
                <p>&copy; 2024 {{ ucfirst(env('APP_NAME')) }}. Todos os direitos reservados.</p>
            </footer>
        </div>
        <script src="script.js"></script>
    </body>
</html>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        color: #333;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    header {
        text-align: center;
        margin-bottom: 20px;
    }

    h1 {
        color: #2c3e50;
    }

    .intro p {
        line-height: 1.6;
        margin: 10px 0;
    }

    footer {
        text-align: center;
        margin-top: 20px;
        font-size: 0.9em;
        color: #777;
    }
</style>