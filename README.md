# MySQL Google Sheets Integration

Queries a MySQL database and sends the results to a Google Spreadsheet

### Prerequisites

```
Composer
PHP 5.6
MySQL 5.5
```

### Installing

Run Composer on the project folder to download the Google API libs

```
Composer install
```

### Running the program

Change the config.ini.php file to suit your environment

Add an SQL query to the script.sql file

Run the index.php file

```
/path/to/php /path/to/index.php
```

### Logs

Either run the /path/to/php /path/to/index.php manually and check the console output or send the output to a log file: /path/to/php /path/to/index.php > /path/to/log.txt
