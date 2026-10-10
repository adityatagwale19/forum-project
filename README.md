# iDiscuss

A small discussion forum written in plain PHP and MySQL. Threads live inside categories, anyone can
read and search them, and a registered member can post threads, comment, and edit or delete their own
posts.

I first wrote this while learning PHP. I have since rebuilt it around prepared statements, hashed
passwords, CSRF tokens and escaped output, with the logic split into small classes under `src/`.

## What it does

- Category list with a real thread count per category
- Thread list per category, paginated
- Thread page with paginated comments
- Search over thread titles and bodies, open to visitors who are not logged in
- Sign up and log in, with the login throttled after five failed tries from one IP
- Post a thread, comment on one, edit or delete your own thread or comment
- Profile page with your post counts and your newest threads
- Contact form that stores messages in the database

## Requirements

- PHP 8.1 or newer with the `pdo_mysql` and `mbstring` extensions
- MySQL 5.7 or newer, or MariaDB
- Composer, only if you want to run the tests. The app itself runs without it.

## Setup

1. Clone the repository and create the database:

   ```sql
   CREATE DATABASE forum_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Load the schema:

   ```bash
   mysql -u root -p forum_website < database/schema.sql
   ```

3. Optional, load the demo data:

   ```bash
   mysql -u root -p forum_website < database/seed.sql
   ```

   Every demo account uses the password `demo1234`. Do not load this file on a public server.

4. Copy `.env.example` to `.env` and fill in your own database details:

   ```bash
   cp .env.example .env
   ```

   `.env` is ignored by git, so your credentials stay on your machine.

5. Serve the `public/` folder. Nothing outside it needs to be reachable from the browser:

   ```bash
   php -S localhost:8000 -t public
   ```

   Then open <http://localhost:8000>.

## Configuration

`.env` keys, all optional because `config/config.php` carries working defaults:

| Key | Default | What it does |
| --- | --- | --- |
| `APP_NAME` | `iDiscuss` | Name shown in the navbar, page titles and footer |
| `APP_DEBUG` | `false` | `true` shows database errors instead of a short message |
| `APP_PER_PAGE` | `10` | Rows per page in thread lists, comments and search |
| `DB_HOST` | `127.0.0.1` | MySQL host |
| `DB_PORT` | `3306` | MySQL port |
| `DB_NAME` | `forum_website` | Database name |
| `DB_USER` | `root` | MySQL user |
| `DB_PASS` | empty | MySQL password |

## Tests

```bash
composer install
vendor/bin/phpunit
```

The suite covers the classes that hold logic and need no database: input validation, CSRF tokens, page
number arithmetic, the `LIKE` escaping used by search, and the output escape helper. GitHub Actions runs
the same suite plus `php -l` over every PHP file on each pull request, on PHP 8.1, 8.3 and 8.4.

## Layout

```
public/      the only folder a browser should reach: pages, css, images
src/         one class per job, loaded by a small autoloader
templates/   layout and partials
config/      config loader and the bootstrap that wires everything up
database/    schema.sql and seed.sql
tests/       PHPUnit tests
```

## How the security side works

- Every query goes through a PDO prepared statement with emulation turned off
- Passwords are stored with `password_hash` and checked with `password_verify`
- An unknown email still runs a hash, so the response time does not say whether the account exists
- Failed logins are counted per IP and blocked for 15 minutes after five tries
- Every form that changes data posts a CSRF token, compared with `hash_equals`
- Logout, delete and edit are POST only, so no link or image tag can trigger them
- Owner checks read the row from the database, never a hidden field in the form
- Everything printed to a page goes through one escape helper
- The session holds only the user id, and the id is regenerated on login

## Still to do

- Screenshots in `docs/screenshots/`
- Search uses `LIKE`, which will slow down on a large thread table. A FULLTEXT index is the next step.
- No tests that talk to a real database yet
- No email yet, so the contact form only stores messages and there is no password reset
