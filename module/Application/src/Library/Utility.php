<?php

namespace Application\Library;

/**
 *
 * @author Ravi
 */
class Utility
{

    public static $dateTime = null;

    const VAT_PER = 12;
    const SERVICE_TAX_PER = 10;

    public static function curDateTime()
    {
        if (self::$dateTime == null) {
            self::$dateTime = date("Y-m-d H:i:s");
        }
        return self::$dateTime;
    }

    public static function getFinalAmount(float $amt = 0.00, $details = false)
    {
        $serviceTax = (self::SERVICE_TAX_PER / 100) * $amt;
        $vat = (self::VAT_PER / 100) * $serviceTax;
        $finalAmt = $serviceTax + $vat + $amt;
        if ($details == true) {
            return array(
                'service_tax' => $serviceTax,
                'vat' => $vat,
                'basic_amount' => $amt,
                'final_amount' => $finalAmt,
            );
        }

        return $finalAmt;
    }

}
