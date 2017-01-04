<?php
class View {
  protected $_baseURL; // view 파일의 폴더 경로 정보
  protected $_initialValue; // Controller.render()에서의 정보 request, request의 baseURL, session
  protected $_passValues = array(); // view에 전달할 정보

  public function __construct($baseURL, $initialValue = array()) {
    $this->_baseURL = $baseURL; // application->getBaseURL
    $this->_initialValue = $initialValue;
  }

  /* 레이아웃 페이지에 제목으로 보낼 데이터 설정 */
  public function setPageTitle($name, $value) {
    $this->_passValues[$name] = $value;
  }

  /* View.render() 실행 후 결과값 가공 */
  public function render($filename, $parameters=array(), $template=false) {
    $view = $this->_baseURL.'/'.$filename.'.php';
    extract(array_merge($this->_initialValue, $parameters));
    // 배열 병합해서 연관배열로 반환

    ob_start();
    ob_implicit_flush(0);
    require_once $view;
    $content = ob_get_clean();
    if($template) $content = $this->render($template, array_merge($this->_passValues, array('_content'=>$content)));
    // 템플릿이 있으면 재귀함수로 템플릿까지 포함해서 한번 더!
    return $content;
  }

  public function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }
}
 ?>
