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
                    'align' => 'right'
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
}