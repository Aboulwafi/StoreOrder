<?php

class ExportModel {

    public static function getExports(){
        $state = Configuration::get('STORESORDER_ORDER_STATE');
		$sql = "SELECT d.product_reference as code_article ,SUM(d.product_quantity) as quantite, date_format(sysdate(),'%d/%m/%Y') as date 
        FROM "._DB_PREFIX_."order_detail d 
        LEFT JOIN "._DB_PREFIX_."orders o ON (d.id_order = o.id_order) 
        LEFT JOIN "._DB_PREFIX_."order_state os ON (os.id_order_state = o.current_state) 
        LEFT JOIN "._DB_PREFIX_."product pr ON (pr.id_product = d.product_id) 
        WHERE 1 AND os.id_order_state = ".$state."
        GROUP BY d.product_reference";
		


		if ($results = Db::getInstance()->ExecuteS($sql))
            return $results;     
        
    }
}