<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog Categories
 */
namespace Dcw\AllCategories\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class CategoryList extends Template
{
    
    /**
     * @var CollectionFactory
     */
    protected $_categoryCollectionFactory;
    
    /**
     * @var \Magento\Framework\Registr
     */
    protected $_registry;
    
    /**
     * @var CategoryRepository
     */
    protected $_categoryRepository;
    
    /**
     * @param Template\Context $context
     * @param  CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepository $categoryRepository
     * @param array $data
     */
 
    public function __construct(
        Template\Context $context,
        CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\View\Asset\Repository $assetRepos,
        \Magento\Catalog\Helper\ImageFactory $helperImageFactory,
        \Magento\Framework\Registry $registry,
        CategoryRepository $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_registry = $registry;
        $this->_categoryRepository = $categoryRepository;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
    }
    
    /**
     * Child Category Collection
     *
     * @return CategoryRepository
     */
    public function getLabelChildCategories()
    {
        $currentCategory = $this->getCurrentCategory();
        if (!$currentCategory) {
            return [];
        }
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('parent_id', $currentCategory->getId());
        $collection->addAttributeToFilter('is_active', true);
        $collection->addAttributeToFilter('include_in_menu', true);
        return $collection;
    }

     /**
     * Get place holder image of a product for small_image
     *
     * @return string
     */
    public function getPlaceHolderImage()
    {
        $imagePlaceholder = $this->helperImageFactory->create();
        return $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
    }
    
    /**
     * Return current category
     *
     * @return \Magento\Framework\Registr
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }
    
    /**
     * Return current category
     *
     * @param string|bool|int $categoryId
     * @return CategoryRepository
     */
    public function getCategoryById($categoryId)
    {
        try {
            $category = $this->_categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            $category = null;
        }
        return $category;
    }
    
    /**
     * Return category Url
     *
     * @param string|bool|int $categoryId
     * @return string
     */
    public function getCategoryUrl($categoryId)
    {
        try {
            $category = $this->_categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            $category = null;
        }
        return $category ? $category->getUrl() : '';
    }
    
    /**
     * Return category Image
     *
     * @param string|bool|int $categoryId
     * @return string
     */
    public function getCategoryImageUrl($categoryId)
    {
        try {
            $category = $this->_categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            $category = null;
        }
        return $category && $category->getImageUrl() ?
        $category->getImageUrl() :
        $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/small_image.jpg');
    }
}
