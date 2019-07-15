<?php
namespace pdima88\icms2currencyrates\backend\forms;

use cmsCore;
use cmsForm;
use fieldCheckbox;
use fieldString;
use fieldHtml;
use fieldListMultiple;
use fieldNumber;

class form_unit extends cmsForm {

    public function init(){

        return [
            [
                'type' => 'fieldset',
                'title' => 'Единица',
                'childs' => [
                    new fieldString('code', [
                        'title' => 'Символьный код',
                    ]),

                    new fieldString('name', [
                        'title' => 'Название (на русском)',
                        'rules' => [
                            ['required'],
                        ]
                    ]),

                    new fieldString('name_uz', [
                        'title' => 'Название (на узбекском)',
                    ]),

                    new fieldString('name_en', [
                        'title' => 'Название (на английском)',
                    ]),

                    new fieldString('suffix', [
                        'title' => 'Суффикс',
                    ]),

                    new fieldString('suffix_uz', [
                        'title' => 'Суффикс (на узбекском)',
                    ]),

                    new fieldString('suffix_en', [
                        'title' => 'Суффикс (на английском)',
                    ]),

                    new fieldCheckbox('is_visible', [
                        'title' => 'Показывать'
                    ])
                ]
            ],
        ];
    }

}
