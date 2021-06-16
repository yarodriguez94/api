<?php
require("/var/www/html/zoom/zoombackend/app/Libraries/swagger-generator/vendor/autoload.php");
$openapi = \OpenApi\Generator::scan(['/var/www/html/zoom/zoombackend/app/Controllers/Api/Crm/WSPrivate.php']);
header('Content-Type: application/x-yaml');
echo $openapi->toJson();