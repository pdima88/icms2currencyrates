<?php

namespace pdima88\icms2currencyrates;

use Exception;
use pdima88\icms2currencyrates\tables\table_archive;
use pdima88\icms2currencyrates\tables\table_currency;
use pdima88\icms2ext\Model as BaseModel;

/**
 * @property table_currency $currency
 * @property table_archive $archive
 */
class model extends BaseModel
{
    const TABLE_CURRENCY = 'currency_rates';
    const TABLE_ARCHIVE = 'currency_archive';
    const UPDATE_URL = 'http://cbu.uz/ru/arkhiv-kursov-valyut/json/';

	function getUpdates() {
        if (!function_exists('curl_init')){

            $data = @file_get_contents(self::UPDATE_URL);

        } else {

            $curl = curl_init();

            if(strpos(self::UPDATE_URL, 'https') !== false){
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }
            curl_setopt($curl, CURLOPT_URL, self::UPDATE_URL);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_POST, true);
            //curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

            $data = curl_exec($curl);

            curl_close($curl);

        }

        $curRates = json_decode($data, true);
        return $curRates;
    }

    function updateRates() {
        $rates = $this->getUpdates();
        if ($rates) {
            foreach ($rates as $r) {
                $a = null;
                $code = $r['Ccy'];
                $date = date_iso($r['Date']);
                if (!$code) throw new Exception('Invalid currency data');
                $cur = $this->currency->getByCode($code);
                if (!$cur) {
                    $cur = $this->currency->createRow([
                        'code' => $code
                    ]);
                } else {
                    if ($cur->rate_date != date_iso($r['Date'])) {
                        $this->archive->saveCurrencyRate($cur, $date);
                    }
                }

                $cur->rate = $r['Rate'];
                $cur->diff = $r['Diff'];
                $cur->rate_date = $date;
                $cur->nominal = $r['Nominal'];
                $cur->num_code = $r['Code'];
                if (empty($cur->name)) $cur->name = $r['CcyNm_RU'];
                if (empty($cur->name_uz)) $cur->name_uz = $r['CcyNm_UZC'];
                if (empty($cur->name_en)) $cur->name_en = $r['CcyNm_EN'];

                $cur->save();
                $this->archive->saveCurrencyRate($cur);
            }
        }
    }

    function __get($name)
    {
        if ($name == 'currency' || $name == 'archive') {
            return $this->getTable($name);
        }
        throw new Exception('Unknown property '.$name);
    }

}