# 🍎🥕 Fruits and Vegetables
# Discounts Solution

# Introduction
A service that receives a json file of food with types (fruit and vegetable) and save it in separate collections.
After saving the external food data the service provides 2 Apis for listing the saved data and adding new records

The service started with enhancing the curruent project that was provided by adding docker compose and creating other containers for supporting the service needs such as using `mysql` for the database and `nginx` for running the server.

After finishing the implementation the service the project is upgraded to `Symfony 7.1`

# Structure
- `Symfony 7.1` framework
- `php v8.2`
- docker image `php:8.2-fpm` a docker container with the name `roadsurfer_php`.
- database `mysql` installed in a container with name `roadsurfer_mysql`
- nginx for running the server installed in a container with name `roadsurfer_nginx`
- `phpunit` for automation test

# Installation

## Prerequisites
- Install [docker](https://docs.docker.com/engine/install/).
- Install [docker-composer](https://docs.docker.com/compose/install/).

## Installation commands
-  create `.env` in the project root `teamleadercrm-coding-test/problem-1-discounts-solution/discounts`.
- After cloning the project go to the project root directory `fruits-and-vegetables` in the terminal.
- Run `docker-compose up --build`
- After finishing, the server should be running and to check that go to http://localhost:8080
- Run the below command to access the bash of the container.
  `docker exec -it roadsurfer_mysql bash`
- Inside the container run the below commands to complete the installation.
```
composer install

php bin/console doctrine:migration:migrate

php bin/console doctrine:migration:migrate --env=test
```
- Run the tests to make sure the project is running successfully
`bin/phpunit`

# Usage
- Run the below command for importing the food data and reqplace the `[Path-to-file]` with the file `request.json` or any other path to another file of food data.
`php bin/console app:process-food [Path-to-file]`
- If you are using postman you can import [this](./Api-doc/fruites&vegetables.postman_collection.json) postman collection to use Apis.

# Documentation
## Command Implementation
- The command is implemented by truncating the food data and importing them from the begining that's why a confirmation is added to make sure that the information about replacing the whole data is clear.
- The command is reading the file in chunks for handling large size of json files.
- The package `halaxa/json-machine` is used for reading the file in chunks.

Note:
If we'd like add or edit another command should be implemented for the purpose find or create the file data.

## Apis
- Two apis are implemented 
  1. `/api/food/{type}` a get request that will list all food if type is not passed or will list the passed type (fruit or vegetable)
  2. `/api/food/` a post request that creates a new food record.
- Check the file [apis-yaml](./Api-doc/apis.yaml) for the documentation of the apis

## Database
- `roadsurfer_db` database is created with having one table called `food` that has type column (fruit or vegetable)
- `roadsurfer_db_test` database is created for testing. The creation happens using [this file](./db/init-db.sh)
- In `food` table quantity is added with grams for consistancy of the data and saved in `quantity_in_grams` column.
- A thought about The imported `id` is that it can be the id of the food that is passed from an external project or service so it is saved in `external_id` column, and for the current service the autogererated id is kept as it is.

## Code
- Using Doctrine ORM the entity abstract class `App\Entity\Food` is created with adding a `ORM\DiscriminatorColumn` type that is mapped to the two entity classes `App\Entity\Fruit` and `App\Entity\Vegetable`.
- Each class has it's own repository class in order to deal with them as separate collection
- Collection services are added to apply the logic that is needed on a specific entity/collection
- `App\Service\Collection\FoodCollectionService` is implementing `App\Service\Collection\FoodCollectionInterface` for the generic methods that are used for all entities such as `remove` and `list` and logic is applied dynamically on the target entity
- `App\Service\Collection\FruitCollectionService` and `App\Service\Collection\FruitCollectionService` are implementing `App\Service\Collection\FoodTypeCollectionInterface` for the methods that can be used only for sub entities such as `add`
- `App\Service\Resolver\CollectionResolver` class is created to map to the right Collection Service Type dynamically depending on the type.
- `App\Factory\FoodFactory` class is used for creating new record of food dynamically and it is used in the command and the create api.