<?php 

abstract class TestScenario{
    public $baseUrl= 'http://localhost/';
    /** Es un conjunto de headers que se van a pasar a cada peticion y se pueden ir modificando. Ahora sirve para el login
     * @var mixes
     */
    public $headers= array();//array('Authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE1MTQzMTEyMTIsImF1ZCI6ImE4NDcxZGQxYTM5NjdiOGUxZTU2NjI0OTMyZDQ2MTBkZTIwZGI1YjEiLCJkYXRhIjp7InVzZXJfaWQiOjEsInVzZXJfbG9nZ2VkIjpbImFkbWluIl19fQ.6YXM1ImlhTukkAKBarESkUWgQtL9paAJ6tjfZOsrTPs');
    /** Aca vamos a ir definiendo variables del escenario. Por ejemplo si creamos un usuarios, luego guardamos el id y con eso luego podemos hacer la siguiente peticion para
     * modificar, eliminar o lo que corresponda. 
     * @var mixed
     */
    public $vars= array();
    
    
    protected function executeRequest($name, $request, $expResponse){
        try{
            $request['headers']= array_merge($request['headers'], $this->headers);
            $response= \Enola\Lib\RestClient::exec($request);
            $ok= $this->responseIsOk($response, $expResponse);
            
            $testResponse= new TestResponse($name, $ok);
            $testResponse->response= $response;
            $testResponse->request= $request;
            if(!$testResponse->getOk()){                
                $testResponse->expResponse= $expResponse;
                unset($testResponse->expResponse['data']);
            }
            
            return $testResponse;
        } catch (Exception $ex) {
            return new TestResponse($name . ' - Exception Error', $ok);
        }
        
    }
    
    protected function responseIsOk($response, $expResponse){
        if($response['status'] == $expResponse['status']){
            if(isset($expResponse['data'])){
                return call_user_func_array($expResponse['data'], array($response['response']));
            }
            return true;
        }
        return false;
    }
}

class TestResponse{
    public $name;
    public $ok;
    public $request;
    public $response;
    public $expResponse;
    
    public function __construct($name, $ok) {
        $this->name= $name;
        $this->ok= $ok;
    }
    
    function getName() {
        return $this->name;
    }

    function getOk() {
        return $this->ok;
    }

    function getResponse() {
        return $this->response;
    }

    function getExpResponse() {
        return $this->expResponse;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setOk($ok) {
        $this->ok = $ok;
    }

    function setResponse($response) {
        $this->response = $response;
    }

    function setExpResponse($expResponse) {
        $this->expResponse = $expResponse;
    }
    
    function getRequest() {
        return $this->request;
    }

    function setRequest($request) {
        $this->request = $request;
    }
}