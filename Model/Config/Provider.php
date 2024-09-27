<?php
/**
 * Copyright (c) Bluethink IT Consulting Pvt. Ltd.
 */
declare(strict_types=1);

namespace Bluethinkinc\Seo\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Provider to fetch config value
 */
class Provider
{
    /**
     * @var
     */
    private const XML_PATH_ENABLE_FOR_PRODUCT_PAGE = 'seo/hreflang_config/enable_product_page';

    /**
     * @var
     */
    private const XML_PATH_ENABLE_FOR_CATEGORY_PAGE = 'seo/hreflang_config/enable_category_page';

    /**
     * @var
     */
    private const XML_PATH_ENABLE_FOR_CMS_PAGE = 'seo/hreflang_config/enable_cms_page';

    /**
     * @var
     */
    private const XML_PATH_ENABLE_X_DEFAULT = 'seo/hreflang_config/enable_x_default';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * Provider Constructor
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Check Enable For Product Page Status from configuration
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkEnableForProductPage(): bool
    {
        return $this->getStatus(self::XML_PATH_ENABLE_FOR_PRODUCT_PAGE);
    }

    /**
     * Check Enable For Category Page Status from configuration
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkEnableForCategoryPage(): bool
    {
        return $this->getStatus(self::XML_PATH_ENABLE_FOR_CATEGORY_PAGE);
    }

    /**
     * Check Enable For CMS Page Status from configuration
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkEnableForCmsPage(): bool
    {
        return $this->getStatus(self::XML_PATH_ENABLE_FOR_CMS_PAGE);
    }

    /**
     * Check Enable X-Default Status from configuration
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkEnableXDefault(): bool
    {
        return $this->getStatus(self::XML_PATH_ENABLE_X_DEFAULT);
    }

    /**
     * Get Status from configuration
     *
     * @param  string $configPath
     * @return bool
     * @throws NoSuchEntityException
     */
    public function getStatus(string $configPath): bool
    {
        return $this->scopeConfig->isSetFlag(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }
}
