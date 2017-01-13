<?php
 class Session {
   protected static $_session_flag = false; // 세션 시작 여부 저장하는 정적 프로퍼티
   protected static $_generated_flag = false; // 세션 ID가 생성되었는지 저장하는 정적 프로퍼티

   public function __construct() {
     if(!self::$_session_flag) {
       session_start();
       self::$_session_flag = true;
     }
   }

   public function set($key, $value) {
     $_SESSSION[$key] = $value;
   }

   public function get($key, $par=null) {
     if(isset($_SESSION[$key])) return $_SESSION[$key];
     else return $par;
   }

   public function generateSession($del=true) {
     if(!self::$_generated_flag) {
       session_regenerate_id($del);
       // http://php.net/manual/kr/function.session-regenerate-id.php
       // 현재 세션 id를 새 것으로 하고 정보는 유지한다
       // parameter = true -> 이전 세션 id 삭제
       self::$_generated_flag = true;
     }
   }

   // 로그인 상태 등록
   public function setAuthenticateStatus($flag) {
     $this->set('_authenticated', (bool)$flag);
     $this->generateSession();
   }

   // 인증완료=로그인 상태인지 체크
   public function isAuthenticated() {
     return $this->get('_authenticated', false); // 로그인 중이면 SESSION[_authenticated] == true
   }


 }
 ?>
