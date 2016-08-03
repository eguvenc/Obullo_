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
    public function indexAction($request)
    {
        $args = $this->request->getArgs();
        print_r($args);

        // var_dump($this->container->get('Redis:Default'));
        // var_dump($this->container->get('Database:Default'));
        // var_dump($this->container->get('Amqp:Default'));
        // var_dump($this->container->get('Mongo:Default'));
        // var_dump($this->container->get('Memcached:Default'));

        // $this->flash->success("succesfull !!");
        // echo $this->flash->getMessageString();

        // $this->db = $this->container->get('Database:Default')->createQueryBuilder();

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
