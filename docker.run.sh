#!/bin/bash

docker run \
--rm \
-p 80:80 \
-v $(pwd):/var/www/html \
php:5.6-apache