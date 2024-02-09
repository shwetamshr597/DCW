<?php
/**
 * Created by PhpStorm.
 * User: Bharat-Magecomp
 * Date: 12/23/2019
 * Time: 10:22 AM
 */

namespace Magecomp\Instagrampro\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Graphapi extends AbstractHelper
{
    const GRAPH_API_GET_ACCOUNT_URL = "https://graph.facebook.com/v5.0/me/accounts";
    const GRAPH_API_CONFIG_FB_PAGE_ID = "instagrampro/aut/fb_page_id";
    const GRAPH_API_CONFIG_FB_PAGE_NAME = "instagrampro/aut/fb_page_name";
    const GRAPH_API_CONFIG_FB_PAGE_ACCESS_TOKEN = "instagrampro/aut/fb_page_access_token";
    const GRAPH_API_CONFIG_FB_PAGE_LINK = "instagrampro/aut/fb_page_link";
    const GRAPH_API_CONFIG_IG_BUSINESS_ID = "instagrampro/aut/ig_businss_id";
    const GRAPH_API_CONFIG_IG_BUSINESS_USERNAME = "instagrampro/aut/ig_business_username";
    const GRAPH_API_CONFIG_IG_BUSINESS_PROFILEPIC = "instagrampro/aut/ig_business_profilepic";

    protected $helperData;
    protected $configWriter;

    public function __construct(
        Context $context,
        Data $helperdata,
        WriterInterface $configWriter
    ) {
        $this->helperdata = $helperdata;
        $this->configWriter = $configWriter;
        parent::__construct($context);
    }

    public function getAuthenticateData($storeId)
    {
        $system_user_access_token = $this->helperdata->getAccessToken();

        $businessData = $this->getInstagramBusinessData($system_user_access_token);

        $this->helperdata->saveGlobalConfigValue(self::GRAPH_API_CONFIG_FB_PAGE_ID, $businessData['fb_page_id'], $storeId);
        $this->helperdata->saveGlobalConfigValue(self::GRAPH_API_CONFIG_FB_PAGE_NAME, $businessData['fb_page_name'], $storeId);
        $this->helperdata->saveGlobalConfigValue(self::GRAPH_API_CONFIG_FB_PAGE_ACCESS_TOKEN, $businessData['fb_page_access_token'], $storeId);
        $this->helperdata->saveGlobalConfigValue(self::GRAPH_API_CONFIG_FB_PAGE_LINK, $businessData['fb_page_link'], $storeId);
        $this->helperdata->saveGlobalConfigValue(self::GRAPH_API_CONFIG_IG_BUSINESS_ID, $businessData['ig_businss_id'], $storeId);
        $this->helperdata->saveGlobalConfigValue(self::GRAPH_API_CONFIG_IG_BUSINESS_USERNAME, $businessData['ig_business_username'], $storeId);
        $this->helperdata->saveGlobalConfigValue(self::GRAPH_API_CONFIG_IG_BUSINESS_PROFILEPIC, $businessData['ig_business_profilepic'], $storeId);


        return $businessData;
    }

    public function getInstagramBusinessData($system_user_access_token)
    {
        

        $field = "?fields=name,id,access_token,link,instagram_business_account{id,username,profile_picture_url},app_id&access_token=" . $system_user_access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::GRAPH_API_GET_ACCOUNT_URL . $field);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($output);

        $dataInstagramBusinessId = "";
        if (isset($data->data)) {

            $data2 = $data->data;
            if (isset($data2[0])) {
                $data3 = $data2[0];
                $fbPageId = $data3->id;
                $fbPageName = $data3->name;
                $fbPageAccessToken = $data3->access_token;
                $fbPageLink = $data3->link;
                if (isset($data3->instagram_business_account)) {
                    $dataInstagramBusinessId = $data3->instagram_business_account->id;
                    $dataInstagramBusinessUsername = $data3->instagram_business_account->username;
                    $dataInstagramBusinessProfilePic = $data3->instagram_business_account->profile_picture_url;
                }
            }
        }

        $businessData = [
            'fb_page_id' => $fbPageId,
            'fb_page_name' => $fbPageName,
            'fb_page_access_token' => $fbPageAccessToken,
            'fb_page_link' => $fbPageLink,
            'ig_businss_id' => $dataInstagramBusinessId,
            'ig_business_username' => $dataInstagramBusinessUsername,
            'ig_business_profilepic' => $dataInstagramBusinessProfilePic
        ];

        return $businessData;
    }
}
