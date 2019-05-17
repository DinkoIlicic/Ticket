<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 5/13/19
 * Time: 11:54 AM
 */

namespace Inchoo\Ticket\Model\ResourceModel;

use Inchoo\Ticket\Api\Data\TicketInterface;

class Ticket extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize ticket Resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('inchoo_customer_ticket', TicketInterface::TICKET_ID);
    }
}
