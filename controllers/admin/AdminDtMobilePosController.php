<?php

class AdminDtMobilePosController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'ajax_url' => self::$currentIndex.'&token='.$this->token.'&ajax=1'
        ));

        $this->content = $this->module->display(
            $this->module->getLocalPath(),
            'views/templates/admin/quickorder.tpl'
        );

        $this->context->smarty->assign(array(
            'content' => $this->content
        ));
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS(
            $this->module->getPathUri().'views/css/quickorder.css'
        );

        $this->addJS(
            $this->module->getPathUri().'views/js/quickorder.js'
        );
    }

    protected function loadBusiness()
    {
        require_once dirname(dirname(dirname(__FILE__))).'/classes/loader.php';
    }

    protected function jsonResult(QuickResult $result)
    {
        die(Tools::jsonEncode(array(
            'success' => $result->isSuccess(),
            'message' => $result->getMessage(),
            'data'    => $result->getData()
        )));
    }

    public function ajaxProcessSearchProduct()
    {
        $this->loadBusiness();

        $result = QuickAction::searchProduct(
            Tools::getValue('keyword')
        );

        if ($result->isSuccess()) {
            $rows = array();

            foreach ($result->getData() as $p) {

                $rows[] = array(
                    'id_product' => $p->getIdProduct(),
                    'reference'  => $p->getReference(),
                    'ean13'      => $p->getEan13(),
                    'name'       => $p->getName(),
                    'price'      => $p->getPrice(),
                    'quantity'   => $p->getQuantity(),
                    'image'      => $p->getImage()
                );
            }

            $result = QuickResult::success($rows);
        }

        $this->jsonResult($result);
    }

    public function ajaxProcessCreateCart()
    {
        $this->loadBusiness();

        $this->jsonResult(
            QuickAction::createCart()
        );
    }

    public function ajaxProcessAddProduct()
    {
        $this->loadBusiness();

        $this->jsonResult(
            QuickAction::addProduct(
                (int)Tools::getValue('id_cart'),
                (int)Tools::getValue('id_product'),
                (int)Tools::getValue('quantity',1)
            )
        );
    }

	/* public function ajaxProcessCreateCart()
	{
		$result = QuickAction::createCart();

		QuickResponse::json($result);
	} */

    public function ajaxProcessSummary()
    {
        $this->loadBusiness();

        $this->jsonResult(
            QuickAction::summary(
                (int)Tools::getValue('id_cart')
            )
        );
    }

    public function ajaxProcessUpdateQuantity()
    {
        $this->loadBusiness();

        $this->jsonResult(
            QuickAction::updateQuantity(
                (int)Tools::getValue('id_cart'),
                (int)Tools::getValue('id_product'),
                (int)Tools::getValue('quantity')
            )
        );
    }

    public function ajaxProcessRemoveProduct()
    {
        $this->loadBusiness();

        $this->jsonResult(
            QuickAction::removeProduct(
                (int)Tools::getValue('id_cart'),
                (int)Tools::getValue('id_product')
            )
        );
    }

    public function ajaxProcessCreateOrder()
    {
        $this->loadBusiness();

        $this->jsonResult(
            QuickAction::createOrder(
                (int)Tools::getValue('id_cart'),
                Tools::getValue('apartment')
            )
        );
    }
}