<?php

/**
 * TechDivision\Import\Customer\Observers\CleanUpObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Customer\Observers;

use TechDivision\Import\Customer\Services\CustomerBunchProcessorInterface;
use TechDivision\Import\Customer\Utils\ColumnKeys;

/**
 * An observer implementation that handles the process to import customer bunches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class CleanUpObserver extends AbstractCustomerImportObserver
{

    /**
     * The customer bunch processor instance.
     *
     * @var \TechDivision\Import\Customer\Services\CustomerBunchProcessorInterface
     */
    protected $customerBunchProcessor;

    /**
     * Initialize the observer with the passed customer bunch processor instance.
     *
     * @param \TechDivision\Import\Customer\Services\CustomerBunchProcessorInterface $customerBunchProcessor The customer bunch processor instance
     */
    public function __construct(CustomerBunchProcessorInterface $customerBunchProcessor)
    {
        $this->customerBunchProcessor = $customerBunchProcessor;
    }

    /**
     * Return's the customer bunch processor instance.
     *
     * @return \TechDivision\Import\Customer\Services\CustomerBunchProcessorInterface The customer bunch processor instance
     */
    protected function getCustomerBunchProcessor()
    {
        return $this->customerBunchProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // load email and website code
        $email = $this->getValue(ColumnKeys::EMAIL);
        $website = $this->getValue(ColumnKeys::WEBSITE);

        // add the customer identifier => entity ID mapping
        $this->addCustomerIdentifierEntityIdMapping($email, $website);

        // clean-up the repositories etc. to free memory
        $this->getCustomerBunchProcessor()->cleanUp();

        // temporary persist the last customer identifier
        $this->setLastIdentifier(array($email, $website));
    }

    /**
     * Add the passed mail address/website code => entity ID mapping.
     *
     * @param string $email   The mail address of the customer
     * @param string $website The website code the customer is bound to
     *
     * @return void
     */
    public function addCustomerIdentifierEntityIdMapping($email, $website)
    {
        $this->getSubject()->addCustomerIdentifierEntityIdMapping($email, $website);
    }

    /**
     * Sets the customer identifier of the last imported customer.
     *
     * @param array $identifier The unique customer identifier
     *
     * @return void
     */
    protected function setLastIdentifier(array $identifier)
    {
        $this->getSubject()->setLastIdentifier($identifier);
    }
}
