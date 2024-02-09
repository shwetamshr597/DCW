<?php
namespace Magecomp\Instagrampro\Api;

/**
 * interface InstagramInterface
 * Magecomp\Instagram\Api
 */
interface InstagramProManagementInterface
{
 
    /**
     * Get Configuration data
     * 
     * @return string
     */

   public function getConfigData();

   /**
     * Get Approved Images data
     * @param string $page
     * @return string
     */

    public function getImageData($page);

    /**
     * Get Approved Images data
     * @param int $productId
     * @return string
     */

    public function getProductPageImageData($productId);


}