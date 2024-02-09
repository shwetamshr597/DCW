<?php

namespace Magecomp\Instagrampro\Controller;

use Magecomp\Instagrampro\Helper\Data;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{
    protected $actionFactory;
    protected $helper;

    public function __construct(
        ActionFactory $actionFactory,
        Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $pathInfo = trim($request->getPathInfo(), '/');
        $pathParts = explode('/', $pathInfo);

        if ($pathParts[0] !== $this->helper->getSeoFriendlyUrl()) {
            return null;
        }

        $request->setModuleName('instagrampro')
            ->setControllerName('gallery')
            ->setActionName('instalist');

        $request->setAlias(
            \Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS,
            $pathInfo
        );

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
