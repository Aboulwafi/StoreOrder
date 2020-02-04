<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__).'/models/StatusModel.php';
class StoresOrder extends Module
{
    protected $config_form = false;

    	//Definition des menus 
	protected $tabs = [
        [
            'name'      => 'Stores Order',
            'className' => 'ExportOrders',
            'active'    => 1,
        ],
    ];





    public function __construct()
    {
        $this->name = 'StoresOrder';
        $this->tab = 'export';
        $this->version = '1.1.0';
        $this->author = 'issam aboulwafi';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Stores Order');
        $this->description = $this->l('enables to  extract products to be pushed  to  stores ');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

    }

 
    public function install()
    {
        $this->addTab($this->tabs);
        return parent::install() &&
            $this->registerHook('displayBackOfficeHeader');
            $this->registerHook('actionAdminControllerSetMedia');

           
    }

    public function uninstall()
    {
        $this->removeTab($this->tabs);
        $this->unregisterHook('displayBackOfficeHeader');
		$this->unregisterHook('actionAdminControllerSetMedia');
        return parent::uninstall();
    }



    public function addTab(
        $tabs,
        $id_parent = 0
    )
    {
        foreach ($tabs as $tab)
        {
            $tabModel             = new Tab();
            $tabModel->module     = $this->name;
            $tabModel->active     = $tab['active'];
            $tabModel->class_name = $tab['className'];
            $tabModel->id_parent  = $id_parent;

            //tab text in each language
            foreach (Language::getLanguages(true) as $lang)
            {
                $tabModel->name[$lang['id_lang']] = $tab['name'];
            }

            $tabModel->add();
            if (isset($tab['childs']) && is_array($tab['childs']))
            {
                $this->addTab($tab['childs'], Tab::getIdFromClassName($tab['className']));
            }
        }
        return true;
    }

    public function removeTab($tabs)
    {
        foreach ($tabs as $tab)
        {
            $id_tab = (int) Tab::getIdFromClassName($tab["className"]);
            if ($id_tab)
            {
                $tabModel = new Tab($id_tab);
                $tabModel->delete();
            }

            if (isset($tab["childs"]) && is_array($tab["childs"]))
            {
                $this->removeTab($tab["childs"]);
            }
        }

        return true;
    }












    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */

         
        if (((bool)Tools::isSubmit('submitStoresOrderModule')) == true) {
            $this->postProcess();
         
        
            Configuration::updateValue('STORESORDER_ORDER_STATE',Tools::getValue('STORESORDER_ORDER_STATE'));
        
        }

        $this->context->smarty->assign('module_dir', $this->_path);
       

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitStoresOrderModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $helper->fields_value['STORESORDER_ORDER_STATE'] = Configuration::get('STORESORDER_ORDER_STATE');
        return $helper->generateForm(array($this->getConfigForm()));

       
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $default_state = Configuration::get('STORESORDER_ORDER_STATE');
        $option = array();
        $options = StatusModel::getState();
        $default = array();
      

        foreach($options as $op)
        {
            if($op['id_order_state']== $default_state){
                $default = array(
                    'id_order_state' => $op['id_order_state'],
                    'name' => $op['name'],
                    'value' => $op['id_order_state'],
                    'label' => $op['name']
        
                );
            }else{
                array_push($option,
                            array(
                                'id_order_state' => $op['id_order_state'],
                                'name' => $op['name'],
                                'value' => $op['id_order_state'],
                                'label' => $op['name']
                
                ));
            }
        }

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 6,
                        'type' => 'select',
                        'desc' => $this->l('Select an Order  Status'),
                        'name' => 'STORESORDER_ORDER_STATE',
                        'label' => $this->l('State'),
                         'options' => array(
                            'query' => $option,
                            'id' => 'id_order_state', 
                            'name' => 'name',
                            'default' =>  $default,
                          ) 
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'STORESORDER_ORDER_STATE' => Configuration::get('STORESORDER_ORDER_STATE'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            //$this->context->controller->addJquery(); 
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addJS($this->_path.'views/js/jq.js');
            $this->context->controller->addJS($this->_path.'views/js/table2CSV.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
}
