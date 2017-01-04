<?php
  abstract class AppBase {
    protected $_resquest;
    protected $_response;
    protected $_session;
    protected $_connectModel;
    protected $_router;
    protected $_signinAction = array(); // 로그인 했을 때 컨트롤러와 액션의 조합을 저장하는 배열 프로퍼티
    protected $_displayErrors; // 에러 표시 온/오프 저장을 위한 프로퍼티
    const CONTROLLER = 'Controller';
    const VIEWDIR = '/Views';
    const MODELDIR = '/models';
    const CONTROLLERDIR = '/Controllers';
    const WEBDIR = '/mvc_htdocs'; // 도큐먼트 루트 폴더 = 프론트 컨트롤러 위치

    public function __construct($dspErr) {
      $this->setDisplayErrors($dspErr);
      $this->initialize();
      $this->doDbConnection();
    }

    protected function initialize() {
      $this->_router = new Router($this->getRouteDefinition());
      $this->_connectModel = new ConnectModel();
      $this->_request = new Request();
      $this->_response = new Response();
      $this->_session = new Session();
    }

    /* 에러표시 모드 온/오프에 대해 실제로 php.ini를 설정 */
    protected function setDisplayErrors($dspErr) {
      if($dspErr) {
        $this->_displayErrors = true;
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL);
      } else {
        $this->_displayErrors = false;
        ini_set('display_errors', 0);
      }
      //http://php.net/manual/kr/function.ini-set.php
      //http://php.net/manual/kr/function.ini-get.php
      //http://php.net/manual/kr/function.error-reporting.php
    }

    public function isDisplayErrors() {
      return $this->_displayErrors;
    }

    public function run() {
      try {
        $parameters = $this->_router->getRouterParams($this->_request->getPath()); // getPath()는
        // 정의되어 있는 라우팅 정보와 리퀘스트 url을 참고하여 경로 정보를 매칭시켜 반환, 정의되어 있지 않는 경우 false 반환
        if($parameters === false) throw new FileNotFoundException('NO ROUTE '.$this->_request->getPath());
        // 경로정보가 없으면 에러 발생!
        $controller = $parameters['controller'];
        $action = $parameters['action'];
        // 매칭시킨 값에서 컨트롤러와 액션을 획득
        $this->getContent($controller, $action, $parameters);
        // 컨트롤러와 액션을 이용해 내용을 얻어옴
      } catch (FileNotFoundException $e) {
        $this->disErrorPage($e);
      } catch (AuthorizedException $e) { // 보안 인증 에러
        list($controller, $action) = $this->_signinAction;
        // http://php.net/manual/kr/function.list.php
        // 다음 배열의 값을 각각 controller와 action으로 나눔
        $this->getContent($controller, $action);
      }
      $this->_response->send();
    }

    // public function getcontent($controllerName, $action, $parameters=array()) {
    protected function getcontent($controllerName, $action, $parameters=array()) {
      $controllerClass = ucfirst($controllerName).self::CONTROLLER;
      // 컨트롤러 객체 생성에 필요한 컨트롤러 클래스 이름을 획득
      // http://php/net/manual/kr/function.ucfirst.php
      $controller = $this->getControllerObject($controllerClass);

      if($controller === false) throw new FileNotFoundException($controllerClass.' NOT FOUND.');

      $content = $controller->dispatch($action, $parameters);
      // 해당 컨트롤러의 액션을 실행해 html(view)를 획득
      $this->_response->setContent($content);
    }

    protected function getControllerObject($controllerClass) {
      if(!class_exists($controllerClass)) { // 현재 읽을 수 있는 범위에 클래스가 없을 때
        $controllerFile = $this->getControllerDirectory().'/'.$controllerClass.'.php'; // 클래스 경로를 만들어주고
        if(!is_readable($controllerFile)) return false; // 읽을 수 없는 경로일 경우 false 반환
        else { // 읽을 수 있는 경우 require하고 재검사
          require_once $controllerFile;
          if(!class_exists($controllerClass)) return false;
        }
      }
      // 클래스가 생성 가능한 경우가 됨
      $controller = new $controllerClass($this); // 현재 어플리케이션 객체를 이용해 컨트롤러를 생성!
      return $controller;
    }

    protected function dispErrorPage($e) {
      $this->_response->setStatusCode(404, 'FILE NOT FOUND.');
      $errMessage = $this->isDisplayErrors() ? $e->getMessage() : 'FILE NOT FOUND.';
      // 에러를 보여주는 옵션이 온이었다면 에러메세지를 보여줌
      $errMessage = htmlspecialchars($errMessage, ENT_QUOTES, 'UTF-8');
      $html = "<!DOCTYPE html>
               <html>
                <head>
                 <meta charset='utf-8' />
                 <title> HTTP 404 Error </title>
                </head>
                <body> {$errMessage} </body>
               </html>";
      $this->_response->setContent($html);
    }

    abstract protected function getRouteDefinition(); // return assoc-array

    // protected protected function doDbConnection() {}
    abstract protected function doDbConnection();

    public function getRequestObject() {
      return $this->_request;
    }

    public function getResponseObject() {
      return $this->_response;
    }

    public function getResponseObject() {
      return $this->_session;
    }

    public function getConnectModelObject() {
      return $this->_connectModel;
    }

    public function getViewDirectory() {
      return $this->getRootDirectory().self::VIEWDIR;
    }

    public function getModelDirectory() {
      return $this->getRootDirectory().self::MODELSDIR;
    }

    public function getDocDirectory() {
      return $this->getRootDirectory().self::WEBDIR;
    }

    abstract public function getRootDirectory();

    public function getControllerDirectory() {
      return $this->getRootDirectory().self::CONSTROLLERSDIR;
    }
  }
 ?>
