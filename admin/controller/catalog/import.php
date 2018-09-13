<?php

class ControllerCatalogImport extends Controller
{
    private $error = array();

    public function index()
    {

        if (!empty($this->request->files)) {
            switch ($this->request->post['suppliers']):
                case '1':
                    $this->uploadDreamToys();
                    break;
                case '2':
                    $this->uploadBeles();
                    break;
            endswitch;
        }

        $this->load->language('catalog/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['form_url'] = $this->url->link('catalog/import', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $this->response->setOutput($this->load->view('catalog/import', $data));
    }

    public function uploadDreamToys()
    {

        $this->load->model('catalog/product');


        $filter_data = array(
            'vendor' => 1,
            'select_column' => 'vendor_id',
            'index_column' => 'vendor_id'
        );

        $exist_ids = $this->model_catalog_product->getProductsColumn($filter_data);
        $xml_ids = [];
        $update_ids = [];
        $outOfStock_ids = [];

        copy($_FILES['file']['tmp_name'], 'dreamtoys.xml');
        $xml = simplexml_load_file('dreamtoys.xml');

        //проверка того ли поставщика прайс
        if ((string)$xml->shop->url !== 'http://dreamtoys.com.ua') return;

        foreach ($xml->shop->offers->offer as $item) {
            $xml_ids[] = (string)$item['id'];
        }
        $new_ids = array_diff($xml_ids, $exist_ids);
        $update_ids = array_intersect($exist_ids, $xml_ids);
        $outOfStock_ids = array_diff($exist_ids, $xml_ids);

        $img_dir = 'catalog/products/auto_copy/dream_toys/';
        if (!file_exists(DIR_IMAGE . $img_dir))
            if (!mkdir(DIR_IMAGE . $img_dir, 0777, 1)) die(DIR_IMAGE . $img_dir);


        $this->load->model('catalog/pricing');
        $pricing = $this->model_catalog_pricing->getMaths();

        //==============================Added new item=============================//
        foreach ($xml->shop->offers->offer as $item) {

            if (!in_array((string)$item['id'], $new_ids)) continue;

            $data['model'] = $item->vendorCode;
            $data['quantity'] = 100;
            $data['minimum'] = 1;
            $data['mpn'] = '';
            $data['sku'] = '';
            $data['ean'] = '';
            $data['isbn'] = '';
            $data['location'] = '';
            $data['upc'] = '';
            $data['jan'] = '';
            $data['subtract'] = 0;
            $data['stock_status_id'] = 9;
            $data['date_available'] = date('Y-m-d');
            $data['manufacturer_id'] = 0;
            $data['shipping'] = 1;
            $data['price'] = ceil(((float)$item->price + (int)$pricing[1]['plus']) * (float)$pricing[1]['multiply']);
            $data['pricing_id'] = 1;
            $data['points'] = 0;
            $data['weight'] = 0;
            $data['weight_class_id'] = 7;
            $data['length'] = 0;
            $data['width'] = 0;
            $data['height'] = 0;
            $data['length_class_id'] = 4;
            $data['status'] = 1;
            $data['vendor'] = 1;
            $data['vendor_id'] = (string)$item['id'];
            $data['tax_class_id'] = 0;
            $data['sort_order'] = 1000;

            $data['product_store'] = [0];

            $full_path_name = $img_dir . pathinfo($item->picture, PATHINFO_BASENAME);

            if (!file_exists(DIR_IMAGE . $full_path_name)) {
                if (copy($item->picture, DIR_IMAGE . $full_path_name))
                    $data['image'] = $full_path_name;
                else
                    $data['image'] = '';
            } else {
                $data['image'] = $full_path_name;
            }

            $data['product_description'][2] = [
                'name' => htmlspecialchars($item->name),
                'description' => htmlspecialchars($item->name),
                'tag' => '',
                'meta_title' => htmlspecialchars($item->name),
                'meta_description' => '',
                'meta_keyword' => '',
            ];
            $this->model_catalog_product->addProduct($data);
        }

        $filter_data = array(
            'vendor' => 1,
            'select_column' => 'pricing_id',
            'index_column' => 'vendor_id'
        );

        $pricing_ids = $this->model_catalog_product->getProductsColumn($filter_data);

        //==============================Update item=============================//
        foreach ($xml->shop->offers->offer as $item) {
            if (!in_array((string)$item['id'], $update_ids)) continue;

            $full_path_name = $img_dir . pathinfo($item->picture, PATHINFO_BASENAME);

            if (!file_exists(DIR_IMAGE . $full_path_name)) {
                if (copy($item->picture, DIR_IMAGE . $full_path_name))
                    $data['image'] = $full_path_name;
                else
                    $data['image'] = '';
            } else {
                $data['image'] = $full_path_name;
            }


            $price = ceil(((float)$item->price + (int)$pricing[$pricing_ids[(int)$item['id']]]['plus']) * (float)$pricing[$pricing_ids[(int)$item['id']]]['multiply']);

            $this->model_catalog_product->updateProductPrice($item['id'], 1, $price);
        }

        //==============================outOfStock item=============================//
        foreach ($outOfStock_ids as $ofStock_id) {

            $this->model_catalog_product->setProductStatus($ofStock_id, 1, 0);

        }
    }

    public function uploadBeles()
    {

        $this->load->model('catalog/product');


        $filter_data = array(
            'vendor' => 2,
            'select_column' => 'vendor_id',
            'index_column' => 'vendor_id'
        );

        $exist_ids = $this->model_catalog_product->getProductsColumn($filter_data);

        $xml_ids = [];
        $update_ids = [];
        $outOfStock_ids = [];

        copy($_FILES['file']['tmp_name'], 'beles.yml');


        $file = file_get_contents('beles.yml');
        $file = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $file);
        $xml = simplexml_load_string($file);

        //проверка того ли поставщика прайс
        if ((string)$xml->shop->url !== 'https://beles.com.ua/') return;

//        $xml = simplexml_load_file('beles.yml');

        foreach ($xml->shop->offers->offer as $item) {
            $xml_ids[] = (string)$item->model;
        }

        $new_ids = array_diff($xml_ids, $exist_ids);
        $update_ids = array_intersect($exist_ids, $xml_ids);
        $outOfStock_ids = array_diff($exist_ids, $xml_ids);


        $img_dir = 'catalog/products/auto_copy/beles/';
        if (!file_exists(DIR_IMAGE . $img_dir))
            if (!mkdir(DIR_IMAGE . $img_dir, 0777, 1)) die(DIR_IMAGE . $img_dir);


        $this->load->model('catalog/pricing');
        $pricing = $this->model_catalog_pricing->getMaths();


        //==============================Added new item=============================//
        foreach ($xml->shop->offers->offer as $item) {

            if (!in_array((string)$item->model, $new_ids)) continue;

            $data['model'] = $item->model;
            $data['quantity'] = 100;
            $data['minimum'] = 1;
            $data['mpn'] = '';
            $data['sku'] = '';
            $data['ean'] = '';
            $data['isbn'] = '';
            $data['location'] = '';
            $data['upc'] = '';
            $data['jan'] = '';
            $data['subtract'] = 0;
            $data['stock_status_id'] = 9;
            $data['date_available'] = date('Y-m-d');
            $data['manufacturer_id'] = 0;
            $data['shipping'] = 1;

            $data['price'] = ceil(((float)$item->price + (float)$pricing[1]['plus']) * (float)$pricing[1]['multiply']);
            $data['pricing_id'] = 1;
            $data['points'] = 0;
            $data['weight'] = 0;
            $data['weight_class_id'] = 7;
            $data['length'] = 0;
            $data['width'] = 0;
            $data['height'] = 0;
            $data['length_class_id'] = 4;
            $data['status'] = 1;
            $data['vendor'] = 2;
            $data['vendor_id'] = (string)$item->model;
            $data['tax_class_id'] = 0;
            $data['sort_order'] = 1000;

            $data['product_store'] = [0];

            $full_path_name = $img_dir . pathinfo($item->picture, PATHINFO_BASENAME);

            if (file_exists(DIR_IMAGE . $full_path_name) or copy($item->picture, DIR_IMAGE . $full_path_name)) {
                $data['image'] = $full_path_name;
            }

            $data['product_description'][2] = [
                'name' => htmlspecialchars($item->name),
                'description' => htmlspecialchars($item->name),
                'tag' => '',
                'meta_title' => htmlspecialchars($item->name),
                'meta_description' => '',
                'meta_keyword' => '',
            ];
            $this->model_catalog_product->addProduct($data);
        }

        $filter_data = array(
            'vendor' => 2,
            'select_column' => 'pricing_id',
            'index_column' => 'vendor_id'
        );

        $pricing_ids = $this->model_catalog_product->getProductsColumn($filter_data);


        //==============================Update item=============================//
        foreach ($xml->shop->offers->offer as $item) {
            if (!in_array((string)$item->model, $update_ids)) continue;
            /* if ($item->model == '112944') {
                 echo '<pre>';
                 var_dump((float)$item->price);
                 var_dump((int)$pricing_ids[(string)$item->model]);
                 var_dump($pricing);
                 var_dump($pricing[(int)$pricing_ids[(string)$item->model]]);
                 var_dump($pricing[(int)$pricing_ids[(string)$item->model]]['plus']);
                 var_dump($pricing[(int)$pricing_ids[(string)$item->model]]['multiply']);
                 var_dump(ceil(((float)$item->price + $pricing[(int)$pricing_ids[(string)$item->model]]['plus']) * $pricing[(int)$pricing_ids[(string)$item->model]]['multiply']));
                 die;
             }*/

            $full_path_name = $img_dir . pathinfo($item->picture, PATHINFO_BASENAME);


            if (!file_exists(DIR_IMAGE . $full_path_name)) {
                if (copy($item->picture, DIR_IMAGE . $full_path_name)) {


                }
            }

            $price = ceil(((float)$item->price + $pricing[(int)$pricing_ids[(string)$item->model]]['plus']) * $pricing[(int)$pricing_ids[(string)$item->model]]['multiply']);
            $this->model_catalog_product->updateProductPrice($item->model, 2, $price);
        }

        //==============================outOfStock item=============================//
        foreach ($outOfStock_ids as $ofStock_id) {

            $this->model_catalog_product->setProductStatus($ofStock_id, 2, 0);

        }
    }
}
