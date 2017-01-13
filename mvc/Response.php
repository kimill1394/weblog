<?php
  class Response {
    protected $_content; // 컨텐츠
    protected $_statusCode = 200; // 상태코드
    protected $_statusMsg = 'OK'; // 상태메시지
    protected $_headers = array(); // 응답헤더의 필드를 저장
    const HTTP = 'HTTP/1.1'; // http 버전정보

    public function setStatusCode($code, $msg='') {
      $this->_statusCode = $code;
      $this->_statusMsg = $msg;
    }

    public function setHeader($name, $value) {
      $this->_headers[$name] = $value;
    }

    public function setContent($content) {
      $this->_content = $content;
    }

    public function send() {
      header(self::HTTP.$this->_statusCode.' '.$this->_statusMsg);
      // http://php.net/manual/en/function.header.php
      // http://www.faqs.org/rfcs/rfc2616.html
      // 상태코드와 메시지 전송 HTTP/1.1 200 OK
      foreach($this->_headers as $name=>$value)
        header($name.': '.$value);
      print $this->_content;
    }
  }
 ?>
