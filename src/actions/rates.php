<?php
namespace pdima88\icms2currencyrates\actions;

use cmsAction;
use pdima88\icms2currencyrates\frontend as currencyrates;
use pdima88\icms2currencyrates\tables\table_currency;

/**
 * @mixin currencyrates
 */
class rates extends cmsAction {
    public function run() {
        $errors = [];
        $startMonth = $this->request->get('start_month');
        $startYear = $this->request->get('start_year');
        $endMonth = $this->request->get('end_month');
        $endYear = $this->request->get('end_year');
        
        $end = date_iso(($endMonth && $endYear) ? $endYear.'-'.$endMonth.'-01' : date('Y-m-01'));
        $start = date_iso(($startMonth && $startYear) ? $startYear.'-'.$startMonth.'-01' : date('Y-m-01', strtotime($end.'-2 months')));
        if ($end < $start) {
            $errors[] = 'Дата окончания периода меньше даты начала!';
        }
        $startMonth = date_month($start);
        $startYear = date_year($start);
        $endMonth = date_month($end);
        $endYear = date_year($end);

        $months = 'Январь,Февраль,Март,Апрель,Май,Июнь,Июль,Август,Сентябрь,Октябрь,Ноябрь,Декабрь';
        $monthList = explode(',', ','.$months);
        unset($monthList[0]);

        $minDate = $this->model->archive->getMinDate();
        $beginYear = date_year($minDate);

        $currentYear = date('Y');
        $yearList = [];
        for ($i = $currentYear; $i >= $beginYear; $i--) {
            $yearList[$i] = $i;
        }

        $selectedCurrencies = $this->request->get('currency');
        if (!$selectedCurrencies) {
            $selectedCurrencies = $this->model->currency->selectAs()
                ->where('type = ?', table_currency::TYPE_CURRENCY_PRIMARY)
                ->where('is_visible = 1')
                ->columns(['code'])->query()->fetchAll(\Zend_Db::FETCH_COLUMN, 0);
        }

        $selectedCurrencyArr = [];
        $currencies = $this->model->currency->selectCurrencyRates()
            ->where('type <> ?', table_currency::TYPE_CURRENCY_DEFAULT)
            ->where('is_visible = 1')->order('type DESC')->order('name')->query()->fetchAll();
        foreach ($currencies as $i => $currency) {
            if ($currency['code'] == 'XDR') {
                $currencies[$i]['country'] = false;
            } else {
                $currencies[$i]['country'] = substr($currency['code'], 0, 2);
            }
            if (in_array($currency['code'], $selectedCurrencies)) {
                $currencies[$i]['checked'] = true;
                $selectedCurrencyArr[$currency['id']] = $currencies[$i];
            }
        }

        $selectedCurrencyIds = array_keys($selectedCurrencyArr);
        $endRange = date('Y-m-d',strtotime($end.' +1 month'));
        if (!empty($selectedCurrencyIds)) {
            $select = $this->model->archive->select(true)
                ->where('currency_id IN (?)', $selectedCurrencyIds)
                ->where('rate_date <> \'0000-00-00\'')
                ->where('rate_date >= ?', $start)
                ->where('rate_date < ?', $endRange)
                ->orWhere('currency_id IN (?)', $selectedCurrencyIds)
                ->where('rate_date <> \'0000-00-00\'')
                ->where('rate_date < ?', $start)
                ->where('end_date IS NULL OR end_date > ?', $start)
                ->order('rate_date DESC');
            $rates = $select->query()->fetchAll();

        }
        $ratesByDate = [];

        if (!$errors) {
            $lastRate = [];
            foreach ($rates as $rate) {
                $date = $rate['rate_date'];
                $curId = $rate['currency_id'];
                if (isset($lastRate[$curId]) && !isset($lastRate[$curId]['diff'])) {
                    $lastRate[$curId]['diff'] = round($lastRate[$curId]['rate'] - $rate['rate'], 2);
                    $ratesByDate[$lastRate[$curId]['rate_date']][$rate['currency_id']] = $lastRate[$curId];
                }
                if ($date < $start && ($lastRate[$curId]['rate_date'] ?? '9999-01-01') <= $start) continue;
                if (!isset($ratesByDate[$date])) {
                    $ratesByDate[$date] = [];
                }
                $ratesByDate[$date][$rate['currency_id']] = $rate;
                $lastRate[$curId] = $rate;
            }
        }

        $this->cms_template->render('rates', [
            'startMonth' => $startMonth,
            'startYear' => $startYear,
            'endMonth' => $endMonth,
            'endYear' => $endYear,
            'monthList' => $monthList,
            'yearList' => $yearList,
            'currencies' => $currencies,
            'rates' => $ratesByDate,
            'selectedCurrencies' => $selectedCurrencyArr,
            'errors' => $errors,
        ]);
         
    }
}