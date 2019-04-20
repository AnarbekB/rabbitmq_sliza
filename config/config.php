<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('HOST', 'localhost');
define('PORT', 5672);
define('USER', 'guest');
define('PASS', 'guest');
define('VHOST', '/');

define('EXCHANGE', 'router_test');
define('QUEUE', 'msgs_test');
define('CONSUMER_TAG', 'consumer_test');

define('AMQP_DEBUG', false);
