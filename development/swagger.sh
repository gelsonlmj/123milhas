#!/bin/bash

#mkdir ../public/swagger

php ../vendor/bin/openapi --bootstrap ./swagger-variables.php --output ../public/swagger/swagger.json ./swagger-v1.php ../app/Http/Controllers