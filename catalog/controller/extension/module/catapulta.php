<?php

class ControllerExtensionModuleCatapulta extends Controller
{

    public function test()
    {
        echo 'test';
    }

    public function index()
    {
        if ($this->config->get('catapulta_status')) {
            $this->language->load('module/catapulta');

            $this->document->addStyle('catalog/view/javascript/jquery/colorbox2/colorbox.css');
            $this->document->addScript('catalog/view/javascript/jquery/colorbox2/jquery.colorbox-min.js');

            $this->document->addScript('catalog/view/javascript/jquery/jquery.maskedinput.min.js');
            $this->document->addScript('catalog/view/javascript/catapulta.js');

            $data['heading_title'] = $this->language->get('heading_title');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/catapulta.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/module/catapulta.tpl', $data);
            } else {
                return $this->load->view('default/template/module/catapulta.tpl', $data);
            }
        }
    }


    public function form()
    {

        $data['text_wait'] = 'Подождите...';

        $data['product_id'] = $_POST['product_id'];

        $data['phone_mask'] = '+375 (хх) ххх хх хх';

//        $phone_text = $this->config->get('catapulta_phone_text');

        $this->response->setOutput($this->load->view('extension/module/catapulta_form', $data));

    }


    public function write()
    {
        $this->load->model('extension/module/catapulta');

        $json = array();


        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $contact = $this->request->post['contact'];
            $product_id = $this->request->post['product_id'];
        } else {
            exit;
        }

        if ($product_id) {
            if ((utf8_strlen($contact) < 3) || (utf8_strlen($contact) > 20)) {
                if ($this->config->get('catapulta_phone_mask_status')) {
                    $json['error']['contact'] = $this->language->get('error_mask');
                } else {
                    $json['error']['contact'] = $this->language->get('error_contact');
                }
            }

            if (!isset($json['error'])) {

                $this->load->model('catalog/product');

                $product_info = $this->model_catalog_product->getProduct($product_id);

                $price = isset($product_info['special']) ? $product_info['special'] : $product_info['price'];

                $data = array(
                    'contact' => $contact,
                    'product_id' => $product_id,
                    'product_name' => $product_info['name'],
                    'product_model' => $product_info['model'],
                    'total' => $price
                );

                $order_id = $this->model_extension_module_catapulta->addOrder($data);


                $email_subject = sprintf($this->language->get('text_subject'), $this->language->get('heading_title'), $this->config->get('config_name'), $order_id);
                $email_text =  "\n\n";
//                $email_text = sprintf($this->language->get('text_order'), $order_id) . "\n\n";
                $email_text .=  "<b>Телефон:</b>  " .html_entity_decode($contact) . "\n<br>";

                $email_text .=  "<b>Товар:</b> <a href='". $this->url->link('product/product', 'product_id=' . $product_id)."'>".html_entity_decode($product_info['name'])."</a> " . "\n<br>";
//                $email_text .= "<b>ИД продукта:</b> " . $product_id . "\n<br>";
//                $email_text .= "<b>Ссылка:</b>  " . $this->url->link('product/product', 'product_id=' . $product_id) . "\n<br>\n<br>";
                $email_text .= "<b>Дата:</b>  " .date('d.m.Y H:i'). "\n<br>\n<br>";
//                $email_text .= "Ссылка:  " .$this->request->server['REMOTE_ADDR'] . "\n\n";
                $email_text .= "<b>Цена:</b>  " .$price;




                $this->load->model('setting/setting');
                $from = $this->model_setting_setting->getSettingValue('config_email', $order_info['store_id']);

                if (!$from) {
                    $from = $this->config->get('config_email');
                }


                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($from);
                $mail->setSender(html_entity_decode('Все для празника', ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(html_entity_decode('Быстрый заказ', ENT_QUOTES, 'UTF-8'));
                $mail->setHtml($email_text);
//                $mail->setText($email_text);
                $mail->send();





               /* $mail = new Mail();

                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = $this->config->get('config_mail_smtp_password');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

//                    $mail->setTo('chugusov.igor@gmail.com');
                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($this->config->get('config_name'));
                $mail->setSubject($email_subject);
                $mail->setText($email_text);
                $mail->send();*/

                // Send to additional alert emails
                $emails = explode(',', $this->config->get('config_mail_alert_email'));

                foreach ($emails as $email) {
                    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $mail->setTo($email);
                        $mail->send();
                    }
                }
                return;
                //Очищаем корзину после заказа в 1 клик.
                $this->cart->clearCart();

                $json['success'] = $this->language->get('text_success');
            }
        }

        $this->response->setOutput(json_encode($json));
    }


    public function send($settings = array())
    {
        if ($settings) {
            $phone = $settings['phone'];
            $model = $settings['model'];
            $url = $settings['url'];
        } elseif ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $phone = $this->request->post['phone'];
            $model = $this->request->post['model'];
            $url = $this->request->post['url'];
            $product_id = $this->request->post['product_id'];
            $name = $this->request->post['name'];
        }

        $day_today = date("d.m.y");
        $today[1] = date("H:i:s");
        $date = "Время запроса: $today[1] и дата: $day_today .";
        $text = $date . '<br/>' . 'Телефон: <a href="tel:' . $phone . '">' . $phone . '</a><br/>Ссылка на продукт: ' . $url . '<br/> Модель:' . $model . '<br/>';
        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = $this->config->get('config_mail_smtp_password');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        $mail->setTo('sohoroomsonline@gmail.com');
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender($this->config->get('config_name'));
        $mail->setSubject('Запрос сообщить. Модель ' . $model);
        $mail->setHtml($text);
        var_dump($mail->send());

        $bitrix24login = "lambova.anna@gmail.com"; // Укажите логин аккаунта для добавления лида
        $bitrix24password = "дфьищмф34"; // Укажите пароль аккаунта для добавления лида
        $bitrix24domen = "b24-n59a93821daede.bitrix24.ua"; // Укажите домен вашего битрикса


        $query = $this->db->query("select text from product_attribute where product_id='$product_id' and attribute_id=14");
        $brand = $query->row['text'];

        $query = $this->db->query("select text from product_attribute where product_id='$product_id' and attribute_id=18");
        $collection = $query->row['text'];


        $bitrix24products =
            "<b>Название:</b> " . $name .
            "<br/><b>Модель:</b> " . $model . " (<a target='_blank' href='" . $url . "'>Ссылка</a>)" .
            "<br/><b>Бренд:</b> " . $brand .
            "<br/><b>Коллекция:</b> " . $collection .
            "<br/><b>Контакт:</b> " . $phone;


        $bitrix24GetData = array(
            'LOGIN' => $bitrix24login,
            'PASSWORD' => $bitrix24password,
            'TITLE' => "Сообщить об акциии",
            'PHONE_OTHER' => $phone,
            'OPPORTUNITY' => 0,
            'SOURCE_ID' => 'WEB',
            'SOURCE_DESCRIPTION' => $data['store_url'],
            'COMMENTS' => $bitrix24products
        );

        $bitrix24GetData = http_build_query($bitrix24GetData);

        file_get_contents("https://" . $bitrix24domen . "/crm/configs/import/lead.php?" . $bitrix24GetData);

    }


}
