<?php

namespace Oro\Bundle\ShippingBundle\Tests\Unit\Form\EventSubscriber;

class MethodConfigSubscriberTest extends AbstractConfigSubscriberTest
{
    public function setUp()
    {
        parent::setUp();
        $this->subscriber = $this->methodConfigSubscriber;
    }
}
