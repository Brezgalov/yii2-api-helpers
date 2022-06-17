## О пакете

Этот пакет - результат работы по стандартизации, ускорению разработки 
команды и увеличению качества кода. 

Он ориентирован на разработку в первую очередь серверных API, однако, 
работа с web формами так же поддерживается. 
_(Апи часто требует "админку" для существования в виде продукта)_

## Ключевые особенности

### Стандартизация подключения логики. 
Никакой логики в контроллере, обеспечиваем подключение "Controller -> Action -> Service"

### Отдельный интерфейс для пользовательского ввода.
Если сервису не нужен пользовательский ввод - мы не будем пытаться его наполнять. 

### Отсутствие необходимости контроля за транзакциями.
Переворачиваем игру: теперь транзакции есть везде и "стажер" не забудет их применить, 
а отказ от использования транзакций представлен в явном виде

### Последовательное выполнение action при одновременном вызове с одного и того же IP.
Избегаем случаев, когда много одновременных запросов путают друг другу "карты"

### Использование отложенных событий.
Списываем деньги с карты пользователя, только если action гарантированно выполнился без ошибок

### Разделение форматирования ответа и логики.
Один сервис, много вариантов форматирования в разных action

### Разделение логики и отображения для web-форм.
Вывод html - отдельный вариант форматирования. Для передачи данных на страницу используем "интерфейс DTO".

## Установка

    composer require brezgalov/yii2-api-helpers --prefer-dist

## Подключение сервиса

Используем метод **Controller::actions()** для подключения **action**:

    public function actions()
    {
        return [
            'index' => [
                'class' => ApiGetAction::class,
                'service' => MyExampleRepositoryService::class,
                'methodName' => MyExampleRepositoryService::METHOD_LIST,
            ],
        ];
    }

Для разработки API я предлагаю использовать два вида **action**:
* **ApiGetAction** - для вывода информации
* **ApiPostAction** - для работы логики

> _ApiGetAction и ApiPostAction отличаются набором поведений внутри. Cм. секцию "Поведения для action"

При подключении **action** используется 2 параметра:
* **service** - Конфигурация класса сервиса
* **methodName** - Строка, обозначающая метод сервиса, который необходимо вызвать

Код сервиса **MyExampleRepositoryService**:

    class MyExampleRepositoryService extends Model
    {
        const METHOD_LIST = 'listData';
  
        protected function getExampleData()
        {
            return [
                [
                    'id' => 1,
                    'name' => 'Barbara',
                    'sex' => 'female',
                ],
                [
                    'id' => 2,
                    'name' => 'Mike',
                    'sex' => 'male',
                ],
            ];
        }
    
        public function listData()
        {
            return $this->getExampleData();
        }
    }

## Пользовательский ввод

### Обрабатываем пользовательский ввод напрямую

