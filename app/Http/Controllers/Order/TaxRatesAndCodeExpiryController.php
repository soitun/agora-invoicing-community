<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\CartController;
use App\Model\Order\Invoice;
use App\Model\Order\Order;
use App\Model\Payment\Tax;
use App\Model\Payment\TaxClass;
use App\Model\Payment\TaxOption;
use App\User;
use Bugsnag;

class TaxRatesAndCodeExpiryController extends Controller
{
    /**
     *Tax When state is not empty.
     */
    public function getTaxWhenState($user_state, $productid, $origin_state)
    {
        $cartController = new CartController();
        $c_gst = $user_state->c_gst;
        $s_gst = $user_state->s_gst;
        $i_gst = $user_state->i_gst;
        $ut_gst = $user_state->ut_gst;
        $state_code = $user_state->state_code;
        if ($state_code == $origin_state) {//If user and origin state are same
             $taxClassId = TaxClass::where('name', 'Intra State GST')->pluck('id')->toArray(); //Get the class Id  of state
               if ($taxClassId) {
                   $taxes = $cartController->getTaxByPriority($taxClassId);
                   $value = $cartController->getValueForSameState($productid, $c_gst, $s_gst, $taxClassId, $taxes);
               } else {
                   $taxes = [0];
               }
        } elseif ($state_code != $origin_state && $ut_gst == 'NULL') {//If user is from other state

            $taxClassId = TaxClass::where('name', 'Inter State GST')->pluck('id')->toArray(); //Get the class Id  of state
            if ($taxClassId) {
                $taxes = $cartController->getTaxByPriority($taxClassId);
                $value = $cartController->getValueForOtherState($productid, $i_gst, $taxClassId, $taxes);
            } else {
                $taxes = [0];
            }
        } elseif ($state_code != $origin_state && $ut_gst != 'NULL') {//if user from Union Territory
        $taxClassId = TaxClass::where('name', 'Union Territory GST')->pluck('id')->toArray(); //Get the class Id  of state
         if ($taxClassId) {
             $taxes = $cartController->getTaxByPriority($taxClassId);
             $value = $cartController->getValueForUnionTerritory($productid, $c_gst, $ut_gst, $taxClassId, $taxes);
         } else {
             $taxes = [0];
         }
        }

        return ['taxes'=>$taxes, 'value'=>$value];
    }

    /**
     *Tax When from other Country.
     */
    public function getTaxWhenOtherCountry($geoip_state, $geoip_country, $productid)
    {
        $cartController = new CartController();
        $taxClassId = Tax::where('state', $geoip_state)->orWhere('country', $geoip_country)->pluck('tax_classes_id')->first();
        $value = '';
        $rate = '';
        if ($taxClassId) { //if state equals the user State

            $taxes = $cartController->getTaxByPriority($taxClassId);

            // $taxes = $this->cartController::getTaxByPriority($taxClassId);
            $value = $cartController->getValueForOthers($productid, $taxClassId, $taxes);
            $rate = $value;
        } else {//if Tax is selected for Any State Any Country
            $taxClassId = Tax::where('country', '')->where('state', 'Any State')->pluck('tax_classes_id')->first();
            if ($taxClassId) {
                $taxes = $cartController->getTaxByPriority($taxClassId);
                $value = $cartController->getValueForOthers($productid, $taxClassId, $taxes);
                $rate = $value;
            } else {
                $taxes = [0];
            }
        }

        return ['taxes'=>$taxes, 'value'=>$value, 'rate'=>$rate];
    }

    /**
     * Get tax when enabled.
     */
    public function getTaxWhenEnable($productid, $taxs, $userid)
    {
        $rate = $this->getRate($productid, $taxs, $userid);
        $taxs = ([$rate['taxs']['0']['name'], $rate['taxs']['0']['rate']]);

        return $taxs;
    }

    /**
     * GeT Total Rate.
     */
    public function getTotalRate($taxClassId, $productid, $taxs)
    {
        $cartController = new CartController();
        $taxs = $cartController->getTaxByPriority($taxClassId);
        $value = $cartController->getValueForOthers($productid, $taxClassId, $taxs);
        if ($value == 0) {
            $status = 0;
        }
        $rate = $value;

        return ['rate'=>$rate, 'taxes'=>$taxs];
    }

    /**
     * Get Grandtotal.
     **/
    public function getGrandTotal($code, $total, $cost, $productid, $currency)
    {
        if ($code) {
            $grand_total = $this->checkCode($code, $productid, $currency);
        } else {
            if (!$total) {
                $grand_total = $cost;
            } else {
                $grand_total = $total;
            }
        }

        return $grand_total;
    }

    /**
     * Get Message on Invoice Generation.
     **/
    public function getMessage($items, $user_id)
    {
        if ($items) {
            $this->sendmailClientAgent($user_id, $items->invoice_id);
            $result = ['success' => \Lang::get('message.invoice-generated-successfully')];
        } else {
            $result = ['fails' => \Lang::get('message.can-not-generate-invoice')];
        }

        return $result;
    }

    /**
     * get Subtotal.
     */
    public function getSubtotal($user_currency, $cart)
    {
        if ($user_currency == 'INR') {
            $subtotal = \App\Http\Controllers\Front\CartController::rounding($cart->getPriceSumWithConditions());
        } else {
            $subtotal = \App\Http\Controllers\Front\CartController::rounding($cart->getPriceSumWithConditions());
        }

        return $subtotal;
    }

    public function calculateTotal($rate, $total)
    {
        try {
            $rates = explode(',', $rate);
            $rule = new TaxOption();
            $rule = $rule->findOrFail(1);
            if ($rule->inclusive == 0) {
                foreach ($rates as $rate1) {
                    if ($rate1 != '') {
                        $rateTotal = str_replace('%', '', $rate1);
                        $total += $total * ($rateTotal / 100);
                    }
                }
            }

            return intval(round($total));
        } catch (\Exception $ex) {
            Bugsnag::notifyException($ex);

            throw new \Exception($ex->getMessage());
        }
    }

    public function whenDateNotSet($start, $end)
    {
        //both not set, always true
        if (($start == null || $start == '0000-00-00 00:00:00') &&
         ($end == null || $end == '0000-00-00 00:00:00')) {
            return 'success';
        }
    }

    public function whenStartDateSet($start, $end, $now)
    {
        //only starting date set, check the date is less or equel to today
        if (($start != null || $start != '0000-00-00 00:00:00')
         && ($end == null || $end == '0000-00-00 00:00:00')) {
            if ($start <= $now) {
                return 'success';
            }
        }
    }

    public function whenEndDateSet($start, $end, $now)
    {
        //only ending date set, check the date is greater or equel to today
        if (($end != null || $end != '0000-00-00 00:00:00') && ($start == null || $start == '0000-00-00 00:00:00')) {
            if ($end >= $now) {
                return 'success';
            }
        }
    }

    public function whenBothSet($start, $end, $now)
    {
        //both set
        if (($end != null || $start != '0000-00-00 00:00:00') && ($start != null || $start != '0000-00-00 00:00:00')) {
            if ($end >= $now && $start <= $now) {
                return 'success';
            }
        }
    }

    public function getPrice($price, $price_model)
    {
        if ($price == '') {
            $price = $price_model->sales_price;
            if (!$price) {
                $price = $price_model->price;
            }
        }

        return $price;
    }

    public function checkExecution($invoiceid)
    {
        try {
            $response = false;
            $invoice = Invoice::find($invoiceid);
            // dd($invoice);
            $order = Order::where('invoice_id', $invoiceid);
            // dd($order);
            $order_invoice_relation = $invoice->orderRelation()->first();
            if ($order_invoice_relation) {
                $response = true;
            } elseif ($order->get()->count() > 0) {
                $response = true;
            }

            return $response;
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);

            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function invoiceContent($invoiceid)
    {
        $invoice = $this->invoice->find($invoiceid);
        $items = $invoice->invoiceItem()->get();
        $content = '';
        if ($items->count() > 0) {
            foreach ($items as $item) {
                $content .= '<tr>'.
                        '<td style="border-bottom: 1px solid#ccc; color: #333; font-family: Arial,sans-serif; font-size: 14px; line-height: 20px; padding: 15px 8px;" valign="top">'.$invoice->number.'</td>'.
                        '<td style="border-bottom: 1px solid#ccc; color: #333; font-family: Arial,sans-serif; font-size: 14px; line-height: 20px; padding: 15px 8px;" valign="top">'.$item->product_name.'</td>'.
                        '<td style="border-bottom: 1px solid#ccc; color: #333; font-family: Arial,sans-serif; font-size: 14px; line-height: 20px; padding: 15px 8px;" valign="top">'.$this->currency($invoiceid).' '.$item->subtotal.'</td>'.
                        '</tr>';
            }
        }

        return $content;
    }

    public function sendInvoiceMail($userid, $number, $total, $invoiceid)
    {

        //user
        $users = new User();
        $user = $users->find($userid);
        //check in the settings
        $settings = new \App\Model\Common\Setting();
        $setting = $settings->where('id', 1)->first();
        $invoiceurl = $this->invoiceUrl($invoiceid);
        //template
        $templates = new \App\Model\Common\Template();
        $temp_id = $setting->invoice;
        $template = $templates->where('id', $temp_id)->first();
        $from = $setting->email;
        $to = $user->email;
        $subject = $template->name;
        $data = $template->data;
        $replace = [
            'name'       => $user->first_name.' '.$user->last_name,
            'number'     => $number,
            'address'    => $user->address,
            'invoiceurl' => $invoiceurl,
            'content'    => $this->invoiceContent($invoiceid),
            'currency'   => $this->currency($invoiceid),
        ];
        $type = '';
        if ($template) {
            $type_id = $template->type;
            $temp_type = new \App\Model\Common\TemplateType();
            $type = $temp_type->where('id', $type_id)->first()->name;
        }
        //dd($type);
        $templateController = new \App\Http\Controllers\Common\TemplateController();
        $mail = $templateController->mailing($from, $to, $data, $subject, $replace, $type);

        return $mail;
    }

    public function invoiceUrl($invoiceid)
    {
        $url = url('my-invoice/'.$invoiceid);

        return $url;
    }
}
