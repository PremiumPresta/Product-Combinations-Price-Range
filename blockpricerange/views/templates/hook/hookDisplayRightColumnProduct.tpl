{*
* BlockPriceRange Module
* 
*  @author    PremiumPresta <premiumpresta@gmail.com>
*  @copyright 2014 PremiumPresta
*  @license   http://creativecommons.org/licenses/by-nd/4.0/ CC BY-ND 4.0
*}

<div id="blockpricerange" class="block">
    {l s='Prices range from ' mod='blockpricerange'}{convertPrice price=$product_price_min|floatval} {l s='to ' mod='blockpricerange'}{convertPrice price=$product_price_max|floatval}
</div>