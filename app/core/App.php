<?php

namespace App\Core;

use App\Service\CompteService;
use App\Service\SecurityService;
use App\Repository\UserRepository;
use App\Service\TransactionService;
use App\Core\Abstract\AbstractEntity;
use App\Repository\CompteRepository ;
use App\Core\Abstract\AbstractRepository;
use App\Repository\TransactionRepository;
use App\Core\Abstract\AbstractControlleur;
use App\Repository\NumeroTelephoneRepository;

class App
{
    private static array $dependencies = [];

    public static function getInit () 
    { 
        
    self::$dependencies = 
    [
            
            'core' => 
            [
                    'Database' => Database::class,
                    'Router'  => Router::class,
                    'Session'  => Session::class,
                    'Validator'  => Validator::class,
                    'singleton'  => Singleton::class,
                    'ImageService'  => ImageService::class
            ],

            'abstract' => 
            [
                    'AbstractContolleur'=> AbstractControlleur::class,
                    'AbstractRepository'=> AbstractRepository::class ,
                    'AbstractEntity'=> AbstractEntity::class 
            ],

            'repository' => 
            [
                'CompteRepository' => CompteRepository::class,
                'UserRepository' => UserRepository::class,
                'TransactionRepository' => TransactionRepository::class,
                'NumeroTelephoneRepository' => NumeroTelephoneRepository::class,
                

            ],

            'services'  => 
            [
                'CompteService' => CompteService::class,
                'SecurityService' => SecurityService::class,
                'TransactionService' => TransactionService::class

            ]

        ];


    }

    public static function getDependencies (string $key){
      
        if(empty(self::$dependencies )){
            self::getInit();
        
        }

        foreach(self::$dependencies as $dossier){

               if (is_array($dossier) && isset($dossier[$key]) ){
                   $classe= $dossier[$key];

                   if(method_exists($classe , 'getInstance')){

                    return $classe::getInstance() ;
                                                                                                                                                                                                                                                                            
                   }else {
                    return new $classe();
                   }
    
               }
        }
    

    }
}








































/* <?php

namespace App\Core;

use App\Core\Abstract\AbstractControlleur;
use App\Core\Abstract\AbstractRepository;
use App\Core\Abstract\AbstractEntity;
use App\Core\Database;
use App\Core\Router;
use App\Repository\CompteRepository;
use App\Service\CompteService;

class App
{
    private static array $container = [];
    private static bool $initialized = false;

    private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        $dependencies = [
            'core' => [
                'database' => Database::class,
                'router' => Router::class,
            ],
            'abstract' => [
                'abstractController' => AbstractControlleur::class,
                'abstractEntity' => AbstractEntity::class,
            ],
            'services' => [
                'compteServ' => CompteService::class,
            ],
            'repositories' => [
                'compteRepo' => CompteRepository::class,
            ]
        ];

        foreach ($dependencies as $category => $services) {
            foreach ($services as $key => $class) {
                $flatKey = $category . '.' . $key;
                self::$container[$flatKey] = fn() => $class::getInstance();
            }
        }

        self::$initialized = true;
    }

    public static function getDependency(string $key)
    {
        self::initialize();

        if (!isset(self::$container[$key])) {
            throw new \Exception("Dependency '{$key}' not found");
        }

        $dependency = self::$container[$key];
        
        if (is_callable($dependency)) {
            self::$container[$key] = $dependency();
        }

        return self::$container[$key];
    }
}
<?php

namespace App\Core;

use App\Core\Abstract\AbstractController;
use App\Core\Abstract\AbstractRepository;
use App\Core\Abstract\AbstractEntity;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Router;

use App\Repository\UsersRepository;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;
use App\Repository\TelephoneRepository;

use App\Service\SecurityService;
use App\Service\CompteService;
use App\Service\TransactionService;

class App
{
    private static array $container = [];
    private static bool $initialized = false;

    private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        $dependencies = [
            'core' => [
                'database' => Database::class,
                'session' => Session::class,
                'validator' => Validator::class,
                'router' => Router::class,
                'imageServ' => ImageService::class,
            ],
            'abstract' => [
                'abstractRepo' => AbstractRepository::class,
                'abstractController' => AbstractController::class,
                'abstractEntity' => AbstractEntity::class,
            ],
            'services' => [
                'securityServ' => SecurityService::class,
                'compteServ' => CompteService::class,
                'transactionServ' => TransactionService::class,
            ],
            'repositories' => [
                'usersRepo' => UsersRepository::class,
                'compteRepo' => CompteRepository::class,
                'transactionRepo' => TransactionRepository::class,
                'telephoneRepo' => TelephoneRepository::class,
            ]
        ];

        foreach ($dependencies as $category => $services) {
            foreach ($services as $key => $class) {
                $flatKey = $category . '.' . $key;
                self::$container[$flatKey] = fn() => $class::getInstance();
            }
        }

        self::$initialized = true;
    }

    public static function getDependency(string $key)
    {
        self::initialize();

        if (!isset(self::$container[$key])) {
            throw new \Exception("Dependency '{$key}' not found");
        }

        $dependency = self::$container[$key];
        
        if (is_callable($dependency)) {
            self::$container[$key] = $dependency();
        }

        return self::$container[$key];
    }
} */