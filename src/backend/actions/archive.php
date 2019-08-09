<?php

namespace pdima88\icms2currencyrates\backend\actions;

use pdima88\icms2currencyrates\model;
use pdima88\icms2ext\crudAction;

/**
 * @property model $model
 */
class archive extends crudAction {

    function getGrid()
    {
        $select = $this->model->archive->selectCurrencyRatesArchive();
        $currencyId = $this->getParam();
        if (isset($currencyId) && $currencyId) $select->where('currency_id = ?', $currencyId);

        $grid = [
            'id' => 'currency_archive',
            'select' => $select,
            'sort' => [
                'rate_date' => 'desc',
            ],

            'multisort' => true,
            'paging' => 10,

            'url' => $this->cms_core->uri_absolute,
            'ajax' => $this->cms_core->uri_absolute,
            'columns' => [
                'rate_date' => [
                    'title' => 'Начало периода',
                    'format' => 'date',
                    'align' => 'center',
                    'sort' => true,
                    'filter' => 'dateRange'
                ],
                'end_date' => [
                    'title' => 'Окончание периода',
                    'format' => 'date',
                    'align' => 'center',
                    'sort' => true,
                    'filter' => 'dateRange'
                ],
                'rate' => [
                    'title' => 'Курс',
                    'sort' => true,
                    'align' => 'right',

                ],
                'diff' => [
                    'title' => 'Рост/Падение',
                    'sort' => true,
                    'align' => 'right',
                    'format' => __NAMESPACE__.'\rates::formatDiff',
                ],

            ]
        ];

        if (!isset($currencyId) || !$currencyId) {
            $grid['columns'] = array_merge(['currency_id' => [
                'title' => 'Валюта',
                'filter' => 'select',
                'sort' => 'true',
                'format' => $this->model->currency->getCurrencyMap()
            ]], $grid['columns']);



            $grid['multisort'] = true;
        }

        return $grid;
    }

    public function __construct($controller, array $params)
    {
        parent::__construct($controller, $params);

        $this->pageTitle = 'Архив курсов валют';
        $this->titles['add'] = 'Добавление валюты';
        $this->titles['edit'] = 'Редактирование валюты';
        $this->titles['add_value'] = 'Добавление курса';
        $this->titles['edit_value'] = 'Редактирование курса';
        $this->messages['add'] = 'Валюта добавлена';
        $this->messages['error_edit_no_item'] = 'Значение не найдено';
    }

    public function actionIndex() {
        $res = parent::actionIndex();

        $currencies = $this->model->currency->getCurrencyList();
        $currencies = is_array($currencies) ? $currencies : [];
        $currencies = array_pad($currencies, (sizeof($currencies)+1)*-1, array(
                'id' => 0,
                'title' => LANG_ALL)
        );
        $res['data']['currency_id'] = $this->getParam();
        $res['data']['currencies'] = $currencies;
        return $res;
    }
}