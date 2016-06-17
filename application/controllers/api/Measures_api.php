<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require_once APPPATH . '/libraries/REST_Controller.php';

class Measures_api extends REST_Controller {

  /**
   * Defines which methods are protected by
   * an API key
   * @var Array
   */
   protected $methods = [
     'index_put' => ['key' => true, 'limit' => 60],
     'index_post' => ['key' => true, 'limit' => 60],
     'index_delete' => ['key' => true, 'limit' => 60],
     'index_options' => ['key' => false]
  ];

  /**
   * Default constructor
   */
 public function __construct(){
   parent::__construct();
   $this->load->model("key");
   $this->load->model("watch");
   $this->load->model("measure");
 }

  public function index_options(){
     $this->response(null, REST_Controller::HTTP_OK);
  }

 /**
  * Creates a new measure for a given watch
  * @param int $watchId
  * @param long $referenceTime in ms
  * @param long $userTime  in ms
  * @return HTTP_BAD_REQUEST | HTTP_OK
  */
 public function index_post(){

   $watchId = $this->post('watchId');
   $referenceTime = $this->post('referenceTime');
   $userTime = $this->post('userTime');

   log_message('INFO', $watchId);
   log_message('INFO', $referenceTime);
   log_message('INFO', $userTime);
   log_message('INFO', $this->watch->isOwnedBy($watchId, $this->rest->user_id));

   if($watchId != null && is_numeric($watchId) &&
   is_numeric($referenceTime) && is_numeric($userTime)
   && $this->watch->isOwnedBy($watchId, $this->rest->user_id)){

       $this->response(
          ["measureId" =>  $this->measure->addBaseMesure(
                                    $watchId,
                                    (int)$referenceTime,
                                    (int)$userTime)
          ],
          REST_Controller::HTTP_OK);

   }else{
     $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
   }
 }

 /**
  * Creates a new accuracy measure for a given measure
  * @param int $measureId
  * @param long $referenceTime in ms
  * @param long $userTime  in ms
  * @return HTTP_BAD_REQUEST | HTTP_OK
  */
 public function index_put(){

   $measureId = $this->put('measureId');
   $referenceTime = $this->put('referenceTime');
   $userTime = $this->put('userTime');

   log_message("info", "=================");
   log_message("info", $measureId);
   log_message("info", $referenceTime);
   log_message("info", $userTime);
   log_message("info", $this->measure->isOwnedBy($measureId, $this->rest->user_id));
   log_message("info", "=================");

   if($measureId != null && is_numeric($measureId) &&
   is_numeric($referenceTime) && is_numeric($userTime)
   && $this->measure->isOwnedBy($measureId, $this->rest->user_id)){

     log_message("info", "=========&&&========");

     $measure = $this->measure->addAccuracyMesure(
         $measureId,
         (int)$referenceTime,
         (int)$userTime);

     $this->response(["result"=>(array)$measure], REST_Controller::HTTP_OK);

   }else{
     log_message("info", "========ééé=======");

     $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
   }
 }

 public function index_option($id){
  return true;
 }

 /**
  * Soft delete a measure
  *
  * @param int $measureId
  * @return HTTP_BAD_REQUEST | HTTP_OK
  */
 public function index_delete(){
   $measureId = $this->delete('measureId');

   log_message('error',  $this->post('measureId'));
   log_message('error',  $this->delete('measureId'));
   log_message('error',  $id);

   if($measureId != null && is_numeric($measureId)
   && $this->measure->isOwnedBy($measureId, $this->rest->user_id)){

     $this->response(
        array(
          "success"=>$this->measure->delete($measureId)
        ),
        REST_Controller::HTTP_OK
     );
   }else{
     $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
   }
 }
}
