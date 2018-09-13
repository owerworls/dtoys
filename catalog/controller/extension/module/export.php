<?php

class ControllerExtensionModuleExport extends Controller
{
    public function index()
    {
       $this->product();
    }

    private $availableCategories = [70, 71];

    public function yml()
    {
        $date = date('Y-m-d H:i');
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
        <yml_catalog date=\"$date\">
          <shop>
            <name>Интернет магазин «Conte Frostini»</name>
            <company>Интернет магазин «Conte Frostini»</company>
            <url>https://frostini.com.ua/</url>
            <currencies>
              <currency id=\"USD\" rate=\"CB\"/>
              <currency id=\"KZT\" rate=\"CB\"/>
              <currency id=\"RUR\" rate=\"CB\"/>
              <currency id=\"BYN\" rate=\"CB\"/>
              <currency id=\"UAH\" rate=\"1\"/>
              <currency id=\"EUR\" rate=\"CB\"/>
            </currencies>
            <categories>";

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');


        $results = $this->model_catalog_category->getCategories($this->availableCategories);

        foreach ($results as $result) {
            $xml .= "<category id=\"{$result['category_id']}\">{$result['name']}</category>";
        };
        $xml .= "</categories>";
        $xml .= "<offers>";

        $filter_data = array(
            'filter_category_id' => null,
            'filter_filter' => '',
            'sort' => 'p.sort_order',
            'order' => 'ASC',
            'start' => 0,
            'limit' => 1000000
        );

        $results = $this->model_catalog_product->getProducts($filter_data);
        $offers = '';
        foreach ($results as $result) {


            $price = isset($result['special']) ? $result['special'] : $result['price'];
            $offers .= "<offer available=\"true\" id=\"{$result['product_id']}\">";
            $offers .= "<price>$price</price>";
            $offers .= "<currencyId>UAH</currencyId>";

            $categories = $this->model_catalog_product->getCategories($result['product_id']);

            $url = '';
            foreach ($categories as $category) {

                if (!in_array($category['category_id'], $this->availableCategories)) continue;
                if (empty($url)) {
                    $url = $this->url->link('product/product', 'path=' . $category['category_id'] . '&product_id=' . $result['product_id']);
                }

                $offers .= "<categoryId>{$category['category_id']}</categoryId>";
            }

            $images = '';

            $image_results = $this->model_catalog_product->getProductImages($result['product_id']);
            foreach ($image_results as $image_result) {
                $images .= '<picture>http://contefrostini.com/image/' . $image_result['image'] . '</picture>';
            }

            $offers .= "<url>$url</url>";
            $offers .= $images;
            $offers .= "<pickup>false</pickup>";
            $offers .= "<delivery>true</delivery>";
            $offers .= "<name>{$result['name']}</name>";
//            $offers .= "<name>Сумка Conte Frostini {$result['upc']} {$result['model']} </name>";
            $offers .= "<vendorCode>{$result['upc']}</vendorCode>";
            $offers .= "<country_of_origin>Италия</country_of_origin>";
            $offers .= "<description>{$result['description']}</description>";
            $offers .= '<param name="Страна-производитель">Италия</param>';

            $attribute_groups = $this->model_catalog_product->getProductAttributes($result['product_id']);

            $attributes = '';
            foreach ($attribute_groups as $attribute_group) {
                foreach ($attribute_group['attribute'] as $item) {

                    $attributes .= "<param name=\"{$item['name']}\" >{$item['text']}</param>";

                }
            }
            $offers .= $attributes;

            $offers .= "<vendor>Conte Frostini</vendor>";
            $offers .= "<stock_quantity>{$result['quantity']}</stock_quantity>";


            $offers .= "</offer>";

        }

        $xml .= $offers;
        $xml .= "</offers>";
        $xml .= "</shop>";
        $xml .= "</yml_catalog>";

        header('Content-type: text/xml');
        header('Pragma: public');
        header('Cache-control: private');
        header('Expires: -1');
//        header('Content-Disposition: attachment; filename="rebenku.xml"');
        file_put_contents('php://output', $xml);

    }
}