<?php
/**
 *
 * @description Geo IP router
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 * @note Take into consideration that this geo IP redirection logic will be applied only to frontend requests because this router is only called for storefront requests (so this logic will not be applied to REST API requests or other type of requests)
 *
 */
namespace Bina\GeoIp\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Controller\Store\SwitchAction\CookieManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Bina\GeoIp\Api\SessionInterface;

class Router implements RouterInterface
{
    /**
     *
     * @var SessionInterface
     *
     */
    protected $_session;

    /**
     *
     * @var StoreManagerInterface
     *
     */
    protected $_storeManager;

    /**
     *
     * @var StoreRepositoryInterface
     *
     */
    protected $_storeRepository;

    /**
     *
     * @var CookieManager
     *
     */
    protected $_cookieManager;

    /**
     *
     * @var HttpResponse
     *
     */
    protected $_response;

    /**
     *
     * @var ActionFactory
     *
     */
    protected $_actionFactory;

    /**
     *
     * Constructor
     *
     * @param SessionInterface         $session
     * @param StoreManagerInterface    $storeManager
     * @param StoreRepositoryInterface $storeRepository
     * @param CookieManager            $cookieManager
     * @param ActionFactory            $actionFactory
     * @param HttpResponse             $response
     *
     */
    public function __construct(
        SessionInterface         $session,
        StoreManagerInterface    $storeManager,
        StoreRepositoryInterface $storeRepository,
        CookieManager            $cookieManager,
        ActionFactory            $actionFactory,
        HttpResponse             $response
    ) {
        /**
         *
         * @note Init geo IP session model
         *
         */
        $this->_session = $session;

        /**
         *
         * @note Init store manager
         *
         */
        $this->_storeManager = $storeManager;

        /**
         *
         * @note Init store repository
         *
         */
        $this->_storeRepository = $storeRepository;

        /**
         *
         * @note Set cookie manager
         *
         */
        $this->_cookieManager = $cookieManager;

        /**
         *
         * @note Init action factory
         *
         */
        $this->_actionFactory = $actionFactory;

        /**
         *
         * @note Init response
         *
         */
        $this->_response = $response;
    }

    /**
     *
     * Match
     *
     * @param RequestInterface|Http $request
     *
     * @return ActionInterface|null
     *
     */
    public function match(RequestInterface $request)
    {
        /**
         *
         * @note We are going to apply this redirection by IP logic only to GET requests (we could apply this logic to other request verbs, but we think that this feature is not necessary for them)
         *
         */
        if ($request->isGet()) {
            /**
             *
             * @note Get store ID related to user IP
             *
             */
            $storeId = $this->_session->getUserStoreFromIp();

            /**
             *
             * @note Check if current store is related to user IP store
             *
             */
            if (($storeId) && ($storeId !== $this->_storeManager->getStore()->getId())) {
                /**
                 *
                 * @note Get user IP store entity
                 *
                 */
                $store = $this->_storeRepository->getActiveStoreByCode($storeId);

                /**
                 *
                 * @note Set user IP store
                 *
                 */
                $this->_setCurrentStore($store);

                /**
                 *
                 * @note Redirect to user IP store
                 *
                 */
                return $this->_redirect($request, $store);
            }
        }

        /**
         *
         * @note Return NULL
         *
         */
        return null;
    }

    /**
     *
     * Set current store
     *
     * @param StoreInterface|Store $store
     *
     * @return void
     *
     */
    private function _setCurrentStore($store)
    {
        /**
         *
         * @note Set current store
         *
         */
        $this->_storeManager->setCurrentStore($store);

        /**
         *
         * @note Set cookie store
         *
         */
        $this->_cookieManager->setCookieForStore($store);
    }

    /**
     *
     * Redirect
     *
     * @param RequestInterface|Http $request
     * @param StoreInterface|Store  $store
     *
     * @return ActionInterface
     *
     */
    private function _redirect($request, $store)
    {
        /**
         *
         * @note Avoid store code in URL
         *
         */
        $store->setData('has_disable_store_in_url', true);

        /**
         *
         * @note Set redirect URL
         *
         */
        $this->_response->setRedirect($store->getCurrentUrl(false));

        /**
         *
         * @note Set request as dispatched
         *
         */
        $request->setDispatched(true);

        /**
         *
         * @note Return redirect action
         *
         */
        return $this->_actionFactory->create(Redirect::class);
    }
}
