# filterable-news
filterable-news is a mysql/php/javascript powered news collector and presenter. Feel free to report bugs, suggest features and contribute. A live version with Turkish news can be found at [haber.keremkayacan.com](https://haber.keremkayacan.com)

## Features
- Backend developed with [CodeIgniter](https://codeigniter.com/)
- Frontend developed with [OpenUI5](https://openui5.org/)
- Collects news from [Google News RSS](https://news.google.com/rss)
- News links parsed with [Simple HTML Dom Parser](https://simplehtmldom.sourceforge.io/)
- Backend can be called as a separate json rest api
- News can be filtered by time frame
- Filters are bookmarkable
- Sorts news by source count

## Requirements
- PHP 5.6+
- MySQL 5.1+
- mod_rewrite for Apache server must be enabled for CodeIgniter routing

## Installation
1. Allow override and set environment to production for virtual host. For Apache server:
```
sudo nano /etc/apache2/apache2.conf
```
Enter this:
```
<Directory /var/www/example.com/public_html/>
    AllowOverride All
	SetEnv CI_ENV production
</Directory>
```
2. Clone the repository and change directory name to make filterable-news/public_html your document root.
```
git clone https://github.com/kkayacan/filterable-news.git
```
3. Create a new MySQL database and import file sql/newsdb.sql
4. Copy application/config/config.php to application/config/production/config.php
5. Open the application/config/production/config.php file with a text editor and set your base URL.
```
$config['base_url'] = 'https://example.com/api/';
```
6. Copy application/config/database.php to application/config/production/database.php
7. Open the application/config/production/database.php file with a text editor and set your database info.
8. Set a cron job to start collecting news, ie every 20 minutes:
```
crontab -e
```
```
*/20 * * * * /usr/bin/curl --silent https://example.com/api/collector/fetch >/dev/null 2>&1
```