### Welcome to the civilsbudget wiki! [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/civils-council/civilsbudget/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/civils-council/civilsbudget/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/civils-council/civilsbudget/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/civils-council/civilsbudget/?branch=master) [![Build Status](https://travis-ci.org/civils-council/civilsbudget.svg?branch=master)](https://travis-ci.org/civils-council/civilsbudget) 

Hi :) My name is Vladimir Dybenko.
I am happy to share my idea with you.

We build a new principles in government management. The first principle is direct voting for initiatives.  
This application  helps implement direct voting for projects. 

Let's create a civil oriented world together!

## How it works

Roles: Voter, Project Owner, Observers, Admins

Admin create a new **set of projects** and define

  * title,
  * description,  
  * location (it could be country, region,city/village, a district of the city or street)  
  * start date and finish date for voting,  
  * rules of voting (One choice, multiple choice) 
  * and rules for author of projects (anyone, only someone who matched to location). 

Define observers for this set of project. Public this set of project. All user who are matched to location of set of project will receive notification and rules of this voting.  

Project Owner posted a new project into set of projects.  

Observer receive notification about new project. 

Observer approve or disapprove this project for publishing.

# Docker commands

### Initial commands to initiate docker-containers

```bash
docker-compose build --build-arg host_uid=$(id -u) --build-arg civils-app
docker-compose build nginx
docker-compose up -d
```

### Composer install 
```bash
docker exec -ti $(docker ps -aqf "name=civils-app") composer install -d /var/www/civils-app
```

### Doctrine migration
```bash
docker exec -ti $(docker ps -aqf "name=civils-app") /var/www/civils-app/bin/console d:m:m
```

### To build front run:
```bash
docker-compose run --rm node npm i 
docker-compose run --rm node ./node_modules/.bin/bower install 
docker-compose run --rm node ./node_modules/.bin/gulp
```

Finally project locally available: http://localhost:8080