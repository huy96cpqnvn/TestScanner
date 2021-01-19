#!/bin/bash

cd /var/www/stg-server

docker-compose pull web_ipp_api
docker-compose stop web_ipp_api
docker-compose up -d web_ipp_api
