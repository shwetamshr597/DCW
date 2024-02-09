<?php
/**
 * Magento Instagrampro extension
 *
 * @category   Magecomp
 * @package    Magecomp_Instagrampro
 * @author     Magecomp
 */
namespace Magecomp\Instagrampro\Controller\Adminhtml\Proindex;

class NewAction extends AbstractProindex
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
