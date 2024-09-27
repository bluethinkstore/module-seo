<?php
/**
 * Copyright (c) Bluethink IT Consulting Pvt. Ltd.
 */
declare(strict_types=1);

namespace Bluethinkinc\Seo\ViewModel\Html\Header;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Cms\Block\Page as CmsPage;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Bluethinkinc\Seo\Model\Config\Provider as ConfigProvider;
use Magento\Framework\UrlInterface;
use Magento\Cms\Model\Page;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;

class HreflangData implements ArgumentInterface
{
    /**
     * @var Http
     */
    private Http $request;

    /**
     * @var CmsPage
     */
    private CmsPage $cmsPage;

    /**
     * @var CatalogHelper
     */
    private CatalogHelper $catalogHelper;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProvider;

    /**
     * HreflangData constructor.
     *
     * @param Http                  $request
     * @param CmsPage               $cmsPage
     * @param CatalogHelper         $catalogHelper
     * @param StoreManagerInterface $storeManager
     * @param ConfigProvider        $configProvider
     */
    public function __construct(
        Http $request,
        CmsPage $cmsPage,
        CatalogHelper $catalogHelper,
        StoreManagerInterface $storeManager,
        ConfigProvider $configProvider
    ) {
        $this->request = $request;
        $this->cmsPage = $cmsPage;
        $this->catalogHelper = $catalogHelper;
        $this->storeManager = $storeManager;
        $this->configProvider = $configProvider;
    }

    /**
     * Get hreflang links
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getLinks(): array
    { 
        if ($this->configProvider->checkEnableForProductPage()
            && $this->getCurrentPageType() == 'catalog_product_view'
        ) {
            return $this->getData('product');
        } elseif ($this->configProvider->checkEnableForCategoryPage()
            && $this->getCurrentPageType() == 'catalog_category_view'
        ) {
            return $this->getData('category');
        } elseif ($this->configProvider->checkEnableForCmsPage()
            && $this->cmsPage->getPage()->getId()
        ) {
            return $this->getData('cms');
        }

        return [];
    }

    /**
     * Check if X Default is enabled
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnableXDefault(): bool
    {
        if ($this->configProvider->checkEnableXDefault()
            && $this->getCurrentPageType() == 'cms_index_index'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get data for links
     *
     * @param  string $pageType
     * @return array
     * @throws NoSuchEntityException
     */
    private function getData(string $pageType): array
    {
        if ($pageType == 'product') {
            return $this->getLinksArray($this->catalogHelper->getProduct());
        } elseif ($pageType == 'category') {
            return $this->getLinksArray($this->catalogHelper->getCategory());
        } elseif ($pageType == 'cms') {
            return $this->getLinksArray($this->cmsPage->getPage());
        }

        return [];
    }

    /**
     * Get current page type
     *
     * @return string
     */
    private function getCurrentPageType(): string
    {
        return $this->request->getFullActionName();
    }

    /**
     * Get hreflang link array
     *
     * @param Category|Page|Product $entityType
     * @return array
     * @throws NoSuchEntityException
     */
    private function getLinksArray(Category|Page|Product $entityType): array
    {
        foreach ($this->storeManager->getStores() as $store) {
            $links[] = [
                'code' => $store->getCode(),
                'url' => $this->getUrl($store, $entityType)
            ];
        }

        return $links ?? [];
    }

    /**
     * Get URL for hreflang link
     *
     * @param  Store $store
     * @param  Category|Page|Product $entityType
     * @return string
     */
    private function getUrl(Store $store, Category|Page|Product $entityType): string
    {
        if (!$this->cmsPage->getPage()->getId()) {
            return $store->getBaseUrl() . $entityType->getUrlKey() . '.html';
        } else {
            if ($this->request->getFullActionName() == 'cms_index_index') {
                return $store->getBaseUrl();
            }

            return $store->getBaseUrl() . $entityType->getIdentifier();
        }
    }

    /**
     * Get base URL without store code
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrlWithoutCode(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_WEB
        );
    }
}
