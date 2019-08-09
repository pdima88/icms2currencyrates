<?php
namespace pdima88\icms2currencyrates\actions;

use cmsAction;
use pdima88\icms2currencyrates\frontend as currencyrates;

/**
 * @mixin currencyrates
 */
class units extends cmsAction {
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

        $units = $this->model->currency->selectUnits()
            ->where('is_visible = 1')->order('nominal')->query()->fetchAll();
        $unitsIds = [];
        foreach ($units as $unit) {
            $unitsIds[] = $unit['id'];
        }

        $endRange = date('Y-m-d',strtotime($end.' +1 month'));

        $select = $this->model->archive->select(true)
            ->where('currency_id IN (?)', $unitsIds)
            ->where('rate_date <> \'0000-00-00\'')
            ->where('rate_date >= ?', $start)
            ->where('rate_date < ?', $endRange)
            ->orWhere('currency_id IN (?)', $unitsIds)
            ->where('rate_date <> \'0000-00-00\'')
            ->where('rate_date < ?', $start)
            ->where('end_date IS NULL OR end_date > ?', $start)
            ->order('rate_date DESC');
        $rates = $select->query()->fetchAll();


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

        $this->cms_template->render('units', [
            'startMonth' => $startMonth,
            'startYear' => $startYear,
            'endMonth' => $endMonth,
            'endYear' => $endYear,
            'monthList' => $monthList,
            'yearList' => $yearList,
            'units' => $units,
            'rates' => $ratesByDate,
            'errors' => $errors,
        ]);

    }
}