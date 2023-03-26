# Тестовое задание Vl

```shell
#Download
git clone https://github.com/bodiia/test_assignment_vl.git <directory>
```
## Task 1

Так как в условии задания не указан формат содержимого файла, то я предположил следующий:
```shell
1 2
3 -2 asd sd2
```
Отрицательные числа так же будут учитываться при подсчёте.

Так как использую рекурсивный итератор, то переданные директории консольной команде фильтруются на вложенность друг в друга, вложенные директори
отбрасываются и оставляют только корневую.
```shell
['./test', './test/test_1', './test/test_2'] => ['./test']
```

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

### Usage example

```php

class ExampleService
{
    public function __construct(
        private readonly \TestAssignment\Client\CommentsClientInterface $client
    ) {
    }
    
    public function method(): ...
    {
        try {
            /** @var Comment[] */
            $comments = $this->client->get();
        } catch (\TestAssignment\Exception\ApiException $e) {
            ...
        }
    } 
}

$service = new ExampleService(new \TestAssignment\Client\CommentsClient());
```