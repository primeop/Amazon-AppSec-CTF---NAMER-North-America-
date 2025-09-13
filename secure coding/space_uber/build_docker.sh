docker build -t secure_coding_space_uber .
docker run  --name=secure_coding_space_uber --rm -p 80:80 -p 445:445 -it secure_coding_space_uber