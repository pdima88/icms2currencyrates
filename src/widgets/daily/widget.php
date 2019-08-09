<?php

namespace pdima88\icms2currencyrates\widgets\daily;

use cmsWidget;
use pdima88\icms2currencyrates\frontend;
use cmsCore;
use cmsController;
use pdima88\icms2currencyrates\tables\table_currency;
use pdima88\icms2currencyrates\model;

class widget extends cmsWidget {

	public $is_cacheable = false;

    public function run() {

	    $this->setTemplate('daily');
		$rates = frontend::getInstance()->model->currency->fetchAll(['type = ?' => table_currency::TYPE_CURRENCY_PRIMARY, 'is_visible' => 1]);
		$units = frontend::getInstance()->model->currency->fetchAll(['type = ?' => table_currency::TYPE_UNITS, 'is_visible' => 1]);
		//$rates = model::getInstance()->currency->fetchAll(['type = ?' => table_currency::TYPE_CURRENCY_PRIMARY]);

        return array(
			'rates' => $rates,
            'units' => $units,
        );

    }

}
