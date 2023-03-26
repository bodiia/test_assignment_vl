# Тестовое задание Vl

```shell
#Download
git clone https://github.com/bodiia/test_assignment_vl.git <directory>
```
## Task 1

```shell
cd <directory>/task_1

#Build
docker build -t console:latest ./

#Usage
docker run --rm -t console ./bin/console sum:count <...directories>

#Run Tests
docker run --rm -t console php ./vendor/bin/phpunit
```

## Task 2

```shell
cd <directory>/task_2

#Build
docker build -t comments:latest ./

#Run Tests
docker run --rm -t comments php ./vendor/bin/phpunit
```