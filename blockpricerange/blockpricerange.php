<?php
/**
 * BlockPriceRange Module
 * 
 *  @author    PremiumPresta <premiumpresta@gmail.com>
 *  @copyright 2014 PremiumPresta
 *  @license   http://creativecommons.org/licenses/by-nd/4.0/ CC BY-ND 4.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class BlockPriceRange extends Module
{

    public function __construct()
    {
        $this->name = 'blockpricerange'; // internal identifier, unique and lowercase
        $this->tab = 'front_office_features'; // backend module coresponding category
        $this->version = '1.0.0'; // version number for the module
        $this->author = 'PremiumPresta'; // module author
        $this->need_instance = 0; // load the module when displaying the "Modules" page in backend
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Block Price Range'); // public name
        $this->description = $this->l('Displays the min and max price for a product with combinations'); // public description

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?'); // confirmation message at uninstall

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Install this module
     * @return boolean
     */
    public function install()
    {
        return parent::install() &&
                $this->registerHook('displayRightColumnProduct') &&
                $this->registerHook('displayProductListReviews');
    }

    /**
     * Uninstall this module
     * @return boolean
     */
    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Product page content hook (Technical name: hookDisplayRightColumnProduct)
     */
    public function hookDisplayRightColumnProduct($params)
    {
        $id_product = (int) Tools::getValue('id_product');
        $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);

        /**
         * If product has combinations
         * get the prices for each
         */
        if (!empty($product->getAttributeCombinations())) {
            $params['tpl'] = 'hookDisplayRightColumnProduct';
            $params['prod_obj'] = $product;
            return $this->getAttributePrices($params);
        }

        return;
    }

    /**
     * Product list page content hook (Technical name: hookDisplayProductListReviews)
     */
    public function hookDisplayProductListReviews($params)
    {
        $id_product = $params['product']['id_product'];
        $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);

        /**
         * If product has combinations
         * get the prices for each
         */
        if (!empty($product->getAttributeCombinations())) {
            $params['tpl'] = 'hookDisplayProductListReviews';
            $params['prod_obj'] = $product;
            return $this->getAttributePrices($params);
        }

        return;
    }

    private function getAttributePrices($params)
    {
        $product = $params['prod_obj'];
        $product_attrbiute_ids = array();
        foreach ($product->getAttributeCombinations() as $product_attribute_id) {
            array_push($product_attrbiute_ids, $product_attribute_id['id_product_attribute']);
        }

        $prices = array();
        foreach ($product_attrbiute_ids as $prod_attr_id) {
            array_push($prices, $product->getPrice($tax = true, $prod_attr_id));
        }
        sort($prices);

        $min_price = $prices[0];
        $max_price = end($prices);

        /**
         * if the prices differ display the price range
         */
        if ($min_price != $max_price) {
            $this->context->smarty->assign(array(
                'product_price_min' => $min_price,
                'product_price_max' => $max_price
            ));

            !isset($params['tpl']) && $params['tpl'] = 'hookDisplayProductListReviews';
            return $this->display(__FILE__, $params['tpl'] . '.tpl');
        }
    }
}
