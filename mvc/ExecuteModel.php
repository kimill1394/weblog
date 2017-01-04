<?php
  abstract class ExecuteModel {
    protected $_pdo; // pdo 객체를 저장하는 프로퍼티

    public function __construct($pdo) {
      $this->setPdo($pdo);
    }

    // public function setPdo($pdo) {
    protected function setPdo($pdo) {
      $this->_pdo = $pdo;
    }

    public function execute($sql, $parameter=array()) {
      $stmt = $this->_pdo->prepare($sql, array(PDO::ATTR_CURSOR=>PDO::CURSOR_SCROLL));
      $stmt->execute($parameter);
      return $stmt;
    }

    public function getAllRecord($sql, $parameter=array()) {
      $all_rec = $this->execute($sql, $parameter)->fetchAll(PDO::FETCH_ASSOC);
      return $all_rec;
    }

    public function getRecord($sql, $parameter=array()) {
      $rec = $this->execute($sql, $parameter)->fetch(PDO::FETCH_ASSOC);
      return $rec;
    }
  }
 ?>
