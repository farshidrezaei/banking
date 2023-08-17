## Banking


## Setup

you must install docker and docker-compose. then:

clone project

```bash
git clone https://github.com/farshidrezaei/banking.git
```

create .env file by copying from .env.example

```bash
cp .env.example .env
```

after create and modify env file you must build service and containers

```bash
docker compose up -d
```

after that all services pulled and started migrate database

```bash
docker exec -it banking-php composer install
```

then generate key

```bash
docker exec -it banking-php php /var/www/html/artisan key:generate
```

then migrate database

```bash
docker exec -it banking-php php /var/www/html/artisan migrate
```





