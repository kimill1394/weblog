<?php
  class Request {

    public function isPost() {
      if($_SERVER['REQUEST_METHOD'] === 'POST') return true;
      // 요청 방식 검사
      else return false;
    }

    /* get 방식으로 요청된 데이터를 획득하는 메서드 */
    public function getGet($name, $param=null) { // $param이 뭐징?
      if(isset($_GET[$name])) return $_GET[$name];
      else return $param;
    }

    public function getPost($name, $param=null) {
      if(isset($_POST[$name])) return $_POST[$name];
      else return $param;
    }

    /* 호스트 이름 획득 */
    public function getHostName() {
      if(!empty($_SERVER['HTTP_HOST'])) return $_SERVER['HTTP_HOST'];
      // 현재 리퀘스트에 Host:헤더값이 있다면 그 내용
      else return $_SERVER['SERVER_NAME'];
      // 없으면 서버측에 설정된 호스트명을 반환
      // 현재 스크립트가 실행되고 있는 웹서버의 호스트명, 가상호스트인경우 가상호스트명
    }

    // public function getRequestUri() {
    private function getRequestUri() {
      return $_SERVER['REQUEST_URI'];
      // 도메인 이후의 경로
      // 페이지 접근을 위해 지정한 URI
    }

    /* 리퀘스트의 기본 경로 즉 프론트 컨트롤러의 위치를 반환 */
    // public function getBaseUrl() {
    private function getBaseUrl() {
      $scriptName = $_SERVER['SCRIPT_NAME'];
      // 현재 스크립트의 경로. 스크립트 자신의 페이지를 지정하는데 유용
      $requestUri = $this->getRequestUri();
      if(strpos($requestUri, $scriptName)===0) return $scriptName;
      // uri에 페이지 이름이 지정되어 있는 경우
      // else if(strpos($requestUri, dirname($scriptName))===0) return rtrim(dirname($scriptName), '/');
      else if(strpos($requestUri, str_replace('\\', '/', dirname($scriptName)))===0) return rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
      // uri에 페이지 이름이 지정되어 있지 않은 경우 (버추얼 호스팅을 통해 접근한 경우)
      // 윈도우에서 경로를 dirname으로 읽으면 \를 사용하는 데 반해 uri는 /를 사용하므로 문자열 변환 코드를 추가함
      else return '';
    }

    ///////////////////////////////////////////////////////???????????????????????????????????????????????????

    public function getPath() {
      $base_url = $this->getBaseUrl();
      $requestUri = $this->getRequestUri();

      if(($sp=strpos($requestUri, '?')) !== false)
        $requestUri = substr($requestUri, 0, $sp);
        // http://php.net/manual/en/function.substr.php
        //////////////////////////////////////////////////////////////////////// 이거 의미가 뭐지?
        $path = (string)substr($requestUri, strlen($base_url));
      return $path;
    }
  }
 ?>
