<?php
  class Router {
    protected $_convertedRoutes;

    /* 미리 정의된 라우팅 정보를 매개변수로 받아와 저장 */
    public function __construct($routedef) {
      $this->_convertedRoutes = $this->routeConverter($routedef);
    }

    /* 사용하기 용이한 상태로 문자열 변환, 정규표현식과 관련해 다시 살필 것 */
    // public function routeConverter($routerdef)
    private function routeConverter($routerdef) {
      $converted = array();
      foreach($routedef as $url => $par) {
        $converts = explode('/', ltrim($url, '/'));
        foreach($converts as $i => $convert) {
          if(strpos($convert, ':')===0) {
            $bar = substr($convert, 1);
            $convert = '(?<'.$bar.'>[^/]+)';
          }
          $converts[$i] = $convert;
        }
        $pattern = "/".implode('/', $converts);
        $converted[$pattern] = $par;
      }
      return $converted;
    }

    /* 해당하는 경로에 맞는 라우팅(컨트롤러와 액션 결정) */
    public function getRouteParams($path) {
      if('/' !== substr($path, 0, 1)) $path = '/'.$path;
      foreach ($this->_convertedRoutes as $pattern => $par)
        if(preg_match('#^'.$pattern.'$#', $path, $p_match)) {
          // 해당하는 경로를 키로 갖는 값(컨트롤러=>'', 액션=>'')을 찾음, p_match = array()
          // http://php.net/manual/kr/function.preg-match.php
          $par = array_merge($par, $p_match);
          // array(컨트롤러=>'', 액션=>'')에 [0]=>$p_match 추가 (경로정보)
          return $par;
        }
      return false;
    }
  }
 ?>
