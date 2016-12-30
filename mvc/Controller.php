<?php // 17
  abstract class Controller {
    protected $_application; // App의 본체 클래스의 인스턴스, 여기선 BlogApp의 인스턴스!
    protected $_controller; // 컨트롤러 클래스의 이름
    protected $_action; // 액션명
    protected $_request; // Request 클래스의 인스턴스
    protected $_response; // response 클래스의 인스턴스
    protected $_session; // session 클래스의 인스턴스, 세션 정보를 저장하기 위함???
    protected $_connect_model; // ConnectionModel 클래스의 인스턴스, == connect()
    protected $_authentication = array(); // 보안인증이 필요한 action인지, 자식이 정의
    const PROTOCOL = 'http://';
    const ACTION = 'Action';

    public function __construct($application) {
      $this->_controller = strtolower(substr(get_class($this), 0, -10)); // controller이름을 소문자로 변환해 저장
      $this->_application = $application;
      $this->_request = $application->getRequestObject();
      $this->_response = $application->getResponseObject();
      $this->_session = $application->getSessionObject();
      $this->_connect_model = $application->getConnectModelObject();
    }

    public function dispatch($action, $params = array()) { // action: 액션이름, params: Routing 정보
      $this->_action = $action;
      $action_method = $action.self::ACTION; // '__Action';

      if(!method_exists($this, $action_method)) { $this->httpNotFound(); } // 여기에서 this는 해당 메서드를 호출하고 있는 자식객체
      if($this->isAuthentication($action) && !$this->_session->isAuthenticated()) // 인증이 필요한지, 인증이 완료되었는지
        throw new AuthorizedException(); // 사용자 정의 예외
      $content = $this->$action_method($params); // 해당 컨트롤러의 해당 메서드를 실행!
      return $content;
    }

    protected function httpNotFound() {
      throw new FileNotFindException('FILE NOT FOUND'.$this->_controller.'/'.$this->_action);
    }

    protected function isAuthentication($action) { // == 인증이 필요한가?
      if($this->_authentication === true || (is_array($this->_authentication)) && (in_array($action, $this->_authentication))
        return true;
        // 전역적으로 인증이 필요한 컨트롤러) || 일부 인증이 필요하고 && 해당 액션이 그 안에 포함된 경우 true
      else return false;
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    protected function render($param =array(), $viewFile =null, $template =null) {
      $info = array('request' => $this->_request, 'base_url' => $this->_request->getBaseURL(), 'session' => $this->_session);
      $view = new View($this->_application->getViewDirectory(), $info);
      if(is_null($viewFile)) $viewFile = $this->_action;
      if(is_null($template)) $template = 'template';
      $path = $this->_controller.'/'.$viewFile;
      $contents = $view->render($path, $param, $template);
      return $contents;
    }

    protected function redirect($url) {
      $host     = $this->_request->getHostName();
      $base_url = $this->_request->getBaseUrl();
      $url      = self::PROTOCOL.$host.$base_url.$url;
      $this->_response->setStatusCode(302, 'FOUND');
      $this->_response->setHeader('Location', $url);
    }
    protected function getToken($form) {
      $key    = 'token/'.$form;
      $tokens = $this->_session->get($key, array());
      if (count($tokens)>=10) array_shift($tokens);
      $password = session_id().$form;
      $token  = password_hash($password, PASSWORD_DEFAULT);
      $tokens[] = $token;
      $this->_session->set($key, $tokens);
      return $token;
    }

    protected function checkToken($form, $token) {
      $key = 'token/'.$form;
      $tokens = $this->_session->get($key, array());
      if(false !== ($present = array_search($token, $tokens, true))) {
        unset($tokens[$present]);
        $this->_session->set($key, $token);
        return true;
      }
      return false;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  }
 ?>
