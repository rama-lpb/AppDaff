<?php
namespace App\Core;

class Validator extends Singleton
{
    private static array $errors= [];
    private static array $rules= [];
    private static array $ruleInscription= [];

    public function __construct()
    {
        self::$errors = [];
        self::$rules = 
        [
            "require" => function($key, $value,$message= "Le champs obligatoire"):bool
            {
                if(empty($value))
                {
                     self::addError($key, $message);
                     return false;
                }
                return true;
            },
            "minLenght" => function($key,$value, int $taillemin , $message):bool
            {
                if(strlen($value) < $taillemin)
                {
                    self::addError($key,$message);
                    return false;
                }
                return true;
            },
             "maxLenght" => function($key,$value,$message,int $taillemax)
            {
                if(strlen($value) > $taillemax)
                {
                    $message="{$value} doit contenir au maximum {$taillemax} caractères";
                    self::addError($key,$message);
                }
            },
            "isEmail" => function($key, $value, $message)
            {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) 
                {
                    self::addError($key, $message);
                }
            },
            "isPassword" => function ($key, $value, $message = "Mot de passe invalide") 
            {
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $value)) 
                {
                    self::addError($key, $message);
                }
            },
            "isSenegalPhone" => function ($key, $value, $message = "Numéro de téléphone invalide") 
            {
                $value = preg_replace('/\D/', '', $value);
                $prefixes = ['70', '75', '76', '77', '78'];
                if (!(strlen($value) === 9 && in_array(substr($value, 0, 2), $prefixes))) 
                {
                    self::addError($key, $message);
                }
            },
            "isCNI" => function ($key, $value, $message = "Numéro de CNI invalide") 
            {
                $value = preg_replace('/\D/', '', $value);
                if (!preg_match('/^1\d{12}$/', $value)) 
                {
                    self::addError($key, $message);
                }
            }
        ];
    }


    //stockre les erreurs qu'on peut avoir dans le le tableau
    public static function addError(string $key, string $message)
    {
         self::$errors[$key] = $message; 
    }

    // valider les champs
   public function validate(array $data, array $rules): bool // $data= les valeurs saisies par lutilisateur au moment de la connexion
    {
        foreach ($rules as $field => $fieldRules) 
        {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) 
            {
                if(isset(self::$errors[$field])) { break;}
                if (is_string($rule)) 
                {
                    $callback = self::$rules[$rule] ?? null;
                    if ($callback) 
                    {
                        $callback($field, $value);
                    }
                }
                elseif (is_array($rule)) 
                {
                    $ruleName = $rule[0];
                    $params = array_slice($rule, 1);
                    $callback = self::$rules[$ruleName] ?? null;

                    if ($callback) 
                    {
                        $callback($field, $value, ...$params);
                    }
                }
            }
        }

        return empty(self::$errors);
    }

    // recupere et les erreurs
    public static function getError()
    {
        return self::$errors;
    }
}