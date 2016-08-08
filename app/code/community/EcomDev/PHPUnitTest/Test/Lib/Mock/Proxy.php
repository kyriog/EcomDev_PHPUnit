<?php
/**
 * PHP Unit test suite for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   EcomDev
 * @package    EcomDev_PHPUnit
 * @copyright  Copyright (c) 2013 EcomDev BV (http://www.ecomdev.org)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Ivan Chepurnyi <ivan.chepurnyi@ecomdev.org>
 */

class EcomDev_PHPUnitTest_Test_Lib_Mock_Proxy extends PHPUnit_Framework_TestCase
{
    /**
     * @var EcomDev_PHPUnit_Mock_Proxy
     */
    protected $mockProxy;

    /**
     *
     */
    protected function setUp()
    {
        $this->mockProxy = new EcomDev_PHPUnit_Mock_Proxy($this, 'EcomDev_PHPUnit_AbstractConstraint');
        $this->mockProxy->disableOriginalConstructor();
    }

    public function testRemoveMethod()
    {
        EcomDev_Utils_Reflection::setRestrictedPropertyValue($this->mockProxy, 'methods', array(
            'methodName', 'methodName2', 'methodName3',
        ));

        $this->mockProxy->removeMethod('methodName2');
        $this->assertAttributeEquals(array('methodName', 'methodName3'), 'methods', $this->mockProxy);
        $this->mockProxy->removeMethod('methodName');
        $this->assertAttributeEquals(array('methodName3'), 'methods', $this->mockProxy);
    }


    public function testPreserveMethods()
    {
        $this->assertAttributeSame(array(), 'methods', $this->mockProxy);
        $this->mockProxy->preserveMethods();
        $this->assertAttributeSame(null, 'methods', $this->mockProxy);
    }

    public function testGetMockInstance()
    {

        $mockInstance = $this->mockProxy->getMockInstance();

        $this->assertInstanceOf(
            'PHPUnit_Framework_MockObject_MockObject',
            $mockInstance
        );

        $this->assertInstanceOf(
            'EcomDev_PHPUnit_AbstractConstraint',
            $mockInstance
        );

        $this->assertSame($mockInstance, $this->mockProxy->getMockInstance());
    }

    public function testGetMockClass()
    {
        $this->assertSame(
            get_class($this->mockProxy->getMockInstance()),
            $this->mockProxy->getMockClass()
        );
    }

    public function testExpects()
    {
        $mockProxy = new EcomDev_PHPUnit_Mock_Proxy($this, 'EcomDev_PHPUnit_AbstractConstraint');
        $mockProxy->disableOriginalConstructor()
            ->setMethods(array('compareValues'));
        $this->assertAttributeEmpty('mockInstance', $mockProxy);
        $this->assertInstanceOf(
            'PHPUnit_Framework_MockObject_Builder_InvocationMocker',
            $mockProxy->expects($this->any())->method('compareValues')
        );
        $this->assertAttributeInstanceOf('EcomDev_PHPUnit_AbstractConstraint', 'mockInstance', $mockProxy);
    }

    public function testGetInvocationMocker()
    {
        $this->assertAttributeEmpty('mockInstance', $this->mockProxy);
        $this->setExpectedException('RuntimeException', 'getMockInstance');
        $this->mockProxy->__phpunit_getInvocationMocker();

    }

    public function testGetStaticInvocationMocker()
    {
        $this->assertAttributeEmpty('mockInstance', $this->mockProxy);
        $this->setExpectedException('RuntimeException', 'getMockInstance');
        $this->mockProxy->__phpunit_getStaticInvocationMocker();
    }

    public function testVerify()
    {
        $this->assertAttributeEmpty('mockInstance', $this->mockProxy);
        $this->setExpectedException('RuntimeException', 'getMockInstance');
        $this->mockProxy->__phpunit_verify();
    }


    public function testCall()
    {
        // Just checks that call is forwarded to mocked class functionality
        $this->assertEquals(false, $this->mockProxy->compareValues('value1', 'value2'));
        $this->assertEquals(true, $this->mockProxy->compareValues('value1', 'value1'));
    }

}
