<?php

namespace App\Controller;

use Obullo\Mvc\Controller;
use Obullo\View\Model\ViewModel;
use App\View\Template\IndexTemplate;

class WelcomeController extends Controller
{
    /**
     * Index
     * 
     * @return void
     */
    public function indexAction()
    {
        // $this->db = $this->database->shared()->createQueryBuilder();

        // $row = $this->db
        //     ->select('username', 'email')
        //     ->from('users')
        //     // ->setFirstResult(10)
        //     ->setMaxResults(20)
        //     ->execute()
        //     ->getResult();

        // var_dump($row);

        // $row = $this->db->query("SELECT * FROM users")->row();
        // var_dump($row);


        // $this->mvc->getFlush()->path("header", ['userId' => 5]);

        $model = new ViewModel(['foo' => 'bar']);
        $model->setTemplate(new IndexTemplate('welcome.phtml'));
        
        $this->view->render($model);

        // $this->view->render('welcome.phtml');
    }
}
