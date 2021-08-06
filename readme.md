## Как пользоваться
Установка через composer:

    composer require brezgalov/yii2-api-helpers --prefer-dist

### IndexAction
IndexAction - расширение стандартного \yii\rest\IndexAction, позволяет более гибко настраивать "внутренности" экшена

Подключаем IndexAction и передаем в него ISearch

    /**
    * @return array
    */
    public function actions()
    {
        $acts = parent::actions();
    
        $acts['index'] = [
            'class' => IndexAction::class, 
            'searchClass' => AutoBiddersSearch::class, // ISearch
        ];
        
        return $acts;
    }

Более сложный вариант

    /**
    * @return array
    */
    public function actions()
    { 
        $acts = parent::actions();
    
        $acts['index'] = [
            'class' => IndexAction::class,
            'checkAccess' => [$this, 'checkAccess'],
            'searchClass' => AccreditationsSearch::class, // ISearch
            'dataProviderClass' => AccreditationsDataProvider::class, // ActiveDataProvider
        ];

        return $acts;
    }

### ApiInputAction
ApiInputAction - универсальный экшен для совершения операций. Оборачивает run в миграцию, заполняет и валидирует модель IApiInputForm.
Метод IApiInputForm::getResult позволяет гибче работать с выдачей.

Пример подключения:

    /**
    * @inheritDoc
    */
    public function actions()
    {
        $actions = parent::actions();

        $actions['create'] = [
            'class' => ApiInputAction::class,
            'modelClass' => AccreditationInputForm::class,
        ];

        $actions['update'] = [
            'class' => ApiInputAction::class,
            'modelClass' => AccreditationInputForm::class,
        ];

        return $actions;
    }

Пример гибкой отдачи 

    class MyForm extends Model implements IApiInputForm
    {    
        /**
         * @var User
         */
        public $user;
    
        /**
         * BiddersGroupInputForm constructor.
         * @param array $config
         */
        public function __construct($config = [])
        {
            parent::__construct($config);
    
            if (empty($this->user) && \Yii::$app->has('user')) {
                $this->user = \Yii::$app->user->getIdentity();
            }
        }

        /**
         * @return User|false
         */
        public function doSmth()
        {
            if (!$this-validate()) {
                return false;
            }

            // ... <- some code here

            // Отдаем юзера, потому что выше по коду он может быть нужен
            return $this->user;
        }
    
        /**
         * @param array $args
         * @return UserDto|false
         */
        public function getResult(array $args = [])
        {
            $result = $this->doSmth();
    
            // Отдаем DTO юзера, чтобы не спалить личные данные
            if ($result instanceof User) {
                return UserDto::from($result);
            }

            return $result;
        }
    }