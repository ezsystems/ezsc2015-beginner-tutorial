#!/bin/bash

mysql ezplatform -usummer -pcamp < installation/ezplatform.sql
composer install -n
