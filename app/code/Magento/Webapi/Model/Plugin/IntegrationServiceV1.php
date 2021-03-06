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
namespace Magento\Webapi\Model\Plugin;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authz\Model\UserIdentifier\Factory as UserIdentifierFactory;
use Magento\Integration\Model\Integration as IntegrationModel;
use Magento\Authz\Service\AuthorizationV1Interface as AuthorizationInterface;

/**
 * Plugin for \Magento\Integration\Service\IntegrationV1.
 */
class IntegrationServiceV1
{
    /** @var AuthorizationInterface */
    protected $_authzService;

    /** @var UserIdentifierFactory */
    protected $_userIdentifierFactory;

    /**
     * Initialize dependencies.
     *
     * @param AuthorizationInterface $authzService
     * @param UserIdentifierFactory $userIdentifierFactory
     */
    public function __construct(AuthorizationInterface $authzService, UserIdentifierFactory $userIdentifierFactory)
    {
        $this->_authzService = $authzService;
        $this->_userIdentifierFactory = $userIdentifierFactory;
    }

    /**
     * Persist API permissions.
     *
     * @param \Magento\Integration\Service\IntegrationV1 $subject
     * @param IntegrationModel $integration
     *
     * @return IntegrationModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(\Magento\Integration\Service\IntegrationV1 $subject, $integration)
    {
        $this->_saveApiPermissions($integration);
        return $integration;
    }

    /**
     * Persist API permissions.
     *
     * @param \Magento\Integration\Service\IntegrationV1 $subject
     * @param IntegrationModel $integration
     *
     * @return IntegrationModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterUpdate(\Magento\Integration\Service\IntegrationV1 $subject, $integration)
    {
        $this->_saveApiPermissions($integration);
        return $integration;
    }

    /**
     * Add API permissions to integration data.
     *
     * @param \Magento\Integration\Service\IntegrationV1 $subject
     * @param IntegrationModel $integration
     *
     * @return IntegrationModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(\Magento\Integration\Service\IntegrationV1 $subject, $integration)
    {
        $this->_addAllowedResources($integration);
        return $integration;
    }

    /**
     * Add the list of allowed resources to the integration object data by 'resource' key.
     *
     * @param IntegrationModel $integration
     * @return void
     */
    protected function _addAllowedResources(IntegrationModel $integration)
    {
        if ($integration->getId()) {
            $userIdentifier = $this->_createUserIdentifier($integration->getId());
            $integration->setData('resource', $this->_authzService->getAllowedResources($userIdentifier));
        }
    }

    /**
     * Persist API permissions.
     *
     * Permissions are expected to be set to integration object by 'resource' key.
     * If 'all_resources' is set and is evaluated to true, permissions to all resources will be granted.
     *
     * @param IntegrationModel $integration
     * @return void
     */
    protected function _saveApiPermissions(IntegrationModel $integration)
    {
        if ($integration->getId()) {
            $userIdentifier = $this->_createUserIdentifier($integration->getId());
            if ($integration->getData('all_resources')) {
                $this->_authzService->grantAllPermissions($userIdentifier);
            } else if (is_array($integration->getData('resource'))) {
                $this->_authzService->grantPermissions($userIdentifier, $integration->getData('resource'));
            } else {
                $this->_authzService->grantPermissions($userIdentifier, array());
            }
        }
    }

    /**
     * Instantiate new user identifier for an integration.
     *
     * @param int $integrationId
     * @return UserIdentifier
     */
    protected function _createUserIdentifier($integrationId)
    {
        $userIdentifier = $this->_userIdentifierFactory->create(
            UserIdentifier::USER_TYPE_INTEGRATION,
            (int)$integrationId
        );
        return $userIdentifier;
    }

    /**
     * Process integration resource permissions after the integration is created
     *
     * @param \Magento\Integration\Service\IntegrationV1 $subject
     * @param array $integrationData Data of integration deleted
     *
     * @return array $integrationData
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(\Magento\Integration\Service\IntegrationV1 $subject, array $integrationData)
    {
        //No check needed for integration data since it cannot be empty in the parent invocation - delete
        $userIdentifier = $this->_userIdentifierFactory->create(
            UserIdentifier::USER_TYPE_INTEGRATION,
            (int)$integrationData[IntegrationModel::ID]
        );
        $this->_authzService->removePermissions($userIdentifier);
        return $integrationData;
    }
}
