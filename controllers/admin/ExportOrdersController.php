<?php

if(!defined('_PS_VERSION_'))
	exit();
require_once dirname(__FILE__).'/../../models/ExportModel.php';



class ExportOrdersController extends AdminController{
	
	
	public function __construct(){
		$this->bootstrap = true;
		parent::__construct();
		
	}
	
 	public function getTemplatePath()
	{
		return dirname(__FILE__).'/../../views/templates/admin/';
	} 
		
	 public function createTemplate($tpl_name) {
        if (file_exists($this->getTemplatePath() . $tpl_name) && $this->viewAccess())
            return $this->context->smarty->createTemplate($this->getTemplatePath() . $tpl_name, $this->context->smarty);
            return parent::createTemplate($tpl_name);
	} 
	

	public function initContent(){

		$smarty = $this->context->smarty;
		
		$smarty->assign('orders',ExportModel::getExports());

		$this->content=$this->createTemplate('ExportOrders.tpl')->fetch();
		parent::initContent();
		
	}
	
	public function setMedia(){
		parent::setMedia();
		   $this->addJS('../views/js/table2CSV.js'); 
		}

}