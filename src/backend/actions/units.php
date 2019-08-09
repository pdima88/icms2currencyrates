<?php

namespace pdima88\icms2currencyrates\backend\actions;

use cmsCore;
use cmsTemplate;
use pdima88\icms2currencyrates\model;
use pdima88\icms2ext\crudAction;
use pdima88\icms2currencyrates\tables\table_currency;
use pdima88\icms2ext\GridHelper;

/**
 * @property model $model
 */
class units extends crudAction {

    const FORM_UNIT_VALUE = 'unit_value';
    const FORM_UNIT = 'unit';

    function getGrid()
    {
        $unitId = $this->getParam();
        if (isset($unitId) && $unitId) {
            $select = $this->model->archive->selectUnitsArchive();
            $select->where('currency_id = ?', $unitId);

            $unit = $this->model->currency->getById($unitId);

            $grid = [
                'id' => 'units',
                'select' => $select,
                'sort' => [
                    'rate_date' => 'desc',
                ],
                'paging' => 10,
                'actions' => GridHelper::getActions([
                    'edit' => [
                        'title' => 'Редактировать',
                        'href' => href_to($this->pageUrl, 'edit_value', '{id}') . '?back={returnUrl}'
                    ],
                    'delete' => [
                        'title' => 'Удалить',
                        'href' => '',
                        'confirmDelete' => true
                    ]
                ]),
                'url' => $this->cms_core->uri_absolute,
                'ajax' => $this->cms_core->uri_absolute,
                'delete' => href_to($this->pageUrl, 'delete_value', '{id}'). '?back={returnUrl}',
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
                        'title' => 'Значение'.($unit ? ', '.$unit->suffix : ''),
                        'sort' => true,
                        'align' => 'right',
                        'format' => function ($value) use($unit) {
                            return number_format($value, (floatval($value) == intval($value)) ? 0 : 2,'.',' ')
                                .($unit ? ' '.$unit->suffix : '');
                        }
                    ],
                    'diff' => [
                        'title' => 'Рост/Падение',
                        'sort' => true,
                        'align' => 'right',
                        'format' => __NAMESPACE__.'\rates::formatDiff',
                    ],
                ]
            ];
        } else {
            $select = $this->model->currency->selectUnits();

            $grid = [
                'id' => 'units',
                'select' => $select,
                'sort' => [
                    'rate_date' => 'desc',
                ],
                'paging' => 10,
                'actions' => GridHelper::getActions([
                    'add' => [
                        'title' => 'Новое значение',
                        'href' => href_to($this->pageUrl, 'add_value', '{id}') . '?back={returnUrl}'
                    ],
                    'edit' => [
                        'title' => 'Редактировать',
                        'href' => href_to($this->pageUrl, 'edit', '{id}') . '?back={returnUrl}'
                    ],
                    'delete' => [
                        'title' => 'Удалить',
                        'href' => '',
                        'confirmDelete' => true
                    ]
                ]),
                'url' => $this->cms_core->uri_absolute,
                'delete' => href_to($this->pageUrl, 'delete', '{id}'). '?back={returnUrl}',
                'ajax' => $this->cms_core->uri_absolute,
                'columns' => [
                   'name' => [
                        'title' => 'Единица',
                        'filter' => 'text',
                        'sort' => 'true',
                    ],
                    'rate' => [
                        'title' => 'Значение',
                        'sort' => true,
                        'align' => 'right',
                    ],
                    'suffix' => [
                        'title' => 'Ед.изм.',
                        'align' => 'left',
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
                    'is_visible' => [
                        'title' => 'Показывать',
                        'format' => 'checkbox',
                        'sort' => true,
                        'filter' => 'select',
                        'align' => 'center'
                    ]
                ]
            ];
        }

        return $grid;
    }

    public function actionIndex() {
        $res = parent::actionIndex();

        $units = $this->model->currency->getUnitsList();
        $units = is_array($units) ? $units : [];
        $units = array_pad($units, (sizeof($units)+1)*-1, array(
                'id' => 0,
                'title' => LANG_ALL)
        );
        $res['data']['unit_id'] = $this->getParam();
        $res['data']['units'] = $units;
        return $res;
    }



    public function __construct($controller, array $params)
    {
        parent::__construct($controller, $params);

        $this->pageTitle = 'Другие единицы';
        $this->titles['add'] = 'Добавление единицы';
        $this->titles['edit'] = 'Редактирование единицы';
        $this->titles['add_value'] = 'Добавление значения';
        $this->titles['edit_value'] = 'Редактирование значения';
        $this->messages['add'] = 'Единица добавлена';
        $this->messages['error_edit_no_item'] = 'Значение не найдено';
    }

    public function actionUnitInfo() {
        $id = $this->getParam(0);
        if (!$id) { exit; }

        $unit = table_currency::getById($id);

        if (!$unit) {
            echo 'Единица не найдена!';
            exit;
        }        

        $tpl = cmsTemplate::getInstance();
        return $tpl->render('backend/unit_info', array(
            'unit' => $unit->toArray()
        ));
    }

    public function setForm($formName) {
        $this->formName = $formName;
        if ($formName == self::FORM_UNIT_VALUE) {
            $this->tableName = model::TABLE_ARCHIVE;
        } elseif ($formName == self::FORM_UNIT) {
            $this->tableName = model::TABLE_CURRENCY;
        } else {
            throw new Exception('Unknown form name: '.$formName);
        }
    }

    public function actionAdd()
    {
        $this->setForm(self::FORM_UNIT);
        return parent::actionAdd();
    }
    
    public function actionEdit($id = null, $item = null) {
        $this->setForm(self::FORM_UNIT);
        $id = $this->getParam();
        if (!$id) cmsCore::error404();
        $item = table_currency::getById($id)->toArray();
        if (!$item) {
            cmsUser::addSessionMessage('Единица не найдена', 'error');
            $this->redirectBack();
        }
        return parent::actionEdit($id, $item);
    }

    public function actionDelete() {
        $this->setForm(self::FORM_UNIT);
        $id = $this->getParam();
        if (!$id) cmsCore::error404();
        $item = $this->model->currency->getById($id);
        if (!$item) {
            cmsUser::addSessionMessage('Единица не найдена', 'error');
            $this->redirectBack();
        }
        return parent::actionDelete();
    }

    public function actionAddValue() {
        $this->setForm(self::FORM_UNIT_VALUE);$result = parent::actionAdd();
        if (!isset($result['data']['item']['unit_id'])) {
            $unit_id = $this->getParam();
            if (isset($unit_id)) $result['data']['item']['unit_id'] = $unit_id;
        }
        return $result;
    }
        
    public function actionEditValue($id = null, $item = null)
    {
        $this->setForm(self::FORM_UNIT_VALUE);
        $result = parent::actionEdit();

        $unit = $this->model->currency->getById($result['data']['item']['currency_id']);
        if (!$unit) cmsCore::error('Единица не найдена', 'ID: '.$id);
        if (!isset($result['data']['item']['value'])) {
            $result['data']['item']['value'] = $result['data']['item']['rate'];
        }
        /** @var \cmsForm $form */
        $form = $result['data']['form'];
        $form->removeField(0, 'unit_id');
        $field = $form->getField('value');
        $field->element_title .= ' ('. $unit->suffix .')';

        $result['data']['title'] = $this->titles['edit_value']. ' ('.$unit->name.')';
        return $result;
    }

    public function actionDeleteValue() {
        $this->setForm(self::FORM_UNIT_VALUE);
        return parent::actionDelete();
    }

    function save($id, $data) {
        if ($this->formName == self::FORM_UNIT) {
            $data['type'] = table_currency::TYPE_UNITS;
            $data['is_visible'] = $data['is_visible'] ? 1 : 0;
            return parent::save($id, $data);
        } elseif ($this->formName == self::FORM_UNIT_VALUE) {
            $unitId = 0;
            if ($id) {
                $unitRate = $this->model->archive->getById($id);
                if (!$unitRate) cmsCore::error('Значение не найдено', 'ID: '.$id);
                $unitId = $unitRate->currency_id;
            } else {
                $unitId = $data['unit_id'];
                $unitRate = $this->model->archive->createRow([
                   'currency_id' => $unitId
                ]);
            }
            $unit = $this->model->currency->getById($unitId);
            if (!$unit || $unit->type != table_currency::TYPE_UNITS) {
                cmsCore::error('Единица не найдена', 'ID: ' . $data['unit_id']);
            }
            /*if (!$data['end_date']) {
                $unit->rate = $data['value'];
                $unit->rate_date = $data['rate_date'];
                $unit->save();
            }*/
            $unitRate->rate_date = $data['rate_date'];
            //$unitRate->end_date = $data['end_date'] ?: null;
            $unitRate->rate = $data['value'];
            $id = $unitRate->save();
            $this->model->archive->refreshCurrencyRates($unitId, $id);
            return $id;
        }
    }

}