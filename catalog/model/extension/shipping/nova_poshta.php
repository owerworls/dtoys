<?php
class ModelExtensionShippingNovaPoshta extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/nova_poshta');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_nova_poshta_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_nova_poshta_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		if ($this->cart->getSubTotal() < $this->config->get('shipping_nova_poshta_total')) {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['nova_poshta'] = array(
				'code'         => 'nova_poshta.nova_poshta',
				'title'        => $this->language->get('text_description'),
				'cost'         => 0.00,
				'tax_class_id' => 0,
				'text'         => $this->currency->format(0.00, $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'nova_poshta',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_nova_poshta_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}

	function updateAreas(){
        $post='
        {
        "apiKey": "434c8ae6c27c90ef858697d702212dc2",
        "modelName": "Address",
        "calledMethod": "getAreas",
        "methodProperties": {}
        }
        ';

        $ch1 = curl_init('http://api.novaposhta.ua/v2.0/json/AddressGeneral/getWarehouses');
        curl_setopt($ch1, CURLOPT_HEADER, 0);//выводить заголовки
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);// 1 - вывод в переменную
        curl_setopt($ch1, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36");
        curl_setopt($ch1, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch1);
        $response = json_decode($response);
        curl_close($ch1);

        if(!$response->success){
            return false;
        }

        $sql="INSERT INTO `" . DB_PREFIX . "np_areas` (`ref`, `description`, `description_ru`, `areas_center`) VALUES ";

        foreach ($response->data as $datum){
            $sql.="('{$datum->Ref}', '{$datum->Description}', '{$datum->Description}', '{$datum->AreasCenter}'),";
        }

        $sql=substr($sql,0,-1);

        $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "np_areas`");
        $this->db->query($sql);

        return $response;
    }

	function updateCities(){
        $post='
        {
            "apiKey": "434c8ae6c27c90ef858697d702212dc2",
            "modelName": "Address",
            "calledMethod": "getCities"
        }
        ';

        $ch1 = curl_init('http://api.novaposhta.ua/v2.0/json/Address/getCities');
        curl_setopt($ch1, CURLOPT_HEADER, 0);//выводить заголовки
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);// 1 - вывод в переменную
        curl_setopt($ch1, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36");
        curl_setopt($ch1, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch1);
        $response = json_decode($response);
        curl_close($ch1);

        if(!$response->success){
            return false;
        }
        $sql="INSERT INTO `" . DB_PREFIX . "np_cities` (`ref`, `description`, `description_ru`, `area`, `city_id`, `settlement_type`, `settlement_type_description`, `settlement_type_description_ru`) VALUES ";

        foreach ($response->data as $datum){
            $sql.="('{$datum->Ref}', '".addslashes($datum->Description)."', '".addslashes($datum->DescriptionRu)."', '{$datum->Area}', '{$datum->CityID}', '{$datum->SettlementType}', '".addslashes($datum->SettlementTypeDescription)."', '".addslashes($datum->SettlementTypeDescriptionRu)."'),";
        }

        $sql=substr($sql,0,-1);

        $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "np_cities`");
        $this->db->query($sql);

        return $response;
    }

	function updateWarehouses(){
        $post='
        {
            "apiKey": "434c8ae6c27c90ef858697d702212dc2",
            "modelName": "AddressGeneral",
            "calledMethod": "getWarehouses",
            "methodProperties": 
            {
                "Language": "ru"
            }
        }
        ';

        $ch1 = curl_init('http://api.novaposhta.ua/v2.0/json/AddressGeneral/getWarehouses');
        curl_setopt($ch1, CURLOPT_HEADER, 0);//выводить заголовки
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);// 1 - вывод в переменную
        curl_setopt($ch1, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36");
        curl_setopt($ch1, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch1);
        $response = json_decode($response);
        curl_close($ch1);

        if(!$response->success){
            return false;
        }

        $sql="INSERT INTO `" . DB_PREFIX . "np_warehouses` (`ref`, `site_key`, `number`, `description`, `description_ru`, `type_of_warehouse`, `city_ref`, `city_description`, `city_description_ru`) VALUES ";

        foreach ($response->data as $datum){
            $sql.="('{$datum->Ref}', '{$datum->CityRef}', '{$datum->Number}', '".addslashes($datum->Description)."', '".addslashes($datum->DescriptionRu)."', '{$datum->TypeOfWarehouse}', '".addslashes($datum->CityRef)."', '".addslashes($datum->CityDescription)."', '".addslashes($datum->CityDescriptionRu)."'),";
        }

        $sql=substr($sql,0,-1);

        $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "np_warehouses`");
        $this->db->query($sql);

        return $response;
    }

    function getAreas(){
        $sql="SELECT * FROM  `" . DB_PREFIX . "np_areas`  ";
        $result=$this->db->query($sql);
        return $result->rows;
    }

    function getCities($area){
        $sql="SELECT * FROM  `" . DB_PREFIX . "np_cities` where `area`='$area' ORDER BY `city_id` ASC ";
        $result=$this->db->query($sql);
        return $result->rows;
    }

    function getWarehouses($city){
        $sql="SELECT * FROM  `" . DB_PREFIX . "np_warehouses` where `city_ref`='$city' ORDER BY `number` ASC ";
        $result=$this->db->query($sql);
        return $result->rows;
    }

    function getArea($area,$field=null){
        $sql="SELECT * FROM  `" . DB_PREFIX . "np_areas` where `ref`='$area' ";
        $result=$this->db->query($sql);
        return $field?$result->row[$field]:$result->row;
    }

    function getCity($city,$field=''){
        $sql="SELECT * FROM  `" . DB_PREFIX . "np_cities` where `ref`='$city' ";
        $result=$this->db->query($sql);
        return $field?$result->row[$field]:$result->row;
    }

    function getWarehouse($warehouse,$field=null){
        $sql="SELECT * FROM  `" . DB_PREFIX . "np_warehouses` where `ref`='$warehouse' ";
        $result=$this->db->query($sql);
        return $field?$result->row[$field]:$result->row;
    }

}
