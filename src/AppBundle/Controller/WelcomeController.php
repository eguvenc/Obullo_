<?php

namespace AppBundle\Controller;

// use Obullo\View\Model\ViewModel;
// use AppBundle\View\Template\IndexTemplate;

use Obullo\Mvc\Controller;
use Zend\Diactoros\Response\HtmlResponse;

class WelcomeController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction($request)
    {
        // $this->cookie->withName('name')->withExpire(0)->withValue('1.23')->set(); 
        // $this->cookie->delete('name'); 

        // $args = $request->getArgs();
        // print_r($args);

        // throw new \Exception("asda");

        // var_dump($this->container->get('database:default'));
        // var_dump($this->container->get('redis:default'));
        // var_dump($this->container->get('amqp:default'));
        // var_dump($this->container->get('mongo:default'));
        // var_dump($this->container->get('memcached:default'));

        // $this->flash->success("succesfull !!");
        // echo $this->flash->getMessageString();

        // $this->db = $this->container->get('database:default')->createQueryBuilder();

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
        
        // $html = $this->render($model);

        return new HtmlResponse($this->render('welcome.phtml'));
    }
}
