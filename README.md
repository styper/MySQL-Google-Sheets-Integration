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
composer install
```

### Running the program

Change the config.ini.php file to suit your environment

Add an SQL query to the script.sql file

Download the JSON file from Google API console containing the authentication information for a service account

Run the index.php file

```
/path/to/php /path/to/index.php
```

### Logs

Either run the /path/to/php /path/to/index.php manually and check the console output or send the output to a log file: /path/to/php /path/to/index.php > /path/to/log.txt

### Creating a Google API service account

Go to https://console.developers.google.com

Create a project

Enable the Google Sheets API at the Dashboard

Go to Credentials

Click Create credentials -> Service account key

Define an account name and select JSON as the key type

Define the role as Project->Service Account Actor

After you finish the JSON file is downloaded automatically

Remember to move the JSON file to the project folder and share the sheet with read/write permissions to the service account you just created
