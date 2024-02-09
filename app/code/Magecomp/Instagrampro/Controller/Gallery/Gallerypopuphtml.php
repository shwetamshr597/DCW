<?php
namespace Magecomp\Instagrampro\Controller\Gallery;

use Magecomp\Instagrampro\Helper\Data as HelperData;
use Magecomp\Instagrampro\Helper\Graphapi;
use Magecomp\Instagrampro\Helper\Image;
use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Catalog\Helper\Image as CatalogHelper;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Product as ProductLoader;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magecomp\Instagrampro\Helper\Image\User;

class Gallerypopuphtml extends Action
{

    protected $_modelInstagramproimageFactory;
    protected $_helperData;
    protected $_helperImage;
    protected $_productloader;
    protected $productimghelper;
    protected $graphapi;
    protected $_resultPageFactory;
    protected $_resultJsonFactory;
    protected $customhelper;


    public function __construct(
        Context $context,
        Product $helperProduct,
        InstagramproimageFactory $modelInstagramproimageFactory,
        HelperData $helperData,
        Image $helperImage,
        ProductLoader $productloader,
        CatalogHelper $producthelper,
        Graphapi $graphapi,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        User $customhelper
    ) {
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;
        $this->_helperData = $helperData;
        $this->_helperImage = $helperImage;
        $this->_productloader = $productloader;
        $this->productimghelper = $producthelper;
        $this->graphapi = $graphapi;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->customhelper = $customhelper;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $post = $this->getRequest()->getPostValue();
            if ($post['Id'] != '') {
                $html = '';
                $image = $this->_modelInstagramproimageFactory->create()->load($post['Id']);
                if ($image->getLink() != '') {
                    $insta_url = $image->getLink() . '?__a=1';
                } else {
                    $insta_url = 'https://www.instagram.com/p/' . $post['Id'] . '/?__a=1';
                }

                 
                $profilepicture = $this->customhelper->updateuserdata();
        
                $html .= "<div id='loadingdiv' style='display:none'></div>";
                $html .= "<div id='whitebgdiv'>";

                if ($this->_helperData->shownpInPopup()):
                    $html .= "<div id='prevbtndiv' onclick='prevnextpopup(1);'></div>";
                    $html .= "<div id='nextbtndiv' onclick='prevnextpopup(2);'></div>";
                endif;
                $html .= "<div id='closebtndiv' onclick='closepopup();'></div>";

                $html .= "<div id='leftpart'>"; // LEFT-PART START
                if ($image->getMediaType() == "video") {
                    $html .= " <video width='97%'  controls>
                	<source src='" . $image->getThumbnailUrl() . "' type='video/mp4'>
                	Your browser does not support the video tag.
                    </video>";
                }
                else{
                    $html .= "<img alt='" . $image->getImageTitle() . "' src='" . $image->getThumbnailUrl() . "' />";
                } 
                $html .= "<span id='titletext'></span>";
                $html .= "</div>"; // LEFT-PART END

                $html .= "<div id='rightpart'>"; // RIGHT-PART START

                // USER DATA ADDED TO THIS (Start)
                $vedio_url = $insta_url;
                
                $html .= " <div class='main-user-div'>";
                $html .= "<div class='user'><div class='userimage'><img src='" . $profilepicture . "' alt='" . $image->getUsername() . "'></div> <div class='userlink'><a target='_blank' href='https://www.instagram.com/" . $image->getUsername() . "'>" . $image->getUsername() . "</a></div></div>";
                $html .= "<div class='popup_follow_link'><a target='_blank' href='https://www.instagram.com/" . $image->getUsername() . "'><span class='action primary popup follow_btn'>Follow</span></a></div>";
                $html .= "</div>";
                // USER DATA ADDED TO THIS (End)


                if ($this->_helperImage->getPopupConfiguration()): // SHOW PRODUCT IN POPUP
                    $html .= " <div class='insta-popup-product-view'> ";

                    $html .= "<div class='insta-popup-product-view-right'>";

                    $html .= "<ul class='alltitleul prditem'>";

                    $prodId = "";
                    for ($i = 1; $i <= 6; $i++) {

                        if ($image['product_id_' . $i] != '' && $image['product_id_' . $i] != 0):
                            $product = $this->_productloader->load($image['product_id_' . $i]);
                            if (!$prodId) {
                                $prodId = $image['product_id_' . $i];
                            }
                            $html .= "<li class='" . $image['title' . $i . 'x'] . "-" . $image['title' . $i . 'y'] . "'>";
                            $html .= "<div id='prditemdiv$i'>";
                            $html .= "<a>";
                            $html .= "<span class='number'>&nbsp;</span>";
                            $html .= "<span class='numbertitle'> " . $product->getName() . "</span>";
                            $html .= "</a>";
                            $html .= "</div>";

                            $html .= "<a onclick='changeprodtoleft(" . $product->getId() . ");' id='popup-li-prod-" . $product->getId() . "' href='#' title='" . $product->getName() . "' class='prdblocka'>";
                            $html .= "<img src='" . $this->productimghelper->init($product, 'category_page_list')->constrainOnly(false)->keepAspectRatio(false)->keepFrame(false)->resize(75, 75)->getUrl() . "' />";
                            $html .= "</a>";
                            $html .= "</li>";
                        endif;

                    }

                    if ($prodId) {
                        $firstProduct = $this->_productloader->load($prodId);
                        $title = urlencode($firstProduct->getName());
                        $url = $firstProduct->getProductUrl();
                        $html .= "</ul>";
                        $html .= "</div>";
                        $html .= "<div class='insta-popup-product-view-left' id='prod-left-id-" . $product->getId() . "'>";
                        $html .= "<div class='insta-popup-product-view-left-img'>";
                        $html .= "<div id='popprdloadingdiv' style='display:none'></div>";
                        $html .= "<img src='" . $this->productimghelper->init($firstProduct, 'category_page_list')->constrainOnly(false)->keepAspectRatio(false)->keepFrame(false)->resize(220, 220)->getUrl() . "' />";
                        $html .= "<a href='" . $url . "' target='_blank'> <span class='insta-popup-product-view-left-img-name'>SHOP NOW</span></a>";
                        $html .= "</div>";
                        $html .= "</div>";
                        $html .= "</div>";
                        $html .= "<div class='popup-img-desc'>";
                        $html .= "<div class='instatitle'>" . $firstProduct->getName() . "</div>";
                        if (!empty($firstProduct->getShortDescription())) {
                            $html .= "<div class='popup-img-desc-part'>" . substr($firstProduct->getShortDescription(), 0, 150) . "...</div>";
                        }
                        $html .= "</div>";

                        $html .= "<div class='popup-share-icon'>";
                        $html .= "<div class='popup-share-icon-title'>Share This Products</div>";
                        $html .= "<div class='popup-share'>";


                        $html .= " <span class='popup-share-fb'>";
                        $html .= "<a class='fb-share-link' href='https://www.facebook.com/sharer/sharer.php?u=" . $url . "&t=" . $title . "' title='Share on FaceBook' target='_blank'>";
                        $html .= "<i class='fa fa-facebook'></i>";
                        $html .= "</a>";
                        $html .= "</span>";
                        $html .= "<span class='popup-share-twitter'>";
                        $html .= "<a class='tiwtter-share-link' href='https://twitter.com/share?url=" . $url . "&text=" . $title . "' title='Share on Twitter' target='_blank'>";
                        $html .= "<i class='fa fa-twitter'></i>";
                        $html .= "</a>";
                        $html .= "</span>";
                        $html .= "<a class='linkedin-share-link' href='https://www.linkedin.com/shareArticle?mini=true&url=" . $url . "&title=" . $title . "' title='Share on LinkedIn' target='_blank'>";

                        $html .= " <span class='popup-share-linkedin'><i class='fa fa-linkedin'></i></span>";
                        $html .= "</a>";

                        $html .= "</div>";
                        $html .= "</div>";
                    }
                    $html .= "";
                else: // SHOW TITLE WITH LINKS IN POPUP
                    $html .= "<ul class='alltitleul'>";
                    for ($i = 1; $i <= 6; $i++) {
                        if ($image['title' . $i] != '') {
                            $html .= "<li class='" . $image['title' . $i . 'x'] . "-" . $image['title' . $i . 'y'] . "'>";
                            $html .= "<a href='" . $image['titlelink' . $i] . "' target='_blank'>";
                            $html .= "<div id='prditemdiv$i'>";
                            $html .= "<a href='" . $image['titlelink' . $i] . "' target='_blank'>";
                            $html .= "<span class='number'>&nbsp;</span>";
                            $html .= "<span class='numbertitle'> " . $image['title' . $i] . "</span>";
                            $html .= "</a>";
                            $html .= "</div>";
                            $html .= "</a>";
                            $html .= "</li>";
                        }
                    }
                    $html .= "</ul>";
                endif;
                $html .= "</div>"; // RIGHT-PART END
                $html .= "</div>";

                $result = $this->_resultJsonFactory->create();
                $result->setData($html);

                return $result;

            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
