<?php
//
//TEST - SCENARIO
//
\E_fn\import_librarie('TestRestApi/testRestApi');
\E_fn\import_librarie('apiRestClient');
class UsersTest extends TestScenario{    
    public $baseUrl= 'http://localhost/app/';
    
    public function requests_1(): array{
        return array(
            'Login' => array(
                'request' => array(
                    'uri' => $this->baseUrl . 'login',
                    'headers' => array(),
                    'method' => 'POST',
                    'params' => null,
                    'data' => array('username' => 'user', 'password' => '123456')
                ),
                'expectedResponse' => array(
                    'status' => '200',
                    'data' => function($data){
                        $this->headers['Authorization']= $data['TOKEN'];
                        return true;
                    },
                    'dataDef' => null
                )
            ),
            'Create User' => array(
                'request' => array(
                    'uri' => $this->baseUrl . 'users',
                    'headers' => array(),
                    'method' => 'POST',
                    'params' => null,
                    'data' => array('username' => 'user2', 'password' => '123455')
                ),
                'expectedResponse' => array(
                    'status' => '200',
                    'data' => function($data){
                        $this->vars['isUser']= $data['id'];
                        return true;
                    },
                    'dataDef' => null
                )
            )
        );
    }
    public function requests_2(): array{
        return array(
            'ModUsuario' => array(
                'request' => array(
                    'uri' => $this->baseUrl . 'users/' . $this->vars['idUser'],
                    'headers' => array(),
                    'method' => 'PUT',
                    'params' => null,
                    'data' => array('enabled' => 1)
                ),
                'expectedResponse' => array(
                    'status' => '200',
                    'data' => function($data){
                        return $data['user']['id'] == $this->vars['idUser'];
                    },
                    'dataDef' => array('enabled' => 1)
                )
            )
        );
    }
    
    public function execute_1(){
        $rtas= [];
        foreach ($this->requests_1() as $key => $request) {
            $rta= $this->executeRequest($key, $request['request'], $request['expectedResponse']);
            $rtas[]= $rta;
            if(!$rta->ok){
                break;
            }
        }
        return $rtas;
    }
    
    public function execute_2(){
        $rtas= [];
        foreach ($this->requests_2() as $key => $request) {
            $rta= $this->executeRequest($key, $request['request'], $request['expectedResponse']);
            $rtas[]= $rta;
            if(!$rta->ok){
                break;
            }
        }
        return $rtas;
    }
}

//
//CRON 
//Podria ser tambien con un HTTP CONTROLLER
//
use Enola\Cron;
use Enola\Cron\En_CronRequest, Enola\Support\Response;

\E_fn\import_aplication_file('source/tests/UsersTest');
class Tests extends Cron\En_CronController{

    public function __construct() {
        parent::__construct();
    }
    
            
    public function test(En_CronRequest $request, Response $response){        
        $testUsuarios= new UsuariosAbm();
        $rtas= [];
        $rtas= $testUsuarios->execute_1();
        $lastRequest= end($rtas);
        if($lastRequest->getOk()){
            $rtas= array_merge($rtas, $testUsuarios->execute_2());
        }
        //IMPRIMO RTA
        foreach ($rtas as $rta) {
            if($rta->getOk()){
                var_dump($rta->getName() . ' - ' . 'Ok');
            }else{
                var_dump($rta->getName() . ' - ' . 'Error');
                var_dump($rta->getResponse());
            }
        }
    }
}