<?php

class BOIPAAjaxModuleFrontController extends ModuleFrontController
{

    /** @var boipa */
    private $boipa;

    public function postProcess()
    {
        $this->boipa = new BOIPA();
    }

    public function initContent()
    {
        parent::initContent();
        $this->ajax = true;
    }

    public function displayAjax()
    {
        $typeRequest = Tools::getValue('typerequest');
        $statusPost = Tools::getValue('status');
        $token = Tools::getValue('token');
        $retry = Tools::getValue('retry');
        $merchantTxId = Tools::getValue('merchantTxId');

        if($typeRequest==='payment')
        {
            $statusPayment = str_replace('"', '', $statusPost);

            if($this->boipa->getPaymentStatus($token)->result!==$statusPayment){
                $statusPayment = 'failure';
            }
            $cart = $this->context->cart->id;

            if (!$cart) {
                $order = new Order((int)$retry);
                $cart = $order->id_cart;
            }
            $amountPaid = (float) Tools::ps_round((float) $this->context->cart->getOrderTotal(true), 2);
            $id_evo_payment = $this->boipa->addOrderPaymentToDB($cart, $token, $statusPayment, $amountPaid);
            if ($id_evo_payment !== false) {
                echo json_encode($id_evo_payment);
                exit;
            }

            echo json_encode('error_id_payment');
            exit;
        }
		
		if($typeRequest === 'redirect_payment'){
            $cart = $this->context->cart;
            $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
            $displayName = $this->module->displayName;
            $customer = new Customer($cart->id_customer);
            
            $inProgress = BOIPA::MAP_STATUSES['inprogress'];
            $order_state = (int)Configuration::get($inProgress);
            
            $cartId = (int)$cart->id;
            $currency = $this->context->currency;
            
            $this->module->validateOrder($cartId, $order_state, $total, $displayName, null, array(), null, false, $customer->secure_key);
            
            $orderId = $this->module->currentOrder;
            $id_evo_payment = $this->boipa->addOrderPaymentToDB($cartId, $merchantTxId, $inProgress, $total);
            $this->boipa->updateRawIdOrderInEvoPayment($id_evo_payment, $orderId);
            
            $order = new Order($orderId);
            $order->addOrderPayment($total, $displayName, $id_evo_payment, $currency, date('Y-m-d H:i:s'));

            echo '1';
            exit;
        }

        echo json_encode('error');
        exit;
    }
}