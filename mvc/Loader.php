01<?php
  class Loader {
    protected $_directories = array();
    // in to will be autoload Directory

    public function regDirectory($dir) {
      // dir is directory array
      // content is file mame array
      $this->_directories[] = $dir;
      // array_push($$this->_directories, $dir);
      // http://php.net/manual/en/function.array-push.php
    }
    public function register() {
      spl_autoload_register(array($this, 'requireClsFile'));
      // for object's method execute
      // array('class name', 'function name')
      // __autoload()
      // as not yet execute stacked queue
    }
    public function requireClsFile($class) {
      // then instantiation, class's name is $class and this function execute
      foreach ($this->_directories as $dir) {
        $file = $dir.'/'.$class.'php';
        // is / ok? why don't use \
        if(is_readable($file)) {
          //http://php.net/manual/kr/function.is-readable.PHP
          require $file;
          return;
        }
      }
    }
  }
 ?>
