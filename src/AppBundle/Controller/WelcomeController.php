<?php

namespace AppBundle\Controller;

use Obullo\Mvc\Controller;
use Obullo\View\Model\ViewModel;
use AppBundle\View\Template\IndexTemplate;

class WelcomeController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {
        // var_dump($this->container->get('redis:default'));
        // var_dump($this->container->get('database:default'));
        // var_dump($this->container->get('amqp:default'));
        // var_dump($this->container->get('mongo:default'));
        // var_dump($this->container->get('memcached:default'));

        // $this->flash->success("succesfull !!");
        // echo $this->flash->getOutputString();

        // $this->db = $this->container->get('database:default')->createQueryBuilder();
        // $this->db = $this->database->shared();

        // $stmt = $this->db->query("SELECT * FROM users");
        // $row  = $stmt->fetch(\PDO::FETCH_OBJ);

        // $row = $this->db
        //     ->select('username', 'email')
        //     ->from('users')
        //     // ->setFirstResult(10)
        //     ->setMaxResults(20)
        //     ->execute()
        //     ->fetchAll();
        // var_dump($row);

        // $row = $this->db->query("SELECT * FROM users")->row();
        // var_dump($row);

        // $this->mvc->getFlush()->path("header", ['userId' => 5]);

        // $model = new ViewModel(['foo' => 'bar']);
        // $model->setTemplate(new IndexTemplate('welcome.phtml'));
        
        // return $this->view->render($model);

        return $this->view->render('welcome.phtml');
    }
}
