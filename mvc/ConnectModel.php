<?php
  class ConnectModel {
    protected $_dbConnections = array(); // 현재 connect() 실행으로 생성된 PDO 객체들이 담긴 배열
    protected $_connectName; // dbConnections에 담길 PDO객체의 key, 접속명
    protected $_modelList = array(); // 서브 모델 클래스의 인스턴스를 저장하는 프로퍼티
    const MODEL = 'Model';

    /* 데이터베이스 연결, pdo객체 생성 */
    public function connect($name, $connection_strings) { // name: dbConnections의 key, strings: db커넥션에 필요한 정보
      try {
        $cnt = new PDO($connection_strings['string'], $connection_strings['user'], $connection_strings['password']);
        // PDO(dsn, dbusername, dbpassword)
        // when mysql, PDO("mysql:dbname=jinabase;host=127.0.0.1","jina","jinapassword");
      } catch(PDOException $e) { exit("DB connection failed TT : {$e->getMessage()}"); }

      $cnt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // 예외를 던질 수 있도록 예외 레포트에 관한 설정을 set!
      // http://php.net/manual/kr/pdo.setattribute.php
      $this->_dbConnections[$name] = $cnt;
      $this->_connectName = $name;
    }

    /* 접속명에 해당하는 PDO객체 반환 */
    // public function getConnection($name=null) {
    private function getConnection($name=null) { // 접속명
      if(is_null($name)) return current($this->_dbConnections);
      // 접속명이 null인 경우 dbConnections의 제일 첫 요소(현재 내부 포인터가 가리키고 있는 요소)를 반환
      // http://php.net/manual/kr/function.current.php
      return $this->_dbConnections[$name];
    }

    /* connectionName에 해당하는 PDO 객체를 얻어 반환 */
    // public function getModelconnection() {
    private function getModelconnection() {
      if (isset($this->_connectName)) {
        $name = $this->_connectName;
        $cnt = $this->getConnection($name);
      } else $cnt = $this->getConnection();
      return $cnt;
    }

    /* 모델객체 반환 */
    public function get($model_name) {
      if(!isset($this->_modelList[$model_name])) { // 해당 모델명에 대한 객체가 없으면
        $mdl_class = $model_name.self::MODEL; // 모델에 해당하는 클래스 이름을 만들고
        $cnt = $this->getModelconnection(); // 해당 모델에 해당하는 PDO 객체를 만들고
        $obj = new $mdl_class($cnt); // PDO 객체를 이용해 model 객체를 만들어
        $this->_modelList[$model_name] = $obj; // 모델리스트에 모델명으로 저장
      }
      $modelObj = $this->_modelList[$model_name];
      return $modelObj;
    }

    /* 사용했던 자원들(모델, PDO)을 전부 파기 */
    public function __destruct() {
      foreach($this->_modelList as $model) unset($model);
      foreach($this->_dbConnections as $cnt) unset($cnt);
    }
  }
 ?>