Пользовательский ввод представлен в виде полей сервиса, которые заполняются через **IRegisterInputInterface**:

    class MyExampleRepositoryService extends Model implements IRegisterInputInterface
    {
        public $idFilter;
      
        public $nameFilter;

        public function registerInput(array $data = [])
        {
            $this->nameFilter = $data['filter_name'] ?? $this->nameFilter;
            $this->idFilter = $data['filter_ID'] ?? $this->idFilter;
        }

        ... // other code

Благодаря такому интерфейсу:
**!** Не нужно реализовывать/вызывать наполнение пользовательскими данными, когда это не требуется

**!** Интерфейс web API и сервиса могут различаться, это бывает полезно для рефакторинга

**!** Разрывает связь между rules и load для сервисов на основе Model, теперь проще указать правило валидации в rules для переменной которую не хотим заполнять данными запроса

### Используем Model::load для пользовательского ввода

Так же мы можем поддерживать работу с web-формами или сервисами старой версии этой библиотеки, обернув метод load

    class MyExampleRepositoryService extends Model implements IRegisterInputInterface
    {
        public $id;
    
        public $name;
    
        public function rules()
        {
            return [
                [['id'], 'integer'],
                [['name'], 'string'],
            ];
        }
    
        public function registerInput(array $data = [])
        {
            $this->load($data, '');
        }

        ... // other code

## Форматирование ответа

Для форматирования ответа сервиса необходимо использовать объект интерфейса **IFormatter**

    interface IFormatter
    {
        public function format($service, $result);
    }

Метод **format($service, $result)** позволяет иметь доступ к состоянию сервиса через переменную **$service**, 
для формирования более комплексной логики форматирования.

По-умолчанию для форматирования ответа API используется объект класса **ModelResultFormatter**. 
Он отвечает за стандартную обработку ошибок.

Для примера реализуем Formatter скрывающий поле "sex" из набора данных:

    class MyExampleFormatter extends ModelResultFormatter
    {
        public function format($service, $result)
        {
            if (is_array($result)) {
                foreach ($result as &$item) {
                    unset($item['sex']);
                }

                return $result;
            }
    
            return parent::format($service, $result);
        }
    }

Подключим Formatter в контроллере:

    public function actions()
    {
        return [
            'index' => [
                'class' => ApiGetAction::class,
                'service' => MyExampleRepositoryService::class,
                'methodName' => MyExampleRepositoryService::METHOD_LIST,
                'formatter' => MyExampleFormatter::class
            ],
        ];
    }

## Валидация и обработка ошибок

Обработка ошибок API происходит, когда метод сервиса возвращает false.

Вывести ошибку можно с применением объекта ErrorException или использовать метод **Model::addError($attribute, $error)**

    public function format($service, $result)
    {
        if ($result instanceof ErrorException && $this->response) {
        $response = clone $this->response;

            $response->data = $result->error;
            $response->setStatusCode($result->statusCode);

            return $response;
        }

        if ($result instanceof \Exception) {
            throw $result;
        }

        if ($result === false) {
            $errorModel = $service;

            if ($service && $errorModel instanceof Model) {
                if (!$service->hasErrors()) {
                    $service->addError(static::class, $this->unknownExecutionErrorText);
                }
            } else {
                $errorModel = new Model();
                $service->addError(static::class, $this->unknownExecutionErrorText);
            }

            return $errorModel;
        }

        return $result;
    }

**ErrorException** позволяет самостоятельно выбрать формат ошибки и код ответа. 

Использование ошибок класса **Model** приведет к стандартному отображению ошибок Yii2 с кодом ответа 422

## Поведения для action

Библиотека позволяет подключить поведения к **action**.

Стандартный набор поведений **action** задается через метод **BaseAction::getDefaultBehaviors()**

    class ApiPostAction extends BaseAction
    {
        const BEHAVIOR_KEY_TRANSACTION = 'transaction';
        const BEHAVIOR_KEY_MUTEX = 'mutex';
        const BEHAVIOR_KEY_DELAYED_EVENTS = 'delayedEvents';

        public $formatter = ModelResultFormatter::class;
    
        protected function getDefaultBehaviors()
        {
            return [
                static::BEHAVIOR_KEY_TRANSACTION => TransactionBehavior::class,
                static::BEHAVIOR_KEY_MUTEX  => MutexBehavior::class,
                static::BEHAVIOR_KEY_DELAYED_EVENTS  => DelayedEventsBehavior::class,
            ];
        }
    }

Поле **BaseAction::$behavior** позволяет управлять поведениями при подключении к контроллеру.
Если по ключу поведения передать значение **false** - поведение не будет подключено к **action**

    public function actions()
    {
        return [
            'my-post-action' => [
                'class' => ApiPostAction::class,
                ...
                'behaviors' => [
                    ApiPostAction::BEHAVIOR_KEY_TRANSACTION => false,
                    MyCustomBehavior::class,
                ],
            ],
        ];
    }

## TransactionBehavior

_Я обязательно допишу этот док попозже_

## MutexBehavior

_Я обязательно допишу этот док попозже_

## DelayedEventsBehavior

_Я обязательно допишу этот док попозже_

## Работа с web-формами

_Я обязательно допишу этот док попозже_
