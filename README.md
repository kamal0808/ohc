# ohc
Programming language that features programming in native Hindi language

Writing programs in native languages is  made possible with this project.
Online Hindi Compiler supports writing syntax in Hindi language, and doing basic
programming that involves taking user inputs, generating outputs, using conditional
if, if-else and looping statements.

Contributions to the project are welcome.

###Prerequisites:
* [Docker] (https://www.docker.com)    
* [Docker Get Started Guide](https://www.docker.com/products/docker)

###Steps to run:
1) Install [Docker](https://www.docker.com)    
2) Run this command `docker pull shoppinpaldevops/online-hindi-compiler`    
3) After the image is pulled, start server `docker run -d -p 80:80 --name ohc shoppinpaldevops/online-hindi-compiler`    
4) In your web browser, go to `http://localhost:80`.     
5) Copy any of sample hindi programs from the folder `Sample Hindi Programs`.     
6) Run the program, or create your own program using instructions mentioned in the landing page.   

###Dockerfile Explained
* Docker helps set up a php-apache configuration as specified in the Dockerfile.
* `FROM php:7.0-apache` pulls a docker image that containes php7 and apache server bundled together
* `COPY src/ /var/www/html/` copies all contents inside `src` folder to `/var/www/html/`, so that apache server can serve these project files. Defuault path for apache to serve files is `/var/www/html/`.

###Steps to build docker image
* Save Dockerfile with the commands mentioned above.
* Build a docker image using `docker build -t online-hindi-compiler .` This creates an image named `online-hindi-compiler`. 
* Login to docker using `docker login`. You need a docker hub account for this. Create one if you haven't.
* Find your `IMAGE ID` using `docker images`. It displays a list of docker images installed in your system.
* Tag the built image with your docker hub username into a repo. Run `docker tag 05b6e61c6b24 shoppinpaldevops/online-hindi-compiler` where `05b6e61c6b24` is the `IMAGE ID` and `shoppinpaldevops` is the username.
* Push the newly tagged image to docker hub using `docker push shoppinpaldevops/online-hindi-compiler`.
* Share the published repo alongwith installation instructions as mentioned above.
