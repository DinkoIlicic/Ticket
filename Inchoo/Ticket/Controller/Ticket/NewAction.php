<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 5/13/19
 * Time: 2:36 PM
 */

namespace Inchoo\Ticket\Controller\Ticket;

use Inchoo\Ticket\Api\Data\TicketInterface;
use Inchoo\Ticket\Api\TicketRepositoryInterface;
use Magento\Framework\App\Action\Context;

class NewAction extends CustomerAction
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * NewAction constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     * @param TicketRepositoryInterface $ticketRepository
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        TicketRepositoryInterface $ticketRepository,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context, $session, $url, $ticketRepository);
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->ticketRepository = $ticketRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->request = $request;
    }

    public function execute()
    {
        $this->isCustomerLoggedIn();
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->redirectToIndex();
        }

        try {
            $customerId = (int) $this->session->getCustomerId();
            $websiteId = (int) $this->storeManager->getStore()->getWebsiteId();
            $subject = $this->escaper->escapeHtml($this->request->getPostValue('title'));
            $message = $this->escaper->escapeHtml($this->request->getPostValue('content'));
            if (empty($subject) || empty($message)) {
                $this->messageManager->addErrorMessage('Form fields cannot be empty!');
                return $this->redirectToIndex();
            }

            $array = [
                TicketInterface::CUSTOMER_ID => $customerId,
                TicketInterface::WEBSITE_ID => $websiteId,
                TicketInterface::SUBJECT => $subject,
                TicketInterface::MESSAGE => $message
            ];
            $this->ticketRepository->addTicket($array);
            $this->_eventManager->dispatch(
                'inchoo_ticket_created',
                ['ticketData' => $array]
            );
            $this->messageManager->addSuccessMessage(__('Ticket created!'));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Ticket not created!'));
        }

        return $this->redirectToIndex();
    }
}