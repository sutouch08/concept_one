<?php
class Auto_close_discount_policy extends CI_Controller
{
  public $home;

  public function __construct()
  {
    parent::__construct();    
  }

  public function index()
  {
    return $this->db->set('active', 0)->where('end_date <', date('Y-m-d'))->update('discount_policy');
  }

} //--- end class
 ?>
