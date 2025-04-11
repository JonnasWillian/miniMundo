*<b> Atenção todos os comandos e ações devem ser feitos no container do laravel 'mini-mundo-app' <b>*

- *Configuração do ambiente:
- Para iniciar o projeto utilize o comando no diretório inicial e construa e inicie os containers:
'docker compose build'
'docker compose up -d'
Ele estará rodando em http://localhost:8081

- *Váriaveis de ambiente:
- Copie o arquivo '.env.example' e remova o '.example' do final

- *Configurando projeto:
- Acesse o container e execute os seguintes comandos para iniciar
- 1: 'composer install' (para instalar as depências do laravel)
- 2: 'php artisan migrate' (para criarmos o banco de dados e as tabelas necessárias do projeto)
- 3: 'npm install' (instalar as depedências para o front-end)
- 4: 'npm run build' ou npm run dev (carregar o build do projeto)

- *Teste úntário:
- Execute o comando: php artisan test

- *Seeders
- Caso queira facilitar os teste, utilize seeders para acelerar o processo, primeiro limpe o banco para evitar erro: 'php artisan migrate:fresh'
- Depois execute a seeder 'php artisan db:seed'