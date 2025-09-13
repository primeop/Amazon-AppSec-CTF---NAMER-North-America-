docker build -t secure_coding_cor .
docker run  --name=secure_coding_cor --rm -p 80:9000 -p 445:445 -p 1337:1337 -it secure_coding_cor
