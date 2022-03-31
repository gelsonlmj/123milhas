123Milhas
===================================

Esse projeto disponibiliza apis para consultar voos, grupos de voos(ida/volta) disponíveis, grupo com o preço mais barato e informações gerais sobre os voos.

Tecnologias Utilizadas
----------------------

- [Laravel](https://lumen.laravel.com/)
- [Github](https://github.com/)
- [Compose](https://docs.docker.com/compose/)
- [Swagger](https://swagger.io/)

Instalação
-----------

1. Clonar o repositório:

    ```sh
    git clone https://github.com/gelsonlmj/123milhas.git
    ```

- Executar o composer

    ```sh
    composer install --optimize-autoloader --no-dev
    ```

- Criar arquivo .env

    ```sh
    cp .env.example .env
    ```

- Dar permissão de acesso a pasta storage

    ```sh
    chmod -R 777 storage
    ```

- Gerar chave da aplicação

    ```sh
    php artisan key:generate
    ```

- Configurar o cache de configurações

    ```sh
    php artisan config:cache
    ```

- Configurar o cache de rotas

    ```sh
    php artisan route:cache
    ```

API
--------------

Para ter acesso a api basta acessar a seguinte url http://127.0.0.1/api/flights

Swagger
--------------

- Executar a criação do swagger

    ```sh
    cd development
    ./swagger.sh
    ```

- Acessar o swagger

    - http://localhost/swagger/
