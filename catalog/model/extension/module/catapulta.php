<?php
class ModelExtensionModuleCatapulta extends Model {

    public function addOrder($data) {

        $sql="
          INSERT INTO " . DB_PREFIX . "order
          SET 	telephone = '" . $this->db->escape($data['contact']) . "',
          store_id='0',
          store_name='Your Store',
          firstname='Быст.заказ',
          total = '". $data['total'] ."',
          order_status_id = '1',
          currency_id='4',
          currency_code='BYN',
          payment_method='Оплата при доставке',
          payment_code='cod',
          date_added = NOW(),
          date_modified = NOW()
        ";

        $this->db->query($sql);

        $order_id = $this->db->getLastId();

        $sql="
          INSERT INTO " . DB_PREFIX . "order_product
          SET 	order_id = '" . $order_id . "',
          product_id = '" . (int) $data['product_id'] . "',
          name = '" . $data['product_name'] . "',
          model = '" . $data['product_model'] . "',
          price = '". $data['total'] ."'
        ";

        $this->db->query($sql);
/*
        $sql="
          INSERT INTO " . DB_PREFIX . "catapulta
          SET contact = '" . $this->db->escape($data['contact']) . "',
          product_id = '" . (int) $data['product_id'] . "',
          product_name = '" . $data['product_name'] . "',
          corrency_id = '4',
          corrency_code = 'BYN',
          total = '',
          date_added = NOW()
        ";

        $this->db->query($sql);*/

        $order_id = $this->db->getLastId();

        return $order_id;
    }

}

?>