# Тестовое задание Vl

## Task 1

```shell
git clone https://github.com/bodiia/test_assignment_vl.git <directory>
cd <directory>/task_1
docker build -t console:latest ./

#Usage
docker run --rm -t console ./bin/console sum:count <...directories>

#Run Tests
docker run --rm -t console php ./vendor/bin/phpunit
```