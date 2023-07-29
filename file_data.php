<?php
// Author: Hassan Tijani (SureCoder)
// Library Name: File datetails

Class FileData{
    
    // Properties
  public $name;
  public $color;
  private $wo;
  private $sqlConnect;
  public $path;

  // Methods
  function set_variable($fileName) {
    $this->name = $wo['config']['site_url']."/upload/files/file_svg/".$fileName;
  }
  function get_name() {
    return $this->name;
  }
    
    
    
}








