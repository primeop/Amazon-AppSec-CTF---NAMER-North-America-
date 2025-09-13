#!/bin/bash
docker build --tag=web_evil_reviews .
docker run -it -p 1337:80 --rm --name=web_evil_reviews web_evil_reviews

