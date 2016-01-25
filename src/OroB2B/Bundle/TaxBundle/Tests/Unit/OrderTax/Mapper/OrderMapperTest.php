<?php

namespace OroB2B\Bundle\TaxBundle\Tests\Unit\OrderTax\Mapper;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Component\Testing\Unit\EntityTrait;

use OroB2B\Bundle\OrderBundle\Entity\Order;
use OroB2B\Bundle\OrderBundle\Entity\OrderAddress;
use OroB2B\Bundle\OrderBundle\Entity\OrderLineItem;
use OroB2B\Bundle\TaxBundle\Model\Taxable;
use OroB2B\Bundle\TaxBundle\OrderTax\Mapper\OrderLineItemMapper;
use OroB2B\Bundle\TaxBundle\OrderTax\Mapper\OrderMapper;
use OroB2B\Bundle\TaxBundle\Provider\TaxationSettingsProvider;

class OrderMapperTest extends \PHPUnit_Framework_TestCase
{
    use EntityTrait;

    const ORDER_ID = 123;
    const ORDER_SUBTOTAL = 234.34;

    /**
     * @var OrderMapper
     */
    protected $mapper;

    /**
     * @var OrderLineItemMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderLineItemMapper;

    /**
     * @var TaxationSettingsProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $settingsProvider;

    protected function setUp()
    {
        $this->orderLineItemMapper = $this
            ->getMockBuilder('OroB2B\Bundle\TaxBundle\OrderTax\Mapper\OrderLineItemMapper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->settingsProvider = $this->getMockBuilder('OroB2B\Bundle\TaxBundle\Provider\TaxationSettingsProvider')
            ->disableOriginalConstructor()->getMock();
        $this->mapper = new OrderMapper($this->settingsProvider);
        $this->mapper->setOrderLineItemMapper($this->orderLineItemMapper);
    }

    protected function tearDown()
    {
        unset($this->mapper, $this->orderLineItemMapper);
    }

    public function testGetProcessingClassName()
    {
        $this->assertEquals('OroB2B\Bundle\OrderBundle\Entity\Order', $this->mapper->getProcessingClassName());
    }

    public function testMap()
    {
        $this->orderLineItemMapper
            ->expects($this->once())
            ->method('map')
            ->willReturn(new Taxable());

        $order = $this->createOrder(self::ORDER_ID, self::ORDER_SUBTOTAL);

        $taxable = $this->mapper->map($order);

        $this->assertTaxable($taxable, self::ORDER_ID, self::ORDER_SUBTOTAL, $order->getShippingAddress());
        $this->assertCount(1, $taxable->getItems());
        $this->assertInstanceOf('OroB2B\Bundle\TaxBundle\Model\Taxable', $taxable->getItems()->current());
    }

    /**
     * Create order
     *
     * @param int $id
     * @param float $subtotal
     * @return Order
     */
    protected function createOrder($id, $subtotal)
    {
        $orderAddress = (new OrderAddress())
            ->setFirstName('FirstName')
            ->setLastName('LastName')
            ->setStreet('street');

        /** @var Order $order */
        $order = $this->getEntity('OroB2B\Bundle\OrderBundle\Entity\Order', ['id' => $id]);
        $order
            ->setSubtotal($subtotal)
            ->addLineItem(new OrderLineItem())
            ->setShippingAddress($orderAddress);

        return $order;
    }

    /**
     * @param Taxable $taxable
     * @param int $id
     * @param float $subtotal
     * @param AbstractAddress $destination
     */
    protected function assertTaxable($taxable, $id, $subtotal, $destination)
    {
        $this->assertInstanceOf('OroB2B\Bundle\TaxBundle\Model\Taxable', $taxable);
        $this->assertEquals($id, $taxable->getIdentifier());
        $this->assertEquals(1, $taxable->getQuantity());
        $this->assertEquals(0, $taxable->getPrice());
        $this->assertEquals($subtotal, $taxable->getAmount());
        $this->assertEquals($destination, $taxable->getDestination());
        $this->assertNotEmpty($taxable->getItems());
    }
}
