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
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Mview;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\Config
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Mview\Config\Data
     */
    protected $dataMock;

    protected function setUp()
    {
        $this->dataMock = $this->getMock(
            'Magento\Mview\Config\Data', array(), array(), '', false
        );
        $this->model = new Config(
            $this->dataMock
        );
    }

    public function testGetViews()
    {
        $this->dataMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue(['some_data']));
        $this->assertEquals(['some_data'], $this->model->getViews());
    }

    public function testGetView()
    {
        $this->dataMock->expects($this->once())
            ->method('get')
            ->with('some_view')
            ->will($this->returnValue(['some_data']));
        $this->assertEquals(['some_data'], $this->model->getView('some_view'));
    }
}
