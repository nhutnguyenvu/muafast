<?php

class TTS_OnePageCheckout_Block_About
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = <<<HTML
<div style="background:url('http://www.ttstech.com/media/logo.gif') no-repeat scroll 14px 14px #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 164px;">
    <p>
        <b style="font-size:12px;">TTS Tech</b>, a family of niche sites, provides small businesses with everything they need to start selling online.
    </p>
    <p>
        <strong>PREMIUM and FREE MAGENTO TEMPALTES and EXTENSIONS</strong><br />
        <a href="http://www.magentovietnam.com" target="_blank">Magento Viet Nam</a> offers a wide choice of nice-looking and easily editable free and premium Magento Themes. At Magento Viet Nam, you can find free downloads or buy premium tempaltes for the extremely popular Magento eCommerce platform.<br />
        <strong>MAGENTO Solution</strong></strong><br />
        <a href="http://www.ecomsluting.com" target="_blank">Ecom solution</a>, a new and improved hosting solution, is allowing you to easily create, promote, and manage your online store with Magento. Magenting users will receive a valuable set of tools and features, including automatic Magento eCommerce installation, automatic Magento template installation and a free or paid professional Magento hosting account.<br />
        <strong>WEB DEVELOPMENT</strong><br />
        <a href="http://www.ttstech.com" target="_blank">TTS Tech</a> is a team of professional Web developers and designers who are some of the best in the industry. TTS Tech provides Web application development, custom Magento theme designs, and Website design services.<br />
        <br />
    </p>
</div>
HTML;
        return $html;
    }
}
