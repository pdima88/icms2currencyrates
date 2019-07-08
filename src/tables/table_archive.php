<?php
namespace pdima88\icms2currencyrates\tables;

use pdima88\icms2ext\Table;
use Zend_Db_Table_Row_Abstract;

/**
 * Курс валюты за определенный период
 * @property int $id ID
 * @property int $currency_id ID валюты
 * @property row_currency $currency Валюта
 * @property double $rate Курс
 * @property string $rate_date Дата с которой действует курс
 * @property double $diff Изменение курса
 * @property string $end_date Дата окончания периода действия курса (до, не включая указанную дату)
 */
class row_archive extends Zend_Db_Table_Row_Abstract
{
    /**
     * @return null|row_archive
     */
    function prev() {
        return $this->getTable()->fetchRow([
            'currency_id = ?' => $this->currency_id,
            'rate_date < ?' => $this->rate_date
        ], 'rate_date DESC');
    }

    /**
     * @return null|row_archive
     */
    function next() {
        return $this->getTable()->fetchRow([
            'currency_id = ?' => $this->currency_id,
            'rate_date > ?' => $this->rate_date
        ], 'rate_date ASC');
    }
}

/**
 * Таблица содержит историю курсов валют, включая и актуальные курсы
 * @method static row_archive getById($id) Возвращает курс валюты по ID
 * @method row_archive createRow(array $data = [], $defaultSource = null)
 */
class table_archive extends Table {
    protected $_name = 'currency_archive';

    protected $_rowClass = __NAMESPACE__.'\\row_archive';

    protected $_primary = ['id'];

    protected $_referenceMap = [
        'Currency' => [
            self::COLUMNS           => 'currency_id',
            self::REF_TABLE_CLASS   => __NAMESPACE__.'\\table_currency',
            self::REF_COLUMNS       => 'id'
        ],
    ];

    const FK_CURRENCY = __CLASS__.'.Currency';



    /**
     * Возвращает курс валюты по символьному коду
     * @param string $code
     * @return null|row_archive
     */
    public function getByCurrencyIdAndDate($currencyId, $date) {
        if (!isset($date)) $date = '0000-00-00';
        return $this->fetchRow([
            'currency_id = ?' => $currencyId,
            'rate_date = ?' => $date
        ]);
    }

    public function selectCurrencyRatesArchive() {        
        return table_currency::instance()->selectCurrencyRates(
            $this->selectAs('a')->joinBy(self::FK_CURRENCY, 'c')
        )->columns([
            'a.*'
        ]);
    }

    public function selectUnitsArchive()
    {
        return table_currency::instance()->selectUnits(
            $this->selectAs('a')->joinBy(self::FK_CURRENCY, 'c')
        )->columns([
            'a.*'
        ]);
    }

    /**
     * @param row_currency $currency
     */
    public function saveCurrencyRate($currency, $endDate = null) {
        if ($currency->rate === null || $currency->rate_date === null) return;
        $a = $this->getByCurrencyIdAndDate($currency->id, $currency->rate_date);
        if (!$a) {
            $a = $this->createRow([
                'currency_id' => $currency->id,
                'rate_date' => $currency->rate_date,
                'rate' => $currency->rate,
                'diff' => $currency->diff,
            ]);
            $a->save();
            $p = $a->prev();
            if ($p && !$p->end_date) {
                $p->end_date = $currency->rate_date;
                $p->save();
            }
        }
        if ($endDate) {
            $a->end_date = $endDate;
            $a->save();
        }
    }




}