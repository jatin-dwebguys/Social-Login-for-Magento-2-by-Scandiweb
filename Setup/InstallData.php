<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;

class InstallData implements InstallDataInterface
{

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * InstallSchema constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $this->createFacebookAttributes();
        $this->createTwitterAttributes();
        $this->createGoogleAttributes();
        $this->createInstagramAttributes();
        $this->createYahooAttributes();
    }

    /**
     * Create facebook attributes
     *
     * @return void
     */
    private function createFacebookAttributes()
    {
        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_facebook_id',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Facebook Id',
            ]
        );

        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_facebook_user',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Facebook User',
            ]
        );
    }

    /**
     * Create twitter attributes
     *
     * @return void
     */
    private function createTwitterAttributes()
    {
        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_twitter_id',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Twitter Id',
            ]
        );

        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_twitter_user',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Twitter User',
            ]
        );
    }

    /**
     * Create google attributes
     *
     * @return void
     */
    private function createGoogleAttributes()
    {
        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_google_id',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Google Id',
            ]
        );

        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_google_user',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Google User',
            ]
        );
    }

    /**
     * Create instagram attributes
     *
     * @return void
     */
    private function createInstagramAttributes()
    {
        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_instagram_id',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Instagram Id',
            ]
        );

        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_instagram_user',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Instagram User',
            ]
        );
    }

    /**
     * Create yahoo attributes
     *
     * @return void
     */
    private function createYahooAttributes()
    {
        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_yahoo_id',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Yahoo Id',
            ]
        );

        $this->eavSetup->addAttribute(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'scandi_yahoo_user',
            [
                'type'     => 'text',
                'visible'  => false,
                'required' => true,
                'unique'   => true,
                'system'   => 0,
                'note'     => 'Yahoo User',
            ]
        );
    }
}