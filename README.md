# MEV LLC Tets

Version 0.0.1 Created by Platonov Valerii

## Introduction

Dependencies are managed via composer. Simple run in root of project

php composer.phar install


Code follow PSR-2 coding standards and support PHP 5.5;


## Requirements

Composer;
doctrine 2;


## Installation

Simply clone this project into your directory. Install composer.

/migrate folder consists :

shema DB in PDF format mev_test.pdf
DB dump in mev_test.sql

in Application folder change data/searsh_index w permition for apache
( it should appropriate your OS )
chmod 775
chown wwwrun:www

Copy Application/config/*.dist files to config/autoload wo dist extension.
Put in this files contents yours local DB access parameters.

On search page press link "Regenerate Search Index!" first.
Search query use Wildcards like in 
http://framework.zend.com/manual/1.12/en/zend.search.lucene.query-language.html

for check REST service run
curl -i -H "Content-type: application/json" -X GET -k http://localhost/rest/twitter

OR

Run test from module/Application/test
./../../../vendor/bin/phpunit ApplicationTest/Controller/TwitterRestfullControllerTest.php

Provided Classes
----------------

    'Entity\\User'
    'Entity\\Twitter'

    
    