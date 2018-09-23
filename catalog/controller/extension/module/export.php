<?php

class ControllerExtensionModuleExport extends Controller
{
    public function index()
    {

       $this->yml();
    }

    private $availableCategories = [70, 71];

    public function yml()
    {
        $date = date('Y-m-d H:i');
        $xml = '<yml_catalog date="2010-04-01 17:00">
                    <shop>
                    <name>Magazin</name>
                    <company>Magazin</company>
                    <url>http://www.magazin.ru/</url>
                    <currencies>
                    <currency id="RUR" rate="1" plus="0"/>
                    </currencies>
                    <categories>
                    <category id="1">Оргтехника</category>
                    <category id="10" parentId="1">Принтеры</category>
                    <category id="100" parentId="10">Струйные принтеры</category>
                    <category id="101" parentId="10">Лазерные принтеры</category>
                    <category id="2">Фототехника</category>
                    <category id="11" parentId="2">Фотоаппараты</category>
                    <category id="12" parentId="2">Объективы</category>
                    <category id="3">Книги</category>
                    <category id="13" parentId="3">Детективы</category>
                    <category id="14" parentId="3">Художественная литература</category>
                    <category id="15" parentId="3">Учебная литература</category>
                    <category id="16" parentId="3">Детская литература</category>
                    <category id="4">Музыка и видеофильмы</category>
                    <category id="17" parentId="4">Музыка</category>
                    <category id="18" parentId="4">Видеофильмы</category>
                    <category id="5">Путешествия</category>
                    <category id="19" parentId="5">Туры</category>
                    <category id="20" parentId="5">Авиабилеты</category>
                    <category id="6">Билеты на мероприятия</category>
                    </categories>';

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');


        $results = $this->model_catalog_category->getCategories();
var_dump($results); die;
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