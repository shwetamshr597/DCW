<?php

namespace Dcw\ProductImage\Observer;

use Magento\Framework\Event\ObserverInterface;

class Productsaveafter implements ObserverInterface {
    public function execute (\Magento\Framework\Event\Observer $observer) {
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();  
    $request = $objectManager->get('Magento\Framework\Webapi\Rest\Request'); 
    $body = $request->getBodyParams();					
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $tableName = $connection->getTableName("catalog_product_entity_media_gallery");
    $product = $observer->getProduct ();
    $image = $product->getData('image');

    if(isset($body['product']['media_gallery_entries']) && !empty($body['product']['media_gallery_entries'])){
				$postdata = $body['product']['media_gallery_entries'];
				foreach($postdata as $data){
					if(isset($image) && $image!=""){
						$imgextattr = $data['extension_attributes']['canto_url'];
						$data = ["custom_image_link" => $imgextattr]; // Key_Value Pair
						$where = ['value = ?' => $image];
						$connection->update($tableName, $data, $where);
					}
				}
				
			}
    }
    }
