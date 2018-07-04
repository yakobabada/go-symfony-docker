Coupons
==================
This application displays list of Coupons in JSON format `API`. The coupon display once and then remove from the storage.

It's built on 'PHP', 'MYSQL', 'NGINX', 'GO', 'LOG' microservices build on docker environment.

PHP container requests GO container copoun data to display it.

# Requirements

* [docker](https://docs.docker.com/install/)


# Framework

* PHP -> Symfony 4.0
* GO -> Gin

# Download

    git clone https://github.com/yakobabada/go-symfony-docker.git

# Installation

    cd go-symfony-docker/
    docker-compose build
    docker-compose up -d
    docker exec -it php_app sh
    composer install

* open the `.env` file to update mysql params
    `vi .env`

    `DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name` -> `DATABASE_URL=mysql://app:password@mysql/symfony`

* Run migrations

    `bin/console doctrine:migrations:migrate`

# Run

Open your terminal and paste this url `http://localhost:8080/coupons?limit=1`.

* filters: brand, value. Ex: `http://localhost:8080/coupons?brand=Tesco&value=3`
