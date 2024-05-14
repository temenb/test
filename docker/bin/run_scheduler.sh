#!/bin/bash

docker exec -i testtask-php sh -c "php artisan schedule:run"
