<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Measures extends MY_Controller 
{
    public function __construct()
	{
        $this->_needLoggedIn = true;
		parent::__construct();
        $this->load->model('watch');
        $this->load->model('measure');
	}

    public function index()
    {       

        if($this->input->post('addWatch'))
        {
            $this->event->add($this->event->ADD_WATCH);

            $brand = $this->input->post('brand');
            $name = $this->input->post('name');
            $yearOfBuy = $this->input->post('yearOfBuy');
            $serial = $this->input->post('serial');
            $caliber = $this->input->post('caliber');
            
            if($this->watch->addWatch($this->session->userdata('userId'), $brand, $name, $yearOfBuy, $serial, $caliber))
            {
                $this->_bodyData['success'] = 'Watch successfully added!';
            }
            else
            {
               $this->_bodyData['error'] = 'An error occured while adding your watch.';
            }
        }
        else if($this->input->post('deleteMeasures'))
        {
            $this->event->add($this->event->DELETE_ALL_MEASURES);

            $measureId = $this->input->post('deleteMeasures');
            
            if($this->measure->deleteMesure($measureId))
            {
                $this->_bodyData['success'] = 'Measures successfully deleted!';
            }
            else
            {
               $this->_bodyData['error'] = 'An error occured while deleting your measures.';
            }
            
        }
        else if($this->input->post('deleteWatch'))
        {
            $this->event->add($this->event->DELETE_WATCH);

            $watchId = $this->input->post('deleteWatch');
            
            if($this->watch->deleteWatch($watchId))
            {
                $this->_bodyData['success'] = 'Watch successfully deleted!';
            }
            else
            {
               $this->_bodyData['error'] = 'An error occured while deleting your watch.';
            }
        }

        $this->event->add($this->event->BOARD_LOAD);
        
        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);
        
        $this->_bodyData['watches'] = $this->watch->getWatches($this->session->userdata('userId'));
        $this->_bodyData['allMeasure'] = $this->measure->getMeasuresByUser($this->session->userdata('userId'), 
            $this->_bodyData['watches']);
        
        $this->load->view('measure/all', $this->_bodyData);    
        
        $this->load->view('footer');  
    }
    
    public function new_watch()
    {    
        $this->event->add($this->event->ADD_WATCH);

        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);
        
        $this->load->view('measure/new-watch', $this->_bodyData);    
        
        $this->load->view('footer');  
    }
    
    public function new_measure()
    {

        $this->event->add($this->event->MEASURE_LOAD);

        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);
        
        $this->_bodyData['watches'] = $this->watch->getWatches($this->session->userdata('userId'));
        $this->load->view('measure/new-measure', $this->_bodyData);
        $this->load->view('measure/audio.php');

        
        $this->load->view('footer');  
    }  
    
    public function get_accuracy()
    {
        if($this->input->post('measureId') && $this->input->post('watchId'))
        {

            $this->event->add($this->event->ACCURACY_LOAD);

            $this->_headerData['headerClass'] = 'blue';
            array_push($this->_headerData['javaScripts'], "jquery.sharrre.min", "sharrre.logic", "watch.animation");
            $this->load->view('header', $this->_headerData);
        
            $this->_bodyData['selectedWatch'] = $this->watch->getWatch($this->input->post('watchId'));
            $this->_bodyData['measureId'] = $this->input->post('measureId');

            $this->load->view('measure/get-accuracy', $this->_bodyData);  
            $this->load->view('measure/audio.php');  
        
            $this->load->view('footer');  
                            
        }
        else
        {
            redirect('/measures/');
        }
    }
}