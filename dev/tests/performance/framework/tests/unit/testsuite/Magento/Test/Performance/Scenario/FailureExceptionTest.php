<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Test\Performance\Scenario;

class FailureExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Performance\Scenario\FailureException
     */
    protected $_object;

    /**
     * @var \Magento\TestFramework\Performance\Scenario
     */
    protected $_scenario;

    protected function setUp()
    {
        $this->_scenario = new \Magento\TestFramework\Performance\Scenario('Title', '', array(), array(), array());
        $this->_object = new \Magento\TestFramework\Performance\Scenario\FailureException(
            $this->_scenario,
            'scenario has failed'
        );
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_scenario = null;
    }

    public function testConstructor()
    {
        $this->assertEquals('scenario has failed', $this->_object->getMessage());
    }

    public function testGetScenario()
    {
        $this->assertSame($this->_scenario, $this->_object->getScenario());
    }
}
