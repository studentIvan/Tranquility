# ![Tranquility](http://habrastorage.org/storage2/cef/853/3d7/cef8533d75bb0f8f014282a7a3c81668.png)
## Content Management Framework
## Version 1.2.0.0 RC 21

### Features
* Easy to install and use
* Normal work on cheap hostings
* Web/PDA automatic difference
* Solutions difference
* BSD license

### Composition
* Twitter Bootstrap 3 No-Icons + Font Awesome
* jQuery 2 + Tags Input
* Twig template engine
* Uri-match routing + basic application architecture
* Many global singletons (oh god, why?)
* LocalizedDate.JS
* BatmanHand.JS

### Requirements
* php 5.2.4 +
* pdo mysql
* mb_ (optional)

### Production install
* Turn off developer mode in config/base.json

### Example apache configuration
```apache
<VirtualHost *>
    ServerName turbo.local
	DocumentRoot "/path/to/tranquility/webroot"

	<Directory /path/to/tranquility/webroot>
        DirectoryIndex index.php index.html
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

### Cheap hosting webroot placement
You can rename webroot so as you like. For example: htdocs (as default hosting directory) or www

### Birthday
16th April 2012

### Testing
* For tests u need a PHPUnit
* Example run: ```php ide-phpunit.php --configuration /projects/mysite/testing/phpunit.xml```
* Or just right-click on testing/phpunit.xml and select 'Run it (Run phpunit.xml)' in your professional IDE