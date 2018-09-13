<?php
class ModelCatalogPricing extends Model {

	public function getList() {
		$sql = "SELECT * FROM " . DB_PREFIX . "pricing ";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getMaths() {
		$sql = "SELECT * FROM " . DB_PREFIX . "pricing ";
		$query = $this->db->query($sql);

		$result=[];
        foreach ($query->rows as $item) {
            switch($item['type']){
                case 'F': $result[$item['id']]=['multiply'=>1,'plus'=>$item['value']] ; break;
                case 'P': $result[$item['id']]=['multiply'=>1+($item['value']/100),'plus'=>0] ; break;
                default: $result[$item['id']]=['multiply'=>1,'plus'=>0];
            }
		}
		return $result;
	}

	public function addPricing($data){
        $this->db->query("INSERT INTO " . DB_PREFIX . "pricing SET title = '" . $this->db->escape($data['title']) . "',  type = '" . $this->db->escape($data['type']) . "',  value = '" . $data['value'] . "'");
//        $product_id = $this->db->getLastId();
    }

	public function editPricing($data){
        $this->db->query("UPDATE " . DB_PREFIX . "pricing SET title = '" . $this->db->escape($data['title']) . "',  type = '" . $this->db->escape($data['type']) . "',  value = '" . $data['value'] . "' where id='" . $data['pricing_id'] . "'");
//        $product_id = $this->db->getLastId();
    }

	public function getPricing($pricing_id){
        $sql = "SELECT * FROM " . DB_PREFIX . "pricing where id='$pricing_id' ";
        $query = $this->db->query($sql);

        return $query->row;
    }

}
