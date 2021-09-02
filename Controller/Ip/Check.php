<?php
/**
 *
 * @description Geo IP check controller action
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Controller\Ip;

use Exception;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Bina\GeoIp\Api\IpManagementInterface;

class Check extends Action implements HttpGetActionInterface
{
    /**
     *
     * @var IpManagementInterface
     *
     */
    protected $_ipManagement;

    /**
     *
     * @var StoreManagerInterface
     *
     */
    protected $_storeManager;

    /**
     *
     * @var StoreResolverInterface
     *
     */
    protected $_storeResolver;

    /**
     *
     * @var EncoderInterface
     *
     */
    protected $_encoder;

    /**
     *
     * Constructor
     *
     * @param IpManagementInterface  $ipManagement
     * @param StoreManagerInterface  $storeManager
     * @param StoreResolverInterface $storeResolver
     * @param EncoderInterface       $encoder
     * @param Context                $context
     *
     */
    public function __construct(
        IpManagementInterface  $ipManagement,
        StoreManagerInterface  $storeManager,
        StoreResolverInterface $storeResolver,
        EncoderInterface       $encoder,
        Context                $context
    ) {
        /**
         *
         * @note Init IP management
         *
         */
        $this->_ipManagement = $ipManagement;

        /**
         *
         * @note Init store manager
         *
         */
        $this->_storeManager = $storeManager;

        /**
         *
         * @note Init store resolver
         *
         */
        $this->_storeResolver = $storeResolver;

        /**
         *
         * @note Init encoder
         *
         */
        $this->_encoder = $encoder;

        /**
         *
         * @note Parent constructor
         *
         */
        parent::__construct($context);
    }

    /**
     *
     * Execute
     *
     * @return Json
     *
     */
    public function execute()
    {
        /**
         *
         * @note Init result
         *
         */
        $result = [];

        /**
         *
         * @note Try
         *
         */
        try {
            /**
             *
             * @note Get request
             *
             */
            /** @var HttpRequest $request */
            $request = $this->getRequest();

            /**
             *
             * @note Validate if it is an AJAX request
             *
             */
            if (!$request->isAjax()) {
                /**
                 *
                 * @note Throw exception
                 *
                 */
                throw new Exception(__('Invalid request.'));
            }

            /**
             *
             * @note Check store ID
             * @note Check if store ID is different from current store ID to allow redirection
             *
             */
            if (($storeId = $this->_ipManagement->checkRelatedStoreId()) && ($storeId != $this->_storeResolver->getCurrentStoreId())) {
                /**
                 *
                 * @note Add URL to switch store
                 *
                 */
                $result['url'] = $this->_getSwitchStoreUrl($storeId);
            }
        }
        catch (Exception $e) {
            /**
             *
             * @note Init result with error data
             *
             */
            $result = [
                'error'     => $e->getMessage(),
                'errorcode' => $e->getCode(),
            ];
        }

        /**
         *
         * @note Send response
         *
         */
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }

    /**
     *
     * Get switch store URL
     *
     * @param int $storeId
     *
     * @return string
     *
     */
    private function _getSwitchStoreUrl($storeId)
    {
        /**
         *
         * @note Get store
         *
         */
        /** @var StoreInterface|Store $store */
        $store = $this->_storeManager->getStore($storeId);

        /**
         *
         * @note Return switch store URL
         *
         */
        return $this->_url->getUrl(
            'stores/store/redirect',
            [
                StoreResolverInterface::PARAM_NAME      => $store->getCode(),
                '___from_store'                         => $this->_storeManager->getStore()->getCode(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->_encoder->encode($this->getRequest()->getParam('current_url'))
            ]
        );
    }
}
