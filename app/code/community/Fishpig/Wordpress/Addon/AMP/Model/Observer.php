<?php
/**
 *
**/
class Fishpig_Wordpress_Addon_AMP_Model_Observer
{
	/**
	  *
	  * @param Varien_Event_Observer $observer
	  *
	  * @return Fishpig_Wordpress_Addon_AMP_Model_Observer
	  *
	 **/
	public function wordpressPostControllerPreDispatchAfterObserver(Varien_Event_Observer $observer)
	{
		if (1 !== (int)Mage::app()->getRequest()->getParam('amp')) {
			return $this;
		}
		
		if ('' !== Mage::app()->getRequest()->getParam('preview', '')) {
			return $this;
		}
		
		if (!$this->_isPluginEnabled()) {
			return $this;
		}

		try {
			$coreHelper = Mage::helper('wp_addon_amp/core');			
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e->getMessage());
		}
		
		return $this;
	}

	/**
	  *
	  * @param Varien_Event_Observer $observer
	  *
	  * @return Fishpig_Wordpress_Addon_AMP_Model_Observer
	  *
	 **/
	public function wordpressRenderLayoutBeforeObserver(Varien_Event_Observer $observer)
	{
		$object = $observer->getEvent()->getObject();
		
		if (!($object instanceof Fishpig_Wordpress_Model_Post)) {
			return $this;
		}
		
		if (($headBlock = Mage::app()->getLayout()->getBlock('head')) === false) {
			return $this;
		}
		
		if (!$this->_isPluginEnabled()) {
			return $this;
		}

		$headBlock->addItem('link_rel', $object->getPermalink() . '?amp=1', 'rel="amphtml"');
		
		return $this;
	}

	/**
	  *
	  * @param Varien_Event_Observer $observer
	  *
	  * @return Fishpig_Wordpress_Addon_AMP_Model_Observer
	  *
	 **/
	public function wordpressIntegrationTestsAfterObserver(Varien_Event_Observer $observer)
	{
		$observer->getEvent()->getHelper()->applyTest(array($this, 'checkForPlugin'));
		
		return $this;
	}

	/**
	  *
	  * @return bool
	  *
	 **/
	public function checkForPlugin()
	{
		if (!$this->_isPluginEnabled()) {
			throw Fishpig_Wordpress_Exception::error(
				'AMP', 
				sprintf('The free <a href="%s" target="_blank">AMP Plugin</a> is required. Please install in WordPress.', 'https://wordpress.org/plugins/amp/')
			);
		}
		
		return true;

	}

	/**
	  *
	  * @return bool
	  *
	 **/
	protected function _isPluginEnabled()
	{
		return Mage::helper('wordpress/plugin')->isEnabled('amp/amp.php');
	}
}
