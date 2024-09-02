## WIP | PET PROJECT
***

A project for testing the Neo4J graph database, featuring a small blog with user authentication (including the ability to add friends), posts (with the option to like them), using a combination of PostgreSQL and Neo4J (for friends and likes).

**ToDo:**
1. add comments (an equivalent of "retweets")
2. attachments for posts
3. update user info endpoint
4. views of posts
5. maybe analytics system (Clickhouse DB)
6. maybe recommendation algorithm  

***
Stack: PHP 8.3, Laravel 11 (Octane, Sanctum, Sail, Reverb), Vite (node.js 20),

DB: PostgreSQL 15, Neo4j, Redis

Mail: mailhog

DB admin: adminer

Libs: [Telescope](https://laravel.com/docs/11.x/telescope), [Horizon](https://laravel.com/docs/11.x/horizon), [internachi/modular](https://github.com/InterNACHI/modular), [scramble de:doc](https://scramble.dedoc.co/)

### Startup:
```bash
git clone git@github.com:m1n64/laravel-11-docker-template.git my-project
```

Next,
```bash
cd my-project
```
```bash
cp .env.example .env
```

Change your database name in `.env` `DB_DATABASE`

```bash
chmod 755 ./sail
```
```bash
chmod 755 ./rr
```
Next, change docker network and `-l11` postfix in containers name in `docker-compose.yml` and `docker-compose.dev.yml`

```bash
./sail -f docker-compose.yml -f docker-compose.dev.yml up -d --build
```
```bash
./sail composer install
```
```bash
./sail npm i
```
```bash
./sail artisan key:generate
```
```bash
./sail artisan migrate --seed
```
Reload app:
```bash
./sail stop
```
```bash
./sail -f docker-compose.yml -f docker-compose.dev.yml up -d
```

App successfully installed!

App API url: [http://localhsot/](http://localhost/docs/api)

Neo4j Browser: [http://localhost:7474/](http://localhost:7474/) - user `neo4j`, password `neo4jpassword`

Telescope url: [http://localhsot/telescope](http://localhost/telescope)

Horizon url: [http://localhsot/horizon](http://localhost/horizon)

Mailhog url: [http://localhsot:8025](http://localhost:8025)

***
