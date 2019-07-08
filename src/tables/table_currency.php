<?php
namespace pdima88\icms2currencyrates\tables;

use pdima88\icms2ext\Table;
use Zend_Db_Select;
use Zend_Db_Table_Row_Abstract;

/**
 * Валюта и ее актуальный курс
 * @property int $id ID
 * @property int $num_code Числовой международный код валюты
 * @property string $code Символьный трехбуквенный международный код валюты
 * @property int $nominal Номинал иностранной валюты
 * @property string $name Наименование (на русском)
 * @property double $rate Курс
 * @property string $rate_date Дата с которой действует курс
 * @property double $diff Изменение курса
 * @property string $name_uz Наименование (на узбекском)
 * @property string $name_en Наименование (на английском)
 * @property bool $is_primary Основная валюта (1 - основная, -1 - валюта по умолчанию, к которой приведен курс)
 * @property bool $is_visible Показывать на сайте
 */
class row_currency extends Zend_Db_Table_Row_Abstract
{

}

/**
 * @method static row_currency getById($id) Возвращает курс валюты по ID
 * @method static self instance()
 * @method row_currency createRow(array $data = [], $defaultSource = null)
 */
class table_currency extends Table 
{
    protected $_name = 'currency_rates';

    protected $_rowClass = __NAMESPACE__.'\\row_currency';

    protected $_primary = ['id'];

    const REF_ARCHIVE = __NAMESPACE__.'\\table_archive.Currency';

    const TYPE_CURRENCY = 0;
    const TYPE_CURRENCY_PRIMARY = 1;
    const TYPE_CURRENCY_DEFAULT = 2;
    const TYPE_UNITS = 3;

    /**
     * Возвращает курс валюты по символьному коду
     * @param string $code
     * @return null|row_currency
     */
    public static function getByCode($code) {
        return self::instance()->fetchRow(['code = ?' => $code]);
    }

    public function getCurrencyList($onlyVisible = true) {
        $select = $this->selectCurrencyRates()
            ->where('type <> ?', self::TYPE_CURRENCY_DEFAULT)
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns([
                'id',
                'title' => 'name'
            ]);

        if ($onlyVisible) $select->where('is_visible = ?',1);

        return $select->query()->fetchAll();
    }

    public function getCurrencyMap() {
        return array_column($this->getCurrencyList(false), 'title', 'id');
    }

    public function selectCurrencyRates($select = null) {
        if (!isset($select)) {
            $select = $this->selectAll();
        }
        return $select->where('type >= ?', self::TYPE_CURRENCY)
        ->where('type <= ?', self::TYPE_CURRENCY_DEFAULT);
    }

    public function selectUnits($select = null)
    {
        if (!isset($select)) {
            $select = $this->selectAll();
        }
        return $select->where('type = ?', self::TYPE_UNITS);
        
    }

    public function getUnitsList() {
        $select = $this->selectUnits()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns([
                'id',
                'title' => 'name'
            ]);

        return $select->query()->fetchAll();
    }

    public function getUnitsMap() {
        return array_column($this->getUnitsList(), 'title', 'id');
    }


}