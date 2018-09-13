<?php

class ControllerCheckoutOcsCart extends Controller
{

    public function index()
    {

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->response->redirect($this->url->link('checkout/cart'));
        }

        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $this->response->redirect($this->url->link('checkout/cart'));
            }
        }

        $this->load->language('checkout/checkout');
        $this->load->language('checkout/cart');

        $this->load->model('extension/shipping/nova_poshta');

        $this->model_extension_shipping_nova_poshta->updateWarehouses();


        $data['areas'] = $this->model_extension_shipping_nova_poshta->getAreas();

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_cart'),
            'href' => $this->url->link('checkout/cart')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('checkout/checkout', '', true)
        );

        $data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'), 1);
        $data['text_checkout_account'] = sprintf($this->language->get('text_checkout_account'), 2);
        $data['text_checkout_payment_address'] = sprintf($this->language->get('text_checkout_payment_address'), 2);
        $data['text_checkout_shipping_address'] = sprintf($this->language->get('text_checkout_shipping_address'), 3);
        $data['text_checkout_shipping_method'] = sprintf($this->language->get('text_checkout_shipping_method'), 4);

        if ($this->cart->hasShipping()) {
            $data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
            $data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
        } else {
            $data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
            $data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);
        }

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error_warning'] = '';
        }

        $data['logged'] = $this->customer->isLogged();

        if (isset($this->session->data['account'])) {
            $data['account'] = $this->session->data['account'];
        } else {
            $data['account'] = '';
        }

        // Cart

        $this->load->model('tool/image');
        $this->load->model('tool/upload');

        $data['products'] = array();

        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
            }

            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
            } else {
                $image = '';
            }

            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['value'];
                } else {
                    $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                    if ($upload_info) {
                        $value = $upload_info['name'];
                    } else {
                        $value = '';
                    }
                }

                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                );
            }

            // Display prices
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

                $price = $this->currency->format($unit_price, $this->session->data['currency']);
                $total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
            } else {
                $price = false;
                $total = false;
            }

            $recurring = '';

            if ($product['recurring']) {
                $frequencies = array(
                    'day' => $this->language->get('text_day'),
                    'week' => $this->language->get('text_week'),
                    'semi_month' => $this->language->get('text_semi_month'),
                    'month' => $this->language->get('text_month'),
                    'year' => $this->language->get('text_year')
                );

                if ($product['recurring']['trial']) {
                    $recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
                }

                if ($product['recurring']['duration']) {
                    $recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                } else {
                    $recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                }
            }

            $data['products'][] = array(
                'cart_id' => $product['cart_id'],
                'thumb' => $image,
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'recurring' => $recurring,
                'quantity' => $product['quantity'],
                'stock' => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
                'reward' => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
                'price' => $price,
                'total' => $total,
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }
        // End Cart


        // Guest
        $this->load->language('checkout/checkout');

        $data['customer_groups'] = array();

        if (isset($this->session->data['guest']['customer_group_id'])) {
            $data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
        } else {
            $data['customer_group_id'] = $this->config->get('config_customer_group_id');
        }

        if (isset($this->session->data['guest']['firstname'])) {
            $data['firstname'] = $this->session->data['guest']['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->session->data['guest']['lastname'])) {
            $data['lastname'] = $this->session->data['guest']['lastname'];
        } else {
            $data['lastname'] = '';
        }

        if (isset($this->session->data['guest']['email'])) {
            $data['email'] = $this->session->data['guest']['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->session->data['guest']['telephone'])) {
            $data['telephone'] = $this->session->data['guest']['telephone'];
        } else {
            $data['telephone'] = '';
        }

        if (isset($this->session->data['payment_address']['company'])) {
            $data['company'] = $this->session->data['payment_address']['company'];
        } else {
            $data['company'] = '';
        }

        if (isset($this->session->data['payment_address']['address_1'])) {
            $data['address_1'] = $this->session->data['payment_address']['address_1'];
        } else {
            $data['address_1'] = '';
        }

        if (isset($this->session->data['payment_address']['address_2'])) {
            $data['address_2'] = $this->session->data['payment_address']['address_2'];
        } else {
            $data['address_2'] = '';
        }

        if (isset($this->session->data['payment_address']['postcode'])) {
            $data['postcode'] = $this->session->data['payment_address']['postcode'];
        } elseif (isset($this->session->data['shipping_address']['postcode'])) {
            $data['postcode'] = $this->session->data['shipping_address']['postcode'];
        } else {
            $data['postcode'] = '';
        }

        if (isset($this->session->data['payment_address']['city'])) {
            $data['city'] = $this->session->data['payment_address']['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->session->data['payment_address']['country_id'])) {
            $data['country_id'] = $this->session->data['payment_address']['country_id'];
        } elseif (isset($this->session->data['shipping_address']['country_id'])) {
            $data['country_id'] = $this->session->data['shipping_address']['country_id'];
        } else {
            $data['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($this->session->data['payment_address']['zone_id'])) {
            $data['zone_id'] = $this->session->data['payment_address']['zone_id'];
        } elseif (isset($this->session->data['shipping_address']['zone_id'])) {
            $data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        // End Guest

        $data['shipping_required'] = $this->cart->hasShipping();

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');


//        $data['guest'] =  $this->load->controller('checkout/guest');
//        $data['guest'] =  $this->response->getOutput();

        $this->response->setOutput($this->load->view('checkout/ocs_guest', $data));
    }

    /**
     *
     */
    public function save()
    {
        $this->load->language('checkout/checkout');

        $json = array();


        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Check if guest checkout is available.
        if (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price') || $this->cart->hasDownload()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', true);
        }

        if (!$json) {
            if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
                $json['error']['firstname'] = $this->language->get('error_firstname');
            }

            if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
                $json['error']['lastname'] = $this->language->get('error_lastname');
            }

            if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
                $json['error']['email'] = $this->language->get('error_email');
            }

            if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
                $json['error']['telephone'] = $this->language->get('error_telephone');
            }

//			if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
//				$json['error']['address_1'] = $this->language->get('error_address_1');
//			}

//			if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
//				$json['error']['city'] = $this->language->get('error_city');
//			}

            $this->load->model('localisation/country');

//			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

//			if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
//				$json['error']['postcode'] = $this->language->get('error_postcode');
//			}

//			if ($this->request->post['country_id'] == '') {
//				$json['error']['country'] = $this->language->get('error_country');
//			}

//			if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
//				$json['error']['zone'] = $this->language->get('error_zone');
//			}


        }

        if (!$json) {

            // Shipping Methods
            $method_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('shipping');

            foreach ($results as $result) {

                if ($this->config->get('shipping_' . $result['code'] . '_status')) {
                    $this->load->model('extension/shipping/' . $result['code']);

                    $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                    if ($quote) {

                        $method_data[$result['code']] = array(
                            'title' => $quote['title'],
                            'quote' => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error' => $quote['error']
                        );
                    }
                }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $this->session->data['shipping_methods'] = $method_data;
            // End Shipping Methods

            $this->session->data['account'] = 'guest';

            $this->session->data['guest']['customer_group_id'] = 1;
            $this->session->data['guest']['firstname'] = $this->request->post['firstname'];
            $this->session->data['guest']['lastname'] = $this->request->post['lastname'];
            $this->session->data['guest']['email'] = $this->request->post['email'];
            $this->session->data['guest']['telephone'] = $this->request->post['telephone'];
            $this->session->data['guest']['custom_field'] = '';
            $this->session->data['comment'] = addslashes(strip_tags($this->request->post['comment']));

            $this->session->data['payment_address']['firstname'] = $this->request->post['firstname'];
            $this->session->data['payment_address']['lastname'] = $this->request->post['lastname'];
            $this->session->data['payment_address']['company'] = '';
            $this->session->data['payment_address']['address_1'] = '';
            $this->session->data['payment_address']['address_2'] = '';
            $this->session->data['payment_address']['postcode'] = '';
            $this->session->data['payment_address']['city'] = '';
            $this->session->data['payment_address']['country_id'] = 21;
            $this->session->data['payment_address']['zone_id'] = 344;

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry(21);

            if ($country_info) {
                $this->session->data['payment_address']['country'] = $country_info['name'];
                $this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['payment_address']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['payment_address']['country'] = '';
                $this->session->data['payment_address']['iso_code_2'] = '';
                $this->session->data['payment_address']['iso_code_3'] = '';
                $this->session->data['payment_address']['address_format'] = '';
            }

            if (isset($this->request->post['custom_field']['address'])) {
                $this->session->data['payment_address']['custom_field'] = $this->request->post['custom_field']['address'];
            } else {
                $this->session->data['payment_address']['custom_field'] = array();
            }

            $this->load->model('localisation/zone');

//			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);
//
//			if ($zone_info) {
//				$this->session->data['payment_address']['zone'] = $zone_info['name'];
//				$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
//			} else {
            $this->session->data['payment_address']['zone'] = '';
            $this->session->data['payment_address']['zone_code'] = '';
//			}

            if (!empty($this->request->post['shipping_address'])) {
                $this->session->data['guest']['shipping_address'] = $this->request->post['shipping_address'];
            } else {
                $this->session->data['guest']['shipping_address'] = false;
            }

            $this->load->model('extension/shipping/nova_poshta');

//            if ($this->session->data['guest']['shipping_address']) {
            $this->session->data['shipping_address']['firstname'] = $this->request->post['firstname'];
            $this->session->data['shipping_address']['lastname'] = $this->request->post['lastname'];
            $this->session->data['shipping_address']['company'] = '';


            if($this->request->post['shipping_method']=='nova_poshta'){
                $this->session->data['shipping_address']['address_1'] = $this->model_extension_shipping_nova_poshta->getWarehouse($this->request->post['np_warehouse'], 'description_ru');
                $this->session->data['shipping_address']['address_2'] = '';
            } else {
                $this->session->data['shipping_address']['address_1'] = $this->request->post['up_address'];
                $this->session->data['shipping_address']['address_2'] = $this->request->post['up_index'];
            }

            $this->session->data['shipping_address']['postcode'] = '';//$this->request->post['up_index'];
            $this->session->data['shipping_address']['city'] =$this->model_extension_shipping_nova_poshta->getCity($this->request->post['np_city'], 'description_ru');
            $this->session->data['shipping_address']['country_id'] = 21;
            $this->session->data['shipping_address']['zone_id'] = 344;
            $this->session->data['shipping_address']['zone'] = $this->model_extension_shipping_nova_poshta->getArea($this->request->post['np_area'], 'description_ru').' обл.';

            if ($country_info) {
                $this->session->data['shipping_address']['country'] = $country_info['name'];
                $this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['shipping_address']['country'] = '';
                $this->session->data['shipping_address']['iso_code_2'] = '';
                $this->session->data['shipping_address']['iso_code_3'] = '';
                $this->session->data['shipping_address']['address_format'] = '';
            }

//				if ($zone_info) {
//					$this->session->data['shipping_address']['zone'] = $zone_info['name'];
//					$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
//				} else {
//					$this->session->data['shipping_address']['zone'] = '';
//					$this->session->data['shipping_address']['zone_code'] = '';
//				}

            if (isset($this->request->post['custom_field']['address'])) {
                $this->session->data['shipping_address']['custom_field'] = $this->request->post['custom_field']['address'];
            } else {
                $this->session->data['shipping_address']['custom_field'] = array();
            }

//            }


//            unset($this->session->data['shipping_method']);
//            unset($this->session->data['shipping_methods']);
//            unset($this->session->data['payment_method']);
//            unset($this->session->data['payment_methods']);
        }

        //==============================//
        //=== route=checkout/confirm ===//
        //==============================//

        $order_data = array();

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes' => &$taxes,
            'total' => &$total
        );

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);

                // We have to put the totals in an array so that they pass by reference.
                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);

        $order_data['totals'] = $totals;

        $this->load->language('checkout/checkout');

        $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        $order_data['store_id'] = $this->config->get('config_store_id');
        $order_data['store_name'] = $this->config->get('config_name');

        if ($order_data['store_id']) {
            $order_data['store_url'] = $this->config->get('config_url');
        } else {
            if ($this->request->server['HTTPS']) {
                $order_data['store_url'] = HTTPS_SERVER;
            } else {
                $order_data['store_url'] = HTTP_SERVER;
            }
        }


        $order_data['customer_id'] = 0;
        $order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
        $order_data['firstname'] = $this->session->data['guest']['firstname'];
        $order_data['lastname'] = $this->session->data['guest']['lastname'];
        $order_data['email'] = $this->session->data['guest']['email'];
        $order_data['telephone'] = $this->session->data['guest']['telephone'];
        $order_data['custom_field'] = $this->session->data['guest']['custom_field'];

        $order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
        $order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
        $order_data['payment_company'] = $this->session->data['payment_address']['company'];
        $order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
        $order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
        $order_data['payment_city'] = $this->session->data['payment_address']['city'];
        $order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
        $order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
        $order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
        $order_data['payment_country'] = $this->session->data['payment_address']['country'];
        $order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
        $order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
        $order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

        if (isset($this->session->data['payment_method']['title'])) {
            $order_data['payment_method'] = $this->session->data['payment_method']['title'];
        } else {
            $order_data['payment_method'] = $this->request->post['payment_method']=='cod'?'Оплата при доставке':'На карту';
        }

        if (isset($this->session->data['payment_method']['code'])) {
            $order_data['payment_code'] = $this->session->data['payment_method']['code'];
        } else {
            $order_data['payment_code'] = $this->request->post['payment_method'];
        }

        $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
        $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
        $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
        $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
        $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
        $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
        $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
        $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
        $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
        $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
        $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
        $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
        $order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

        if (isset($this->session->data['shipping_method']['title'])) {
            $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
        } else {
            $order_data['shipping_method'] = '';
        }


        $order_data['shipping_code'] = $this->request->post['shipping_method'];
        $order_data['shipping_method'] = ($this->request->post['shipping_method']=='nova_poshta')?'Новая почта':'Укрпочта';


        $order_data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                $option_data[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
                    'name' => $option['name'],
                    'value' => $option['value'],
                    'type' => $option['type']
                );
            }

            $order_data['products'][] = array(
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'download' => $product['download'],
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => $product['price'],
                'total' => $product['total'],
                'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward' => $product['reward']
            );
        }

        // Gift Voucher
        $order_data['vouchers'] = array();

//        if (!empty($this->session->data['vouchers'])) {
//            foreach ($this->session->data['vouchers'] as $voucher) {
//                $order_data['vouchers'][] = array(
//                    'description' => $voucher['description'],
//                    'code' => token(10),
//                    'to_name' => $voucher['to_name'],
//                    'to_email' => $voucher['to_email'],
//                    'from_name' => $voucher['from_name'],
//                    'from_email' => $voucher['from_email'],
//                    'voucher_theme_id' => $voucher['voucher_theme_id'],
//                    'message' => $voucher['message'],
//                    'amount' => $voucher['amount']
//                );
//            }
//        }

        $order_data['comment'] = $this->session->data['comment'];
        $order_data['total'] = $total_data['total'];

        if (isset($this->request->cookie['tracking'])) {
            $order_data['tracking'] = $this->request->cookie['tracking'];

            $subtotal = $this->cart->getSubTotal();

            // Affiliate
            $affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

            if ($affiliate_info) {
                $order_data['affiliate_id'] = $affiliate_info['customer_id'];
                $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
            } else {
                $order_data['affiliate_id'] = 0;
                $order_data['commission'] = 0;
            }

            // Marketing
            $this->load->model('checkout/marketing');

            $marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

            if ($marketing_info) {
                $order_data['marketing_id'] = $marketing_info['marketing_id'];
            } else {
                $order_data['marketing_id'] = 0;
            }
        } else {
            $order_data['affiliate_id'] = 0;
            $order_data['commission'] = 0;
            $order_data['marketing_id'] = 0;
            $order_data['tracking'] = '';
        }

        $order_data['language_id'] = $this->config->get('config_language_id');

        $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
        $order_data['currency_code'] = $this->session->data['currency'];
        $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
        $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

        if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
            $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
            $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
        } else {
            $order_data['forwarded_ip'] = '';
        }

        if (isset($this->request->server['HTTP_USER_AGENT'])) {
            $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
        } else {
            $order_data['user_agent'] = '';
        }

        if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
            $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
        } else {
            $order_data['accept_language'] = '';
        }

        $this->load->model('checkout/order');

        $this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod_order_status_id'));

        $json['redirect'] = $this->url->link('checkout/success');

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    function getCities()
    {

        $area = $this->request->get['area'];

        $this->load->model('extension/shipping/nova_poshta');
        $result = $this->model_extension_shipping_nova_poshta->getCities($area);

        $this->response->setOutput(json_encode($result));
    }

    function getWarehouses()
    {
        $city = $this->request->get['city'];

        $this->load->model('extension/shipping/nova_poshta');
        $result = $this->model_extension_shipping_nova_poshta->getWarehouses($city);

        $this->response->setOutput(json_encode($result));
    }
}
