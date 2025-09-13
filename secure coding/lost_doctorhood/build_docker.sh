docker build -t lost_doctorhood .
docker run  --name=lost_doctorhood --rm -p 80:3000 -p 445:445 -it lost_doctorhood