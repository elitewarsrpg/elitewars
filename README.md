# elitewars
Elitewars RPG - Open Source Project. 

Currently in development, Elitewars is being built over the wonderful <b>Slim Framework</b> using <b>Twig</b> for the templating system and <b>Eloquent ORM</b> for the database.

The purpose of elitewars is to bring back an old school style game <b>vibe</b>. If you would like to contribute, please get in contact with me @ <b>gratefuldeadty@gmail.com</b>

#Installation 
1. get composer: curl -sS https://getcomposer.org/installer | php
2. install: php composer.phar install

Create a composer.json file in the directory where you just installed composer, the file should look like:

```json
{
	"name": "vendor/elitewars",
	"description": "Elitewars - RPG Game"

    "require": {
		  "slim/slim": "2.6.1",
		  "slim/views": "dev-master",
		  "slim/extras": "dev-master",
		  "twig/twig": "1.18.*@dev",
		  "illuminate/database: "*"
    	}
}
```


