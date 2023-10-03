
# Arthaloka Backend

Sebuah backend untuk aplikasi manajemen ATM sebuah bank


## Tech Stack

**Client:** Vue, Quasar

**Server:** PHP, Laravel


## Installation

Clone this project

```bash
  git clone https://github.com/rikhoari01/arthaloka-backend.git
```

Install depedencies

```bash
  cd arthaloka-backend
  composer Install
```
Setting environment

```bash
  cp .env.example .env
  php artisan key:generate
  php artisan jwt:secret
```

Setting Database

```bash
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=arthaloka
  DB_USERNAME=root
  DB_PASSWORD=
```

```bash
  php artisan migrate --seed
```

Running server

```bash
  php artisan serve
```
## API Documentation

[https://documenter.getpostman.com/view/30044941/2s9YJc33zf](https://documenter.getpostman.com/view/30044941/2s9YJc33zf)


## License

[MIT](https://choosealicense.com/licenses/mit/)

