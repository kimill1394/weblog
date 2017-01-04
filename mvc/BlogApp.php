<?php
  class BlogApp extends AppBase {
    protected $_signinAction = array('account', 'signin');

    protected function doDbConnection() {
      $this->_connectModel->connect('master', array(
                                                'string'=>'mysql:dname=weblog; host=localhost; charset=utf8',
                                                'user'=>'jina', 'password'=>'jina'));
    }

    public function getRootDirectory() {
      return dirname(__FILE__);
      // http://php.net/manual/en/language.constants.predefined.php
      // 현재 파일의 디렉토리 경로
    }

    /* 라우팅 정보를 미리 정의함, 모든 라우팅 정보가 들어있음 */
    protected function getRouteDefinition() {
      return array(

          // AccountController 관련 Routing
          '/account'          => array('controller'=>'account', 'action'=>'index'),
          '/account/:action'  => array('controller'=>'account'),
          '/follow'           => array('controller'=>'account', 'action'=>'follow'),

          // BlogController 관련 Routing
          '/'                           => array('controller'=>'blog', 'action'=>'index'),
          '/status/post'                => array('controller'=>'blog', 'action'=>'post'),
          '/user/:user_name'            => array('controller'=>'blog', 'action'=>'user'),
          '/user/:user_name/status/:id' => array('controller'=>'blog', 'action'=>'specific')

      );
    }
  }
 ?>
