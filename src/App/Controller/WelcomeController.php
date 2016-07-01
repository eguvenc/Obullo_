<?php

namespace App\Controller;

use Obullo\Mvc\Controller;
// use Obullo\View\Model\ViewModel;
// use App\View\Template\IndexTemplate;

class WelcomeController extends Controller
{
    /**
     * Index
     * 
     * @return void
     */
    public function indexAction()
    {
        // var_dump($this->config->database->connections->default->dsn);

        // $model = new ViewModel(['foo' => 'bar']);
        // $model->setTemplate(new IndexTemplate('welcome.phtml'));
        
        // $this->view->render($model);

        $this->view->render('welcome.phtml');
    }
}
