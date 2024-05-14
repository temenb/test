#!/usr/bin/env bash

CONTAINER_NAME="testtask-postgres";

bash -c "docker exec -i ${CONTAINER_NAME} bash -c \"psql -U postgres app_db < /dumps/latest_dump.sql\"";
