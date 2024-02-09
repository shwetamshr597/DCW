<?php
namespace Dcw\CircularDesignerTool\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Dcw\CircularDesignerTool\Model\CircularDesignerToolFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Controller\ResultFactory;

class Quote extends \Magento\Framework\App\Action\Action
{
    
    protected $_modelCircularDesignerToolFactory;
    
    public function __construct(
        Context $context,
        CircularDesignerToolFactory $modelCircularDesignerToolFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $reader,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Message\ManagerInterface $manager,
        //\Magento\Framework\Url\EncoderInterface $urlEncoder,
        //\Magento\Framework\Url\DecoderInterface $urlDecoder,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        parent::__construct($context);
        $this->_modelCircularDesignerToolFactory = $modelCircularDesignerToolFactory;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->directoryList = $directoryList;
        $this->reader = $reader;
        $this->scopeConfig = $scopeConfig;
        $this->file = $file;
        $this->_json = $json;
        $this->manager = $manager;
        $this->resource = $resource;
        //$this->urlEncoder = $urlEncoder;
        //$this->urlDecoder = $urlDecoder;
        $this->connection = $resource->getConnection();
    }
    
    public function execute()
    {
        $postData = $this->getRequest()->getPost();

        //echo '<pre>';
        //print_r($postData);
        //die();

        $circular_designer_tool_table = $this->connection->getTableName("circular_designer_tool");
        $cdt_frm_data = [
            'name' => $postData['name'],
            'school' => $postData['school'],
            'zipcode' => $postData['zip'],
            'phone' => $postData['phone'],
            'email' => $postData['email'],
            'heard_about' => $postData['howfound'],
            'comments' => $postData['comments'],
            'raw_designer_data' => $postData['raw_designer_data'],
        ];
        $this->connection->insert($circular_designer_tool_table, $cdt_frm_data);
        $lastInsertId = $this->connection->lastInsertId();
        
        $template_code = 'circle_design_tool_request_quote';
        $email_template_table = $this->connection->getTableName("email_template");
        $email_template_select_sql = $this->connection->select()
        ->from($email_template_table, 'template_id')->where('orig_template_code = ?', $template_code);
        $template_id = $this->connection->fetchOne($email_template_select_sql);
        if (empty($template_id)) {
            $template_id = $template_code;
        }
       
        $storeId = $this->_storeManager->getStore()->getStoreId();
        $senderName = $postData['name']; //store admin sender name
        $senderEmail = $postData['email']; // store admin email id
        $recipientEmail =  $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ); // recipient email id
        $recipientName =  $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (!$senderEmail && !$recipientEmail) {
            //return false;
            
            $this->manager->addError(__("Something Went long"));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('DesignerWrestlingMat');
            return $resultRedirect;
            
        }

        $this->_inlineTranslation->suspend();
        $this->_transportBuilder
            ->setTemplateIdentifier($template_id)
            ->setTemplateOptions([
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId,
            ])
            ->setTemplateVars(
                [
                    'cdt_id' => $lastInsertId,
                    'name' => $postData['name'],
                    'school' => $postData['school'],
                    'zip' => $postData['zip'],
                    'phone' => $postData['phone'],
                    'email' => $postData['email'],
                    'howfound' => $postData['howfound'],
                    'comments' => $postData['comments'],
                    'raw_designer_data' => $postData['raw_designer_data'],
                    //'raw_designer_data_encode' => $this->urlEncoder->encode($postData['raw_designer_data']),
                    'raw_designer_data_encode' => base64_encode($postData['raw_designer_data']),
                    'store' => $this->_storeManager->getStore(),
                ]
            );
   
            $this->_transportBuilder
            ->setFrom([
                'name'  => $senderName,
                'email' => $senderEmail,
            ])
            ->addTo($recipientEmail, $recipientName);
        /* @var \Magento\Framework\Mail\Transport $transport */
        $transport = $this->_transportBuilder->getTransport();

        try {
            $transport->sendMessage();
            
        } catch (\Exception $e) {
            $this->context->getLogger()->alert($e->getMessage());
        }
        $this->_inlineTranslation->resume();
        
        $this->manager->addSuccess(__("Your design submitted successfully. We will get back to you shortly."));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('/DesignerWrestlingMat');
        return $resultRedirect;
    }
}
