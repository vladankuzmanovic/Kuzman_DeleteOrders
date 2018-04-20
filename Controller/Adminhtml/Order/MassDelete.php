<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Kuzman
 * @package    Kuzman_DeleteOrders
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Vladan Kuzmanovic (vladan.kuzman@gmail.com)
 */
namespace Kuzman\DeleteOrders\Controller\Adminhtml\Order;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Delete orders controller
 *
 * Class MassDelete
 * @package Kuzman\DeleteOrders\Controller\Adminhtml\Order
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Kuzman_DeleteOrders::delete';

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderRepositoryInterface $orderRepository
    )
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Delete selected orders
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $countDeleteOrder = 0;
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection->getItems() as $order) {
            $this->orderRepository->delete($order);
            $countDeleteOrder++;
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 order(s) were deleted.', $countDeleteOrder)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('sales/*/');
    }
}
