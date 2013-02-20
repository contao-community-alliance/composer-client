Composer
========

Mini module which loads the composer autoloader, creates initial composer.json

### Installation

1. Do a normal page request, this will prepare the folder structure and the default composer.json
2. Go into the composer folder within a terminal (there is no windows support atm)

```
cd path/to/my/project/documentroot/composer
```

3. Download composer as explained here: http://getcomposer.org/doc/00-intro.md#downloading-the-composer-executable

```
curl -sS https://getcomposer.org/installer | php
```

4. Add some wished modules, libs, ... into the composer.json as explained here: http://getcomposer.org/doc/04-schema.md
5. Install those wished vendors

```
php composer.phar install
```

### Requirements
* php 5.3.4 or higher
* php command line and the access to use it
* contao 2.11.*