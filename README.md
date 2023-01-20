# TelTech &mdash; Projet web

## About

A simple e-commerce website made for my web development
classes. Built with:

-   **PHP 8.1**: RESTfull back-end
-   **MySql 8**: Database
-   **Angular 15**: Front-end

## How to run

To run the project, follow the following steps once cloned:

### Install the requirements

#### Back end

On the server running the app you will need a LAMP stack (Linux, Apache, MySql 8+ and PHP 8.1+).
Make sure to install curl for PHP with the following command:

```bash
sudo apt install php8.1-curl # adapt to the version of PHP that you are using
```

#### Front end

To build the front end app, you will need NodeJS 19+.
Set your location to the directory you just clonned and install the
requirements with this command:

```bash
npm install
```

### Configure the database

Run the following command as root user in MySql (using `mysql` tool):

```sql
SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
```

Create or use an existing user account, the create the required database:

```sql
CREATE DATABASE tel;
```

Switch over to the database:

```sql
USE tel;
```

Then run the schema creation script found under `sql/schema.sql` to create all the
required tables.

### Build the project

```bash
npm run build
```

The build artifacts can be found in the `dist` folder.

### Final setup

Move the content to the build artifacts to your apache DocumentRoot, then, in the same directory
where the DocumentRoot is located:

-   Create a folder named `uploads` and give read and write rights to PHP (using `chmod`)
-   Create a file named `config.php` following the template of the `config.example.php` file

Congradulations, the website is up and running !

## Author

Raphaël Caldwell &ndash; [itskatt](https://github.com/itskatt)

## License

See [LICENSE](LICENSE)
