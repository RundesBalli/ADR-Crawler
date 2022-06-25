# :radioactive: :clipboard: ADR-Crawler
Ambient dose rate crawler to fetch the ambient dose rate of around 1700 measurement stations in Germany and around 110 measurement stations in Austria.

## Setup / Requirements
This crawlers are written in PHP, use a SQL database and are used in CLI-Mode with cURL. Install requirements and rename the `config.template.php` to `config.php` in the respective directorys and fill in the variables.
```
sudo apt install php-cli php-curl
cd <de or at>
mv config.template.php config.php
nano config.php
```
After that import the .sql file into your selected database of your choice.

## Cronjob
Set up a cronjob that runs the crawler 1x per hour at quarter past the hour:
```
crontab -e

15 * * * * /usr/bin/php /path/to/adr/crawler.php > /dev/null
```

### Informations to the German data
The data collected by this crawler falls under certain terms of use, which can be viewed [here](https://www.imis.bfs.de/geoportal/resources/sitepolicy.html).  
<sub>I explicitly distance myself from the Federal Office for Radiation Protection of Germany and explicitly point out that this is neither official software nor provided by the Federal Office for Radiation Protection of Germany. I act as an interested, non-expert private person.</sub>
