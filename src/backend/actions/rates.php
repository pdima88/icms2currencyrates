<?php

namespace pdima88\icms2currencyrates\backend\actions;

use cmsUser;
use Nette\Utils\Html;
use pdima88\icms2currencyrates\model;
use pdima88\icms2ext\crudAction;
use pdima88\icms2ext\GridHelper;

/**
 * @property model $model Model
 */
class rates extends crudAction 
{
    function getGrid()
    {
        $select = $this->model->currency->selectCurrencyRates()->columns(['is_primary' => '(type > 0)']);

        $grid = [
            'id' => 'currency',
            'select' => $select,
            'sort' => [
                'type' => 'desc',
            ],

            'multisort' => true,
            'paging' => 10,
            'actions' => GridHelper::getActions([
                'edit' => [
                    'title' => 'Редактировать',
                ]
            ]),
            'url' => $this->cms_core->uri_absolute,
            'ajax' => $this->cms_core->uri_absolute,
            'columns' => [
                'num_code' => [
                    'title' => 'Числовой код валюты',
                    'width' => 70,
                    'sort' => true,
                    'filter' => 'equal'
                ],
                'code' => [
                    'title' => 'Символьный код валюты',
                    'width' => 70,
                    'sort' => true,
                    'filter' => 'equal'
                ],
                'nominal' => [
                    'title' => 'Номинал',
                    'align' => 'right',
                    'width' => 70,
                    'sort' => true,
                ],
                'name' => [
                    'title' => 'Название',
                    'sort' => true,
                    'filter' => 'text'
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
                'rate_date' => [
                    'title' => 'Дата',
                    'format' => 'date',
                    'align' => 'center',
                    'sort' => true,
                    'filter' => 'dateRange'
                ],
                'is_primary' => [
                    'title' => 'Основные валюты',
                    'format' => 'checkbox',
                    'sort' => true,
                    'filter' => 'select',
                    'align' => 'center'
                ],
                'is_visible' => [
                    'title' => 'Показывать',
                    'format' => 'checkbox',
                    'sort' => true,
                    'filter' => 'select',
                    'align' => 'center'
                ]



            ]
        ];


        return $grid;
    }

    function actionUpdate() {
        $this->model->updateRates();
        cmsUser::addSessionMessage('Курсы валют обновлены', 'success');
        $this->redirectBack();
    }

    public function __construct($controller, array $params)
    {
        parent::__construct($controller, $params);

        $this->pageTitle = 'Курсы валют ЦБ РУз';
        $this->titles['add'] = 'Добавление валюты';
        $this->titles['edit'] = 'Редактирование валюты';
        $this->titles['add_rate'] = 'Добавление курса';
        $this->titles['edit_rate'] = 'Редактирование курса';
        $this->messages['add'] = 'Валюта добавлена';
        $this->messages['error_edit_no_item'] = 'Значение не найдено';
    }

    static function formatDiff($value, $row) {
        if (!isset($value)) return '';
        $r = '';
        if ($value > 0) {
            $r = '<i class="glyphicon glyphicon-arrow-up" style="color:green"></i> ';
        } elseif ($value < 0) {
            $r = '<i class="glyphicon glyphicon-arrow-down" style="color:red"></i> ';
        }
        $r.=$value;
        if (isset($row['rate'])) {
            $old = $row['rate'] - $value;
            if ($old) {
                $perc = $value/$old*100;
                $r.= ' <span style="font-size: smaller; color:#888">('.(($perc>0)?'+':'').number_format($perc,2).'%)</span>';
            }
        }
        return $r;
    }
}