<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 5/15/19
 * Time: 10:22 AM
 */

namespace Inchoo\Ticket\Ui\Component\Listing;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    private $customerRepository;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Inchoo\Ticket\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Inchoo\Ticket\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->customerRepository = $customerRepository;
    }

    /**
     * This class can be declared with virtualType
     *
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = $this->getCollection()->toArray();
        foreach ($data['items'] as $key => $item) {
            $customerName = $this->getCustomerName($item['customer_id']);
            $status = $item['status'];
            if ($status === "0") {
                $statusName = "Open";
            } elseif ($status === "1") {
                $statusName = "Closed";
            }
            $data['items'][$key]['customer_name'] = $customerName;
            $data['items'][$key]['status_name'] = $statusName;
        }
        return $data;
    }

    public function getCustomerName($id)
    {
        $customer = $this->customerRepository->getById($id);
        return ucfirst($customer->getFirstname()) . ' ' . ucfirst($customer->getLastname());
    }
}