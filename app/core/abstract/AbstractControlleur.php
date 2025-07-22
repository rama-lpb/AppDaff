<?php

namespace App\Core\Abstract ;

use App\Core\App;
use App\Core\Session;
use App\Core\ImageService;

abstract class AbstractControlleur  extends Session
{
    protected string $layout = 'base';
    private static AbstractControlleur|null $instance = null;
    protected Session $session ;
  

    public function __construct()
    {
      $this->session = App::getDependencies('Session');
    }
    public static function getInstance()
    {
      if (self::$instance === null)
      {
        self::$instance = new static();
      }
      return self::$instance ;
    }
 public function uploadPhotos(array $files): string|false 
    {
        try 
        {
            $uploads = ImageService::uploadMultipleImages([
                'photoRecto' => $files['photoRecto'] ?? null,
                'photoVerso' => $files['photoVerso'] ?? null
            ], __DIR__ . '/../../public/images/uploads/');

            return json_encode([
                'recto' => $uploads['photoRecto']['url'],
                'verso' => $uploads['photoVerso']['url']
            ]);
        } 
        catch (\Exception $e) 
        {
            return false;
        }
    }

    

    abstract public function show();
    abstract public function edit ();
    abstract public function store ();
    abstract public function create ();
    abstract public function index ();

    public function  renderHtml ( string $views,array $data=[] )
    {
        extract($data);
        ob_start();
        require_once '../templates/'. $views ;

        $contentForLayout = ob_get_clean();

        require_once '../templates/layout/'. $this->layout . '.layout.php' ;

    }
}