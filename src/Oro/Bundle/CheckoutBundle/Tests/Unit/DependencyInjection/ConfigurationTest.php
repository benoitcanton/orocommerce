<?php

namespace Oro\Bundle\CheckoutBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\CheckoutBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test Configuration
     */
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $builder = $configuration->getConfigTreeBuilder();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $builder);
    }

    /**
     * @dataProvider processConfigurationDataProvider
     * @param array $configs
     * @param array $expected
     */
    public function testProcessConfiguration(array $configs, array $expected)
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $this->assertEquals($expected, $processor->processConfiguration($configuration, $configs));
    }

    /**
     * @return array
     */
    public function processConfigurationDataProvider()
    {
        return [
            'empty' => [
                'configs'  => [[]],
                'expected' => [
                    'settings' => [
                        'frontend_open_orders_separate_page' => [
                            'value' => false,
                            'scope' => 'app'
                        ],
                        'guest_checkout' => [
                            'value' => false,
                            'scope' => 'app'
                        ],
                        'single_page_checkout_increase_performance' => [
                            'value' => false,
                            'scope' => 'app'
                        ],
                        'registration_allowed' => [
                            'value' => true,
                            'scope' => 'app'
                        ],
                        'resolved' => true,
                        Configuration::DEFAULT_GUEST_CHECKOUT_OWNER => [
                            'value' => null,
                            'scope' => 'app'
                        ],
                        'allow_checkout_without_email_confirmation' => [
                            'value' => false,
                            'scope' => 'app'
                        ]
                    ]
                ]
            ]
        ];
    }
}
