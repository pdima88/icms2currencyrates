<?php

namespace pdima88\icms2currencyrates\backend\actions;

use pdima88\icms2currencyrates\model;
use pdima88\icms2ext\crudAction;
use pdima88\icms2currencyrates\tables\table_currency;

/**
 * @property model $model
 */
class units extends crudAction {

    const FORM_UNIT_VALUE = 'unit_value';
    const FORM_UNIT = 'unit';

    function getGrid()
    {
        $select = $this->model->archive->selectUnitsArchive();
        $unitId = $this->getParam();
        if (isset($unitId) && $unitId) $select->where('currency_id = ?', $unitId);

        $grid = [
            'id' => 'units',
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
                    'align' => 'right'
                ],

            ]
        ];

        if (!isset($unitId) || !$unitId) {
            $grid['columns'] = array_merge(['currency_id' => [
                'title' => 'Единица',
                'filter' => 'select',
                'sort' => 'true',
                'format' => $this->model->currency->getUnitsMap()
            ]], $grid['columns']);

            $grid['multisort'] = true;
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
        $this->titles['add_value'] = 'Добавление тарифного плана';
        $this->titles['edit_value'] = 'Редактирование тарифного плана';
        $this->messages['add'] = 'Единица добавлена';
        $this->messages['error_edit_no_item'] = 'Значение не найдено';
    }

    public function actionUnitInfo() {
        $id = $this->getParam(0);
        if (!$id) { exit; }

        $errors = false;

        $unit = table_currency::getById($id);

        if (!$unit) {
            echo 'Единица не найдена!';
            exit;
        }        

        $tpl = cmsTemplate::getInstance();
        return $tpl->render('backend/unit_info', array(
            'unit' => $unit
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
        $item = $this->model->getTariffPlan($id);
        if (!$item) {
            cmsUser::addSessionMessage('Единица не найдена', 'error');
            $this->redirectBack();
        }
        return parent::actionDelete();
    }

    public function actionAddValue() {
        $this->setForm(self::FORM_UNIT_VALUE);$result = parent::actionAdd();
        if (!isset($result['data']['item']['unit_id'])) {
            $plan_id = $this->getParam();
            if (isset($plan_id)) $result['data']['item']['plan_id'] = $plan_id;
        }
        return $result;
    }
        
    public function actionEditValue($id = null, $item = null)
    {
        $this->setForm(self::FORM_UNIT_VALUE);
        return parent::actionEdit();
    }

    public function actionDeleteValue() {
        $this->setForm(self::FORM_UNIT_VALUE);
        return parent::actionDelete();
    }

}