# Installation with Docker

Describe the easiest way to install the project, as Docker handle all dependencies management.

This installation suppose you already have Docker installed. Check [the Docker Documentation](https://docs.docker.com/) for installation instructions.

## Table of content
- [Installation with Docker](#installation-with-docker)
	- [Table of content](#table-of-content)
  - [First run](#first-run)
  - [Develop with Docker](#develop-with-docker)

# First run

The following 3 commands will download the project, move to the new folder and install and run all the services :

```bash
git clone https://github.com/simde-utc/portail.git # ssh version : git clone git@github.com:simde-utc/portail.git
cd portail
cp .env.docker.example .env # and edit .env.docker.example
chmod -R 777 storage # this command may be required to run again as root to fix permissions issues
docker/run # or "docker/run -d" to run in background
```

4 services will then be run :
 * `back`  : the PHP server with Laravel
 * `front` : NodeJS in watching mode for building the front
 * `proxy` : the Nginx reverse proxy to serve the API as well as statics assets
 * `database` : the MySQL server used by the `back` service

The database will *not* be created by scripts, so you don't forget to run the following commands *after the `back` service is ready* :
```bash
docker/back # Enter a bash shell running in the 'back' container
php artisan portail:clear # Clear cache
php artisan key:generate # Key generation
php artisan migrate:fresh --seed # Tables creation and seeding
exit # It's done. Exit the container
```

The site should be accessible at [localhost:8000](http://localhost:8000), check it out!

# Develop with Docker

Containers and the host *share the project folder*, so you can develop normally without the need of restarting the services.

Only the database will *not* be updated automatically. Remind to run command like the following to update the database.
```bash
docker/back php artisan portail:update
```

Some scripts has been written in order to simplify management of the different services with Docker (assume your terminal is in the root folder of the project) :
 * `docker/compose` : same command as `docker-compose` but with some earlier configuration (set the user id for instance)
 * `docker/run`     : same as `docker/compose up`. Add `-d` to run the run the command in the background.
 * `docker/execute` : execute a command in the given service. If the command is missing, run a bash shell. Syntax : `docker/execute [SERVICE_NAME] <COMMAND>`. Shortcut versions are also available :
   * `docker/back` : same as `docker/execute back`.
   * `docker/front` : same as `docker/execute front`.
   * `docker/proxy` : same as `docker/execute proxy`.
   * `docker/database` : same as `docker/execute database`.
