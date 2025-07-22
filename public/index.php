<?php

require_once '../vendor/autoload.php';

require_once '../app/config/boostrap.php';

require_once '../routes/routes.web.php';

use App\Core\Router;

Router::resolve($routes);

