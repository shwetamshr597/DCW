<?php

namespace Magecomp\Instagrampro\Helper\Image;

use Magecomp\Instagrampro\Helper\Image;
use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magecomp\Instagrampro\Helper\Data as HelperData;

class Hashtag extends Image
{
    const GRAPH_API_INSTAGRAM_FEED_FETCH1 ="https://graph.facebook.com/ig_hashtag_search";
    const GRAPH_API_INSTAGRAM_FEED_FETCH ="https://graph.facebook.com/";
      const GRAPH_API_INSTAGRAM_FEED_UPDATE = "https://graph.facebook.com/v13.0/%ig_media_id%";

    protected $_modelInstagramproimageFactory;
    protected $_WriterInterface;
    protected $_storeManager;
    protected $_helperData;

    public function __construct(
        Context $context,
        WriterInterface $WriterInterface,
        InstagramproimageFactory $modelInstagramproimageFactory,
        StoreManagerInterface $storeManager,
        HelperData $helperData
    ) {
        $this->_WriterInterface = $WriterInterface;
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;
        $this->_storeManager = $storeManager;
        $this->_helperData = $helperData;
        parent::__construct($context, $WriterInterface, $storeManager);
    }

    
    public function updatemediaurl($storeid = 0)
    {
        $instagramdatacollection = $this->_modelInstagramproimageFactory->create()->getCollection();
       
        foreach ($instagramdatacollection as $image) {
            $instgram_image_id = $image->getData('image_id');
            $ig_media_id = $image->getData('ig_feed_id');

            if ($image->getData('ig_feed_id')) {

                $endpointMediaUrl = $this->getEndpointUrlUpdatemediaurl($ig_media_id, $storeid);
                
                   
                   $ch = curl_init();
                   curl_setopt($ch, CURLOPT_URL, $endpointMediaUrl);
                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                   curl_setopt($ch, CURLOPT_HEADER, 0);
                   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                   $output = curl_exec($ch);

                   $data = json_decode($output, true);


                       curl_close($ch);
                   
                if (isset($data)) {

                    if (isset($data['thumbnail_url'])) {

                               $feed = $this->_modelInstagramproimageFactory->create()->load($instgram_image_id);
                               $feed->setStandardResolutionUrl($data['media_url']);
                               $feed->setThumbnailUrl($data['thumbnail_url']);
                               $feed->save();
                    } else {
                         $feed = $this->_modelInstagramproimageFactory->create()->load($instgram_image_id);
                         $feed->setStandardResolutionUrl($data['media_url']);
                          $feed->setThumbnailUrl($data['media_url']);
                         $feed->save();

                    }
                    
                }
            }

        }
    }

    public function getEndpointUrlUpdatemediaurl($ig_media_id, $storeid)
    {
        $endpointUrl = str_replace('%ig_media_id%', $ig_media_id, self::GRAPH_API_INSTAGRAM_FEED_UPDATE);

        $helper = $this->_helperData;
        $fetchCount = $this->fetchCount($storeid);
        $accessToken = $this->scopeConfig->getValue('instagrampro/module_options/access_token', ScopeInterface::SCOPE_STORE);
        $endpointUrl = $helper->buildUrl($endpointUrl, [
            'fields' => "media_url,thumbnail_url",
            'access_token' => $accessToken,
        ]);
        return $endpointUrl;
    }

    public function update($storeid)
    {
        $responseStatus = true;
        $ImageFatch = $this->getFetchImageCount($storeid);
        if ($ImageFatch == '' || $ImageFatch <= 0) {
            $ImageFatch = 100;
        }

        $ig_business_id = $this->scopeConfig->getValue('instagrampro/aut/ig_businss_id', ScopeInterface::SCOPE_STORES, $storeid);
        $Hashtaglist = $this->getTags($storeid);
        foreach ($Hashtaglist as $hashid) {
            $hashid = trim($hashid);
            if ($hashid != ''):
                $total_image = 0;
                $endpointUrl = $this->getEndpointUrl($ig_business_id, $storeid, $hashid);
                try {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $endpointUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    $output = curl_exec($ch);
                    curl_close($ch);
                    $data = json_decode($output);
                    $i = 0;


                    while ($i < count($data->data)) {
                        $item = $data->data[$i];
                        $endpointUrl1 = $this->getHashtagImage($ig_business_id, $storeid, $item->id, $ImageFatch);
                         $ch = curl_init();
                         curl_setopt($ch, CURLOPT_URL, $endpointUrl1);
                         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                         curl_setopt($ch, CURLOPT_HEADER, 0);
                         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                         $output1 = curl_exec($ch);
                         curl_close($ch);
                         $data1 = json_decode($output1);
                         $j = 0;
                        while ($j < count($data1->data)) {
                            $item1 = $data1->data[$j];
                         

                            $captionText = "";
                            if (isset($item1->caption)) {
                                $captionText = $item1->caption;
                            }
                            if (isset($item1->media_url)) {
                                $image = $this->_modelInstagramproimageFactory->create();
                                $image->setThumbnailUrl($item1->media_url)
                                    ->setStandardResolutionUrl($item1->media_url)
                                    ->setImageId($item1->id)
                                    ->setUsername($item->name)
                                    ->setCaptionText($captionText)
                                    ->setMediaType(strtolower($item1->media_type))
                                    ->setTag($hashid)
                                    ->setImageLikes($item1->like_count)
                                    ->setLink($item1->permalink)
                                    ->setImageComments($item1->comments_count)
                                    ->setIgFeedId($item1->id)
                                    ->save();
                                $total_image++;
                                if ($total_image >= $ImageFatch) {
                                    break;
                                }
                            }
                            $j++;
                        }
                        if ($data1->paging) {
                            $endpointUrl =  $data1->paging->cursors->after;
                        }
                        $i++;
                    }
                } catch (\Exception $e) {
                }
                if ($total_image >= $ImageFatch) {
                    break;
                }
            endif;
        }
        return $responseStatus;
    }
    public function getEndpointUrl($ig_business_id, $storeid, $hashid)
    {
        $endpointUrl = str_replace('%ig_business_id%', $ig_business_id, self::GRAPH_API_INSTAGRAM_FEED_FETCH1);
        $helper = $this->_helperData;
        $accessToken = $this->scopeConfig->getValue('instagrampro/module_options/access_token', ScopeInterface::SCOPE_STORE, $storeid);
        $endpointUrl = $helper->buildUrl($endpointUrl, [
            'user_id'=>$ig_business_id,
            'q'=>$hashid,
            'fields' => "id,name",
            'access_token' => $accessToken,
        ]);
        return $endpointUrl;
    }
    public function getHashtagImage($ig_business_id, $storeid, $userid ,$ImageFatch)
    {
        $endpointUrl = self::GRAPH_API_INSTAGRAM_FEED_FETCH.$userid."/recent_media";
        $helper = $this->_helperData;
        // $fetchCount = $this->fetchCount($storeid);
        $fetchCount = $ImageFatch;
        $accessToken = $this->scopeConfig->getValue('instagrampro/module_options/access_token', ScopeInterface::SCOPE_STORE, $storeid);
        $endpointUrl = $helper->buildUrl($endpointUrl, [
            'user_id'=>$ig_business_id,
            'fields' => "id,comments_count,like_count,children,media_type,media_url,permalink&limit=$fetchCount&pretty=0",
            'access_token' => $accessToken,
        ]);
        return $endpointUrl;
    }
    public function fetchCount($Storeid)
    {
        return (int)$this->scopeConfig->getValue('instagrampro/generalgroup/imagefatch', ScopeInterface::SCOPE_STORE, $Storeid);
    }
}
