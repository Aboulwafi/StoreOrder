<?php

class StatusModel {
    public static function getState(){
        $sql = " select *
        FROM "._DB_PREFIX_."order_state a 
        LEFT JOIN "._DB_PREFIX_."order_state_lang b ON (b.id_order_state = a.id_order_state AND b.id_lang = 3)
        WHERE 1  
        ORDER BY a.id_order_state ASC";


		if ($results = Db::getInstance()->ExecuteS($sql))
            return $results;     
	}
}