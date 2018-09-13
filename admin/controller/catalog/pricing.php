<?php

class ControllerCatalogPricing extends Controller
{
    private $error = array();

    public function index()
    {

        $this->load->language('catalog/product');
        $this->load->model('catalog/pricing');

        $this->document->setTitle('Ценообразование');

        $data['rows'] = $this->model_catalog_pricing->getList();

        foreach ($data['rows'] as &$row){
            $row['edit']=$this->url->link('catalog/pricing/edit', 'user_token=' . $this->session->data['user_token']. '&pricing_id=' . $row['id'], true);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['add'] = $this->url->link('catalog/pricing/add', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $this->response->setOutput($this->load->view('catalog/pricing_list', $data));

    }

    public function add()
    {

        $this->load->model('catalog/pricing');

        if (!empty($this->request->post)) {

            $data=$this->request->post;
            $this->model_catalog_pricing->addPricing($data);

            $this->response->redirect($this->url->link('catalog/pricing', 'user_token=' . $this->session->data['user_token'], true));

        }

        $this->load->language('catalog/product');

        $this->document->setTitle('Ценообразование');

        $data['rows'] = $this->model_catalog_pricing->getList();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['action'] = $this->url->link('catalog/pricing/add', 'user_token=' . $this->session->data['user_token'], true);

        $data['title']='';
        $data['type']='';
        $data['value']='';

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $this->response->setOutput($this->load->view('catalog/pricing_form', $data));
    }

    public function edit()
    {
        $this->load->model('catalog/pricing');

        if (!empty($this->request->post)) {
            $this->model_catalog_pricing->editPricing($this->request->post);
            $this->response->redirect($this->url->link('catalog/pricing', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->load->language('catalog/product');

        $this->document->setTitle('Ценообразование');


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['action'] = $this->url->link('catalog/pricing/edit', 'user_token=' . $this->session->data['user_token'], true);

        $result = $this->model_catalog_pricing->getPricing($this->request->get['pricing_id']);

        $data['pricing_id']=$result['id'];
        $data['title']=$result['title'];
        $data['type']=$result['type'];
        $data['value']=$result['value'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $this->response->setOutput($this->load->view('catalog/pricing_form', $data));
    }
}
