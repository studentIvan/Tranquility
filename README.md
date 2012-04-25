# Tranquility
## Content Management Framework
## Version 0.0.9 [![endorse](http://api.coderwall.com/studentivan/endorse.png)](http://coderwall.com/studentivan)

### Features
* News posting
* JQuery Mobile admin panel
* Easy to install and use
* Normal work on cheap hostings
* Web/PDA automatic difference

### Components
* Twig template engine
* Uri-match routing + basic application architecture

### Requirements
* php 5.2.4
* pdo mysql

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