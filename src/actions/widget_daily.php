<?php
namespace pdima88\icms2currencyrates\actions;

use cmsAction;
use cmsTemplate;
use pdima88\icms2currencyrates\frontend as currencyrates;
use pdima88\icms2currencyrates\tables\table_currency;
use Zend_Db_Expr;
use Zend_Db_Select;

/**
 * @mixin currencyrates
 */
class widget_daily extends cmsAction {
    public function run($date = null) {
        if (!$date) {
            $date = today();
            $rates = $this->model->currency->fetchAll(['type = ?' => table_currency::TYPE_CURRENCY_PRIMARY, 'is_visible' => 1]);
            $units = $this->model->currency->fetchAll(['type = ?' => table_currency::TYPE_UNITS, 'is_visible' => 1]);
        } else {
            $date = date_iso($date);
            $sql = $this->model->archive->selectAs('a')
                ->where('a.currency_id = c.id')
                ->where('a.rate_date <= ?', $date)
                ->where('a.end_date IS NULL OR a.end_date > ?', $date)
                ->order('a.rate_date DESC')
                ->columns(['$col$'])
                ->limit(1)->assemble();
            $rates = $this->model->currency->selectAs('c')
                ->where('c.type = ?', table_currency::TYPE_CURRENCY_PRIMARY)
                ->columns([
                    'c.code',
                    'rate' => new Zend_Db_Expr('('.str_replace('$col$', 'rate', $sql).')'),
                    'diff' => new Zend_Db_Expr('('.str_replace('$col$', 'diff', $sql).')'),
                    'rate_date' => new Zend_Db_Expr('('.str_replace('$col$', 'rate_date', $sql).')'),
                    'end_date'  => new Zend_Db_Expr('('.str_replace('$col$', 'end_date', $sql).')'),
                ])
                ->query()->fetchAll();

            $units = $this->model->currency->selectAs('c')
                ->where('c.type = ?', table_currency::TYPE_UNITS)
                ->columns([
                    'c.name',
                    'c.suffix',
                    'rate' => new Zend_Db_Expr('('.str_replace('$col$', 'rate', $sql).')'),
                    'diff' => new Zend_Db_Expr('('.str_replace('$col$', 'diff', $sql).')'),
                    'rate_date' => new Zend_Db_Expr('('.str_replace('$col$', 'rate_date', $sql).')'),
                ])
                ->query()->fetchAll();
        }

        $tpl = cmsTemplate::getInstance();
        $r = $tpl->renderInternal($this, 'widget_fragment', [
            'date' => $date,
            'rates' => $rates,
            'units' => $units,
        ]);
        sendJson([
            'date' => format_date($date),
            'html' => $r,
        ]);
    }
}