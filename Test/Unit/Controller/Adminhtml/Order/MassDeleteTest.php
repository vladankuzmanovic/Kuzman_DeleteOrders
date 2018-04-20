<?php

namespace Kuzman\DeleteOrders\Test\Unit\Controller\Adminhtml\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class MassDeleteTest
 * @package Kuzman\DeleteOrders\Test\Unit\Controller\Adminhtml\Order
 */
class MassDeleteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Kuzman\DeleteOrders\Controller\Adminhtml\Order\MassDelete
     */
    protected $massAction;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\Message\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\Framework\App\ActionFlag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFlagMock;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionFactoryMock;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderRepositoryMock;

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $this->messageManagerMock = $this->createMock(\Magento\Framework\Message\Manager::class);
        $this->responseMock = $this->createMock(\Magento\Framework\App\ResponseInterface::class);
        $this->requestMock = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $this->objectManagerMock = $this->createMock(\Magento\Framework\ObjectManager\ObjectManager::class);

        $resultRedirectFactory = $this->createMock(\Magento\Backend\Model\View\Result\RedirectFactory::class);

        $this->orderCollectionMock = $this->getMockBuilder(\Magento\Sales\Model\ResourceModel\Order\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resourceCollection = \Magento\Sales\Model\ResourceModel\Order\CollectionFactory::class;
        $this->orderCollectionFactoryMock = $this->getMockBuilder($resourceCollection)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->sessionMock = $this->createPartialMock(\Magento\Backend\Model\Session::class, ['setIsUrlNotice']);
        $this->actionFlagMock = $this->createPartialMock(\Magento\Framework\App\ActionFlag::class, ['get', 'set']);
        $this->helperMock = $this->createPartialMock(\Magento\Backend\Helper\Data::class, ['getUrl']);
        $this->resultRedirectMock = $this->createMock(\Magento\Backend\Model\View\Result\Redirect::class);
        $resultRedirectFactory->expects($this->any())->method('create')->willReturn($this->resultRedirectMock);

        $redirectMock = $this->getMockBuilder(\Magento\Backend\Model\View\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)
            ->willReturn($redirectMock);

        $this->contextMock->expects($this->once())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->once())->method('getResponse')->willReturn($this->responseMock);
        $this->contextMock->expects($this->once())->method('getObjectManager')->willReturn($this->objectManagerMock);
        $this->contextMock->expects($this->once())->method('getSession')->willReturn($this->sessionMock);
        $this->contextMock->expects($this->once())->method('getActionFlag')->willReturn($this->actionFlagMock);
        $this->contextMock->expects($this->once())->method('getHelper')->willReturn($this->helperMock);
        $this->contextMock->expects($this->once())
            ->method('getResultRedirectFactory')
            ->willReturn($resultRedirectFactory);
        $this->contextMock->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($resultFactoryMock);
        $this->filterMock = $this->createMock(\Magento\Ui\Component\MassAction\Filter::class);
        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($this->orderCollectionMock)
            ->willReturn($this->orderCollectionMock);
        $this->orderCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->orderCollectionMock);
        $this->orderRepositoryMock = $this->getMockBuilder(\Magento\Sales\Api\OrderRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->massAction = $objectManagerHelper->getObject(
            \Kuzman\DeleteOrders\Controller\Adminhtml\Order\MassDelete::class,
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'collectionFactory' => $this->orderCollectionFactoryMock,
                'orderRepository' => $this->orderRepositoryMock
            ]
        );
    }

    public function testExecute()
    {
        $order1 = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $order2 = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orders = [$order1, $order2];

        $this->orderCollectionMock->expects($this->any())
            ->method('getItems')
            ->willReturn($orders);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 order(s) were deleted.', count($orders)));

        $this->resultRedirectMock->expects($this->any())
            ->method('setPath')
            ->with('sales/*/')
            ->willReturnSelf();

        $this->massAction->execute();
    }
}
