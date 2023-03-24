# Тестовое задание Vl

## Task 1

```shell
git clone https://github.com/bodiia/test_assignment_vl.git <directory>
cd <directory>/task_1
docker build -t console:latest ./
docker run --rm -t console ./bin/console sum:count <...directories>
```