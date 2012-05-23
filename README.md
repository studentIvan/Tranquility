# ![Tranquility](http://habrastorage.org/storage2/cef/853/3d7/cef8533d75bb0f8f014282a7a3c81668.png)
## Content Management Framework
## Version 0.4.0.1 [![endorse](http://api.coderwall.com/studentivan/endorse.png)](http://coderwall.com/studentivan)

### Features
* Easy to install and use
* Normal work on cheap hostings
* Web/PDA automatic difference
* Solutions difference
* BSD license

### Standard solution features
* News posting (also can be used for blog)
* Session monitor (referers, browsers, etc)
* Easy "Session" php api (Session::getToken(), Session::start(), Session::authorize(...), etc)
* Half ready site

### Composition
* Twig template engine + plural ends extension
* Uri-match routing + basic application architecture
* Many global singletons (oh god, why?)
* TinyMCE visual editor (disabled for news by default)
* JQuery mobile + power admin panel
* LocalizedDate.JS

### Requirements
* php 5.2.4
* pdo mysql
* mb_ (optional)

### Production install
* Turn off developer mode in config/config.php
* Install tinymce (optional): unzip js/tiny_mce.zip in js directory and set tinymce_editor => true in config/config.php

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