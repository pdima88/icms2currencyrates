<?php
namespace pdima88\icms2currencyrates\backend\forms;

use cmsForm;
use fieldList;
use fieldDate;
use fieldNumber;
use pdima88\icms2currencyrates\frontend as currencyrates;

/**
 * @property currencyrates $controller
 */
class form_unit_value extends cmsForm {

    public function init(){

        return [
            [
                'type' => 'fieldset',
                'title' => 'Значение единицы',
                'childs' => [
                    new fieldList('unit_id', [
                        'title' => 'Единица',
                        'items' => $this->controller->model->currency->getUnitsMapWithSuffix()
                    ]),

                    new fieldNumber('value', [
                        'title' => 'Значение',
                        'rules' => [
                            ['required'],
                        ],
                    ]),

                    new fieldDate('rate_date', [
                        'title' => 'Дата начала действия',
                    ]),

                    /*new fieldDate('end_date', [
                        'title' => 'Дата окончания действия',
                        'hint' => 'Оставьте пустым, если значение действует на текущий момент',
                    ]),*/
                ]
            ],
        ];
    }

}
