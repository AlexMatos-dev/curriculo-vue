# Jobifull App Backend

### Start

Para Iniciar o ambiente de desenvolvimento execute o comando ``docker compose up -d``

Caso o ambiente esteja sendo executado em um sistema Windows, á preciso ter o WSL2 instalado e estar com o Docker Desktop em execução.

### Docker
As configurações de ambiente estão presentes dentro do arquivo ``docker-compose.yml`` no diretório principal.

### Laravel
Para definir as configurações de ambiente do Laravel, faça um cópia do arquivo ``.env.axample`` e renomeie para ``.env``

Dentro das configurações do arquivo `.env`, definir as configurações a seguir:

#### MySql
``DB_CONNECTION=mysql``<br>
``DB_HOST=jobifull-mysql``<br>
``DB_PORT=3306``<br>
``DB_DATABASE=DATABASE_NAME``<br>
``DB_USERNAME=DATABASE_USERNAME``<br>
``DB_PASSWORD=DATABASE_PASSWORD``<br>

Alterar DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD pelas configurações de banco definidas no ``docker-compose.yml``

<b>ALERT:</b> <i>Ao editar o arquivo .env pela primeira vez, é preciso remove o ``#`` na frentre de alguns parâmetros.</i>

#### Redis (Opcional)

Definir as configurações do Redis conforme a seguir:

``REDIS_HOST=jobifull-redis``

#### Dependências e Migrations

Por fim, executar o ``composer install`` ou ``composer update`` para instalar as dependências do Laravel. E executar o comando ``php artisan migrate`` para executar as configurações de banco de dados e ``php artisan db:seed`` para alimentar algumas tabelas com valores padrões.

#### Configurações adicionais (opcional)

Para ter acesso ao swagger da aplicação, execute os comandos a seguir em sequência ``npm install `` e após ``npm run dev`` ou ``npm run build`` para habilitar o acesso ao SWAGGER atráves da rota "``/swagger``".

#### Dados adicionais

O sistema conta com alguns Seeders que geram dados falsos e aleatórios. Abaixo estão alguns deles:
* Dados de vagas: ``php artisan db:seed --class=CreateFakeJobData``
* Dados de profissionais: ``php artisan db:seed --class=CreateFakeProfessionals``
* Aplicações de vagas para o profissional: ``php artisan db:seed --class=CreateJobApplications``
