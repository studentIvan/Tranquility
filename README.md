# ![Tranquility](http://habrastorage.org/storage2/cef/853/3d7/cef8533d75bb0f8f014282a7a3c81668.png)
## Content Management Framework
## Version 1.0.0.2 [![endorse](http://api.coderwall.com/studentivan/endorse.png)](http://coderwall.com/studentivan)

### Features
* Easy to install and use
* Normal work on cheap hostings
* Web/PDA automatic difference
* Solutions difference
* BSD license

### Composition
* Bootstrap 3 RC2 + jQuery 2
* Twig template engine + plural ends extension
* Uri-match routing + basic application architecture
* Many global singletons (oh god, why?)
* LocalizedDate.JS

### Requirements
* php 5.2.4
* pdo mysql
* mb_ (optional)

### Production install
* Turn off developer mode in config/config.php

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