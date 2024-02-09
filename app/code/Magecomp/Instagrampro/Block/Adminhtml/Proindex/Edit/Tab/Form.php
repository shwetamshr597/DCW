<?php
namespace Magecomp\Instagrampro\Block\Adminhtml\Proindex\Edit\Tab;

use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;

class Form extends Generic implements TabInterface
{

     protected $_systemStore;
    protected $_wysiwygConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('instagrampro_data');
        if ($this->_isAllowedAction('Magecomp_Instagrampro::instagrampro')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Image Information')]);
        
        if ($model->getId()) {
            $fieldset->addField('image_id', 'hidden', ['name' => 'image_id']);
        }
        
        $fieldset->addType(
            'thumbnail',
            '\Magecomp\Instagrampro\Block\Adminhtml\Proindex\Edit\Renderer\Thumbnail'
        );

        
        $fieldset->addField(
            'thumbnail_url',
            'thumbnail',
            [
                     'name'  => 'thumbnail_url',
                    'label' => __('Image Thumbnail'),
                    'title' => __('Image Thumbnail'),
            ]
        );
        
        $fieldset->addField(
            'image_link',
            'text',
            [
                    'label' => 'Link URL',
                    'required' => false,
                    'class'     => 'validate-url',//'validate-clean-url',
                    'name' => 'image_link',
                    'after_element_html' => '<small>Enter link URL to redirect users on click of image.</small>',
                           
            ]
        );
            
        $fieldset->addField(
            'image_title',
            'text',
            [
                    'label' => 'Title',
                    'required' => false,
                    'name' => 'image_title',
                    'after_element_html' => '<small>Enter image title to show on frontend.</small>',
                           
            ]
        );
                
        $fieldset->addField('image_desc', 'editor', [
              'name'      => 'image_desc',
              'label'       => 'Description',
              'style'     => 'height:15em',
              'config'    => $this->_wysiwygConfig->getConfig(),
              'wysiwyg'   => true,
              'required'  => false,
              'after_element_html' => '<small>Enter image description to show on frontend.</small>',
        ]);

        $fieldset = $form->addFieldset('base_fieldset_title1', ['legend' => __('Hotspot 1')]);
        $fieldset->addField(
            'title1',
            'text',
            [
                          'label' => 'Title',
                          'title' => __('Title'),
                          'required' => false,
                          'name' => 'title1',
            ]
        );
        
        $fieldset->addField(
            'titlelink1',
            'text',
            [
                          'label' => 'Link URL',
                          'title' => __('Link URL'),
                          'required' => false,
                          'class'     => 'validate-url',
                          'name' => 'titlelink1',
            ]
        );
        
        $fieldset->addField(
            'title1x',
            'text',
            [
                          'label' => 'Position-TOP',
                          'title' => __('Position-TOP'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title1x',
            ]
        );
        
        $fieldset->addField(
            'title1y',
            'text',
            [
                          'label' => 'Position-LEFT',
                          'title' => __('Position-LEFT'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title1y',
            ]
        );
        
        $fieldset->addField(
            'product_id_1',
            'text',
            [
                          'label' => 'Product Id',
                          'title' => __('Product Id'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'product_id_1',
            ]
        );
        $fieldset = $form->addFieldset('base_fieldset_title2', ['legend' => __('Hotspot 2')]);
        $fieldset->addField(
            'title2',
            'text',
            [
                          'label' => 'Title',
                          'required' => false,
                          'name' => 'title2',
            ]
        );
        
        $fieldset->addField(
            'titlelink2',
            'text',
            [
                          'label' => 'Link URL',
                          'required' => false,
                          'class'     => 'validate-url',
                          'name' => 'titlelink2',
            ]
        );
        
        $fieldset->addField(
            'title2x',
            'text',
            [
                          'label' => 'Position-TOP',
                          'title' => __('Position-TOP'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title2x',
            ]
        );
        
        $fieldset->addField(
            'title2y',
            'text',
            [
                          'label' => 'Position-LEFT',
                          'title' => __('Position-LEFT'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title2y',
            ]
        );
        
        $fieldset->addField(
            'product_id_2',
            'text',
            [
                          'label' => 'Product Id',
                          'title' => __('Product Id'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'product_id_2',
            ]
        );

        $fieldset = $form->addFieldset('base_fieldset_title3', ['legend' => __('Hotspot 3')]);

        $fieldset->addField(
            'title3',
            'text',
            [
                          'label' => 'Title',
                          'required' => false,
                          'name' => 'title3',
            ]
        );
        
        $fieldset->addField(
            'titlelink3',
            'text',
            [
                          'label' => 'Link URL',
                          'required' => false,
                          'class'     => 'validate-url',
                          'name' => 'titlelink3',
            ]
        );
        
        $fieldset->addField(
            'title3x',
            'text',
            [
                          'label' => 'Position-TOP',
                          'title' => __('Position-TOP'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title3x',
            ]
        );
        
        $fieldset->addField(
            'title3y',
            'text',
            [
                          'label' => 'Position-LEFT',
                          'title' => __('Position-LEFT'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title3y',
            ]
        );
        
        $fieldset->addField(
            'product_id_3',
            'text',
            [
                          'label' => 'Product Id',
                          'title' => __('Product Id'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'product_id_3',
            ]
        );
        $fieldset = $form->addFieldset('base_fieldset_title4', ['legend' => __('Hotspot 4')]);
        $fieldset->addField(
            'title4',
            'text',
            [
                          'label' => 'Title',
                          'required' => false,
                          'name' => 'title4',
            ]
        );
        
        $fieldset->addField(
            'titlelink4',
            'text',
            [
                          'label' => 'Link URL',
                          'required' => false,
                          'class'     => 'validate-url',
                          'name' => 'titlelink4',
            ]
        );
        
        $fieldset->addField(
            'title4x',
            'text',
            [
                          'label' => 'Position-TOP',
                          'title' => __('Position-TOP'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title4x',
            ]
        );
        
        $fieldset->addField(
            'title4y',
            'text',
            [
                          'label' => 'Position-LEFT',
                          'title' => __('Position-LEFT'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title4y',
            ]
        );
        
        $fieldset->addField(
            'product_id_4',
            'text',
            [
                          'label' => 'Product Id',
                          'title' => __('Product Id'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'product_id_4',
            ]
        );
        $fieldset = $form->addFieldset('base_fieldset_title5', ['legend' => __('Hotspot 5')]);
        $fieldset->addField(
            'title5',
            'text',
            [
                          'label' => 'Title 5',
                          'required' => false,
                          'name' => 'title5',
            ]
        );
        
        $fieldset->addField(
            'titlelink5',
            'text',
            [
                          'label' => 'Link URL',
                          'required' => false,
                          'class'     => 'validate-url',
                          'name' => 'titlelink5',
            ]
        );
        
        $fieldset->addField(
            'title5x',
            'text',
            [
                          'label' => 'Position-TOP',
                          'title' => __('Position-TOP'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title5x',
            ]
        );
        
        $fieldset->addField(
            'title5y',
            'text',
            [
                          'label' => 'Position-LEFT',
                          'title' => __('Position-LEFT'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title5y',
            ]
        );
        
        $fieldset->addField(
            'product_id_5',
            'text',
            [
                          'label' => 'Product Id',
                          'title' => __('Product Id'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'product_id_5',
            ]
        );
        $fieldset = $form->addFieldset('base_fieldset_title6', ['legend' => __('Hotspot 6')]);
        $fieldset->addField(
            'title6',
            'text',
            [
                          'label' => 'Title',
                          'required' => false,
                          'name' => 'title6',
            ]
        );
        
        $fieldset->addField(
            'titlelink6',
            'text',
            [
                          'label' => 'Link URL',
                          'required' => false,
                          'class'     => 'validate-url',
                          'name' => 'titlelink6',
            ]
        );
        
        $fieldset->addField(
            'title6x',
            'text',
            [
                          'label' => 'Position-TOP',
                          'title' => __('Position-TOP'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title6x',
            ]
        );
        
        $fieldset->addField(
            'title6y',
            'text',
            [
                          'label' => 'Position-LEFT',
                          'title' => __('Position-LEFT'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'title6y',
            ]
        );
        
        $fieldset->addField(
            'product_id_6',
            'text',
            [
                          'label' => 'Product Id',
                          'title' => __('Product Id'),
                          'required' => false,
                          'class'     => 'validate-number',
                          'name' => 'product_id_6',
            ]
        );
        
        $fieldset->addField('note', 'note', [
          'text'     => __('These titles will be displayed in popup on Instagram page. Click of a particular title will redirect users to given link URL.'),
        ]);
        
        $this->_eventManager->dispatch('instagrampro_proindex_edit_tab_form_prepare_form', ['form' => $form]);
        $form->setValues($model->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return __('Image Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Image Settings');
    }

    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
