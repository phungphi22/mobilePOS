<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class DtMobilePos extends PaymentModule
{
	const ADMIN_TAB_CLASS = 'AdminDtMobilePos';
	const ADMIN_TAB_NAME  = 'Mobile POS';
    public function __construct()
    {
        $this->name = 'dtmobilepos';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'DT';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('DT Mobile POS');
        $this->description = $this->l('Mobile POS for PrestaShop 1.6');
        $this->bootstrap = true;

        $this->ps_versions_compliancy = array(
            'min' => '1.6.0.0',
            'max' => _PS_VERSION_
        );
    }

   public function install()
{
    return parent::install()
        && $this->installTab();
}

   public function uninstall()
{
    return $this->uninstallTab()
        && parent::uninstall();
}
protected function installTab()
{
    $tab = new Tab();

    $tab->active = 1;
    $tab->class_name = self::ADMIN_TAB_CLASS;
    $tab->module = $this->name;

    // Bán hàng (Orders)
    $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentOrders');

    foreach (Language::getLanguages(true) as $lang) {
        $tab->name[$lang['id_lang']] = self::ADMIN_TAB_NAME;
    }

    return $tab->add();
}
protected function uninstallTab()
{
    $id_tab = (int)Tab::getIdFromClassName(self::ADMIN_TAB_CLASS);

    if ($id_tab) {
        $tab = new Tab($id_tab);
        return $tab->delete();
    }

    return true;
}
}