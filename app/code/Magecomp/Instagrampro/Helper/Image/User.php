<?php

namespace Magecomp\Instagrampro\Helper\Image;

use Magecomp\Instagrampro\Helper\Data as HelperData;
use Magecomp\Instagrampro\Helper\Image;
use Magecomp\Instagrampro\Model\InstagramauthFactory;
use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class User extends Image
{
    const GRAPH_API_INSTAGRAM_FEED_FETCH = "https://graph.facebook.com/v5.0/%ig_business_id%/media";
    const GRAPH_API_INSTAGRAM_FEED_NEXT_FEED = "instagrampro/feed/next";
    const GRAPH_API_INSTAGRAM_FEED_BEFORE_FEED = "instagrampro/feed/before";
    const GRAPH_API_INSTAGRAM_FEED_LIKE_COUNT = "https://graph.facebook.com/v5.0/";
    const GRAPH_API_INSTAGRAM_FEED_UPDATE = "https://graph.facebook.com/v13.0/%ig_media_id%";
     const GRAPH_API_GET_ACCOUNT_URL = "https://graph.facebook.com/v5.0/me/accounts";

    protected $_helperData;
    protected $_modelInstagramauthFactory;
    protected $_storeManager;
    protected $urlInterface;
    protected $_modelInstagramimageFactory;
    protected $_WriterInterface;

    public function __construct(
        Context $context,
        HelperData $helperData,
        WriterInterface $WriterInterface,
        InstagramauthFactory $modelInstagramauthFactory,
        StoreManagerInterface $storeManager,
        InstagramproimageFactory $modelInstagramimageFactory,
        UrlInterface $urlInterface
    ) {
        $this->_helperData = $helperData;
        $this->_WriterInterface = $WriterInterface;
        $this->_modelInstagramauthFactory = $modelInstagramauthFactory;
        $this->_modelInstagramimageFactory = $modelInstagramimageFactory;
        $this->_storeManager = $storeManager;
        $this->urlInterface = $urlInterface;


        parent::__construct($context, $WriterInterface, $storeManager);
    }

    public function updateUserLies($storeid = 0)
    {
        $ig_business_name = $this->scopeConfig->getValue('instagrampro/aut/ig_business_username', ScopeInterface::SCOPE_STORES, $storeid);
        $collectionForLike = $this->_modelInstagramimageFactory->create()->getCollection()
            ->addFieldToFilter('username', $ig_business_name)
            ->addFieldToFilter('is_visible', 1)
            ->addFieldToFilter('is_approved', 1)
            ->addFieldToFilter('ig_feed_id', ['neq' => 'NULL']);

        if (count($collectionForLike) > 0) {
            $accessToken = $this->scopeConfig->getValue('instagrampro/module_options/access_token', ScopeInterface::SCOPE_STORE);

            foreach ($collectionForLike as $feedId) {

                $endpointUrl = self::GRAPH_API_INSTAGRAM_FEED_LIKE_COUNT . $feedId->getIgFeedId();
                $endpointUrl = $this->_helperData->buildUrl($endpointUrl, [
                    'fields' => "like_count",
                    'access_token' => $accessToken,
                ]);
                $likeCount = $this->fetchLiveCount($endpointUrl);
                try {
                    if ($likeCount != "-1") {
                        $feed = $this->_modelInstagramimageFactory->create()->load($feedId->getImageId());
                    }
                    $feed->setImageLikes($likeCount);
                    $feed->save();
                    unset($feed);
                } catch (\Exception $exception) {

                }
            }
        }
        return true;
    }

    public function fetchLiveCount($endpointUrl)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpointUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $output = curl_exec($ch);

            curl_close($ch);
            $data = json_decode($output);
            if ($data->like_count) {
                $likeCount = $data->like_count;
            } else {
                $likeCount = "-1";
            }
            return $likeCount;
        } catch (\Exception $exception) {
            $likeCount = "-1";
            return $likeCount;
        }
    }

    public function updatemediaurl($storeid = 0)
    {
        $instagramdatacollection = $this->_modelInstagramimageFactory->create()->getCollection();
       
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

                               $feed = $this->_modelInstagramimageFactory->create()->load($instgram_image_id);
                               $feed->setStandardResolutionUrl($data['media_url']);
                               $feed->setThumbnailUrl($data['thumbnail_url']);
                               $feed->save();
                    } else {
                         $feed = $this->_modelInstagramimageFactory->create()->load($instgram_image_id);
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


    public function update($storeid = 0)
    {
        $responseStatus = true;
        $ig_business_id = $this->scopeConfig->getValue('instagrampro/aut/ig_businss_id', ScopeInterface::SCOPE_STORES, $storeid);
        $endpointUrl = $this->getEndpointUrl($ig_business_id, $storeid);
        $fetchCount = $this->fetchCount($storeid);
        $response = $this->getImages($endpointUrl, '@' . $ig_business_id, $storeid, $fetchCount);
        if (isset($response['error'])) {
            $responseStatus = false;
        }
        return $responseStatus;
    }

    public function getEndpointUrl($ig_business_id, $storeid)
    {
        $endpointUrl = str_replace('%ig_business_id%', $ig_business_id, self::GRAPH_API_INSTAGRAM_FEED_FETCH);

        $helper = $this->_helperData;
        $fetchCount = $this->fetchCount($storeid);
        $accessToken = $this->scopeConfig->getValue('instagrampro/module_options/access_token', ScopeInterface::SCOPE_STORE);
        $endpointUrl = $helper->buildUrl($endpointUrl, [
            'fields' => "id,media_type,media_product_type,timestamp,username,comments_count,caption,like_count,comments,shortcode,permalink,owner,media_url,thumbnail_url,ig_id&limit=$fetchCount&pretty=0",
            'access_token' => $accessToken,
        ]);
        return $endpointUrl;
    }

    public function fetchCount($Storeid)
    {
        return (int)$this->scopeConfig->getValue('instagrampro/generalgroup/imagefatch', ScopeInterface::SCOPE_STORE, $Storeid);
    }

    protected function getImages($endpointUrl, $tag, $Storeid, $fetchCount)
    {

        $total_image = 0;
       
        $lastRecOfTable = $this->_modelInstagramimageFactory->create()->getCollection()->getSize();
    

        while (1) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $endpointUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                $output = curl_exec($ch);


                curl_close($ch);

                $data = json_decode($output);
           
                unset($output);
                $out = [];
                if (isset($data->error)) {
                    $out['error'] = __("Instagram connect error");
                    return $out;
                }
                if (!isset($data->data)) {
                    $out['error'] = __("Instagram data not founded");
                    return $out;
                }
                if (isset($data->paging->next)) {
                    $out['nextUrl'] = $data->paging->next;
                }
                if (isset($data->paging->cursors->after)) {
                    $out['nextMaxId'] = $data->paging->cursors->after;
                }
                $i = 0;
                while ($i < count($data->data)) {
                    $item = $data->data[$i];
                    if (isset($item->media_url)) {
                        $username = $item->username;
                        $standardResolutionUrl = $item->media_url;
                        $systemId = $item->shortcode;
                        $media_type = $item->media_type;
                        $likes = $item->like_count;
                        $comments = $item->comments_count;
                        $link = $item->permalink;
                        $ig_feed_id = $item->id;
                        $captionText = "";
                        if (isset($item->caption)) {
                            $captionText = $item->caption;
                        }




                        $image = $this->_modelInstagramimageFactory->create();
                        $image->setThumbnailUrl($standardResolutionUrl)
                        ->setStandardResolutionUrl($standardResolutionUrl)
                        ->setImageId($systemId)
                        ->setUsername($username)
                        ->setCaptionText($captionText)
                        ->setImageDesc($captionText)
                        ->setMediaType(strtolower($media_type))
                        ->setImageLikes($likes)
                        ->setImageComments($comments)
                        ->setLink($link)
                        ->setTag($username)
                        ->setIgFeedId($ig_feed_id)
                        ->save();
                    }
                    $latestRecOfTable = $this->_modelInstagramimageFactory->create()->getCollection()->getSize();
                    if ($latestRecOfTable > $lastRecOfTable) {
                        $total_image++;
                        $lastRecOfTable = $latestRecOfTable;
                    }
                    $i++;
                    if ($total_image >= $fetchCount) {
                        break;
                    }
                }
                if ($data->paging) {
                    $endpointUrl = $data->paging->next;
                    // $endpointUrl =  $data->paging->cursors->after;
                }
            } catch (\Exception $e) {

            }
            if ($total_image >= $fetchCount) {
                break;
            }
        }
        $this->_helperData->saveGlobalConfigValue(self::GRAPH_API_INSTAGRAM_FEED_NEXT_FEED, $data->paging->cursors->after, $Storeid);
        $this->_helperData->saveGlobalConfigValue(self::GRAPH_API_INSTAGRAM_FEED_BEFORE_FEED, $data->paging->cursors->before, $Storeid);
        return $out;
    }

    public function getEndpointUrlData($ig_media_id, $storeid)
    {
        $endpointUrl = str_replace('%ig_media_id%', $ig_media_id, self::GRAPH_API_INSTAGRAM_FEED_UPDATE);

        $helper = $this->_helperData;
        $fetchCount = $this->fetchCount($storeid);
        $accessToken = $this->scopeConfig->getValue('instagrampro/module_options/access_token', ScopeInterface::SCOPE_STORE);
        $endpointUrl = $helper->buildUrl($endpointUrl, [
            'fields' => "id,media_type,media_product_type,timestamp,username,comments_count,caption,like_count,comments,shortcode,permalink,owner,media_url,thumbnail_url,ig_id&limit=$fetchCount&pretty=0",
            'access_token' => $accessToken,
        ]);
        return $endpointUrl;
    } 
    public function updateuserdata()
    {
       
        // $om = \Magento\Framework\App\ObjectManager::getInstance();
        // $storeManager = $om->get('Psr\Log\LoggerInterface');
        // $storeManager->info("profile");
        $accessToken = $this->scopeConfig->getValue('instagrampro/module_options/access_token', ScopeInterface::SCOPE_STORE);
         $field = "?fields=name,id,access_token,link,instagram_business_account{id,username,profile_picture_url},app_id&access_token=" . $accessToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::GRAPH_API_GET_ACCOUNT_URL . $field);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($output,true);

        // $storeManager->info(print_r($data,true));
        //$storeManager->info("test".$data['data'][0]['profile_picture_url']);
        $dataInstagramBusinessProfilePic = "";

         //$storeManager->info("1");
         if ($data) {
            //$storeManager->info("2");
            $data2 = $data['data'];
            if (isset($data2[0])) {
               // $storeManager->info("3");
                $data3 = $data2[0];
                //$storeManager->info("4");
                if (isset($data3['instagram_business_account'])) {
                    //$storeManager->info("5");
                    $dataInstagramBusinessProfilePic = $data3['instagram_business_account']['profile_picture_url'];
                }
            }
        }
       
      
       /* $endpointMediaUrl = $this->getEndpointUrlData($ig_media_id, $storeid);
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $om->get('Psr\Log\LoggerInterface');
        $storeManager->info("media id : ".$ig_media_id);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpointMediaUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        $output = curl_exec($ch);
        
        curl_close($ch);

        $data = json_decode($output, true);
        $storeManager->info(print_r($data,true));*/

        return $dataInstagramBusinessProfilePic;
    }
}
