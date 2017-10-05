<?php

namespace ProjectEight\Magento\Command\System\Report\Payments;

use N98\Magento\Command\CommandAware;
use ProjectEight\Magento\Command\System\Report\Result;
use ProjectEight\Magento\Command\System\Report\ResultCollection;
use ProjectEight\Magento\Command\System\Report\SimpleReport;
use Symfony\Component\Console\Command\Command;
use ProjectEight\Magento\Command\System\ReportCommand;

/**
 * Class PaymentMethodsReport
 *
 * @package ProjectEight\Magento\Command\System\Report\Payment
 */
class PaymentMethodsReport implements SimpleReport, CommandAware
{
    /**
     * @var ReportCommand
     */
    protected $reportCommand;

    /**
     * @param ResultCollection $results
     *
     * @return void
     */
    public function report(ResultCollection $results)
    {
        $result = $results->createResult();
        $result->setStatus(Result::STATUS_INFO);

        $table = "";
        foreach ($this->getPaymentMethodsList() as $method) {
            $table .= "<info>{$method['title']}</info>: {$method['status']}\n";
        }

        $result->setMessage("{$table}");
    }

    /**
     * @param Command $command
     */
    public function setCommand(Command $command)
    {
        $this->reportCommand = $command;
    }

    /**
     * @return array
     */
    protected function getPaymentMethodsList()
    {
        $list = [];
        $methods = \Mage::app()->getConfig()->getNode('default/payment');
        foreach ($methods->children() as $method) {
            /** @var \Mage_Core_Model_Config_Element $method */
            if($method->active == "0") {
                continue;
            }
            $list[] = [
                'code'   => $method->getName(),
                'title'  => (string)$method->title,
                'status'  => ($method->active == "0") ? '<comment>Disabled</comment>' : '<comment>Enabled</comment>',
            ];
        }

        return $list;
    }
}