<?php
/**
 * @var cmsTemplate $this
 * @var array $monthList
 * @var int $startMonth
 * @var int $startYear
 * @var int $endMonth
 * @var int $endYear
 * @var array $currencies
 * @var array $selectedCurrencies
 * @var array $rates
 * @var array $errors
 */
$this->addCSS('static/assets/flags/flags.css', false);
?>
<style>
    select {
        width:auto;
    }
    #currencyList
    {
        columns: 3;
        margin-top:10px;
        margin-bottom: 10px;
    }
    @media (max-width: 1200px) {
        #currencyList {
            columns: 2;
        }
    }
    @media (max-width: 600px) {
        #currencyList {
            columns: 1;
        }
    }
    #currencyList label {
        display: block;
    }

    .table {
        border: 1px solid #CCC;
        width: 100%;
        border-collapse: collapse;
    }

    .table td, .table th {
        border: 1px solid #CCC;
        padding: 5px 10px;
    }

    .rate_up {
        color: green;
        white-space: nowrap;
    }

    .rate_down {
        color: red;
        white-space: nowrap;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
</style>

<div id="CurrencyRatesTabs" class="tabs-menu">
    <ul class="tabbed">
        <li class="active" role="presentation">
            <a href="#" aria-controls="tabRates"
               role="tab" data-toggle="tab">Динамика курсов валют</a>
        </li>
        <li role="presentation">
            <a href="<?= $this->href_to('units').'?'.http_build_query([
                'start_year' => $startYear,
                'start_month' => $startMonth,
                'end_year' => $endYear,
                'end_month' => $endMonth,
            ]) ?>" aria-controls="tabUnits"
               role="tab" data-toggle="tab">МРЗП - МРОТ - Ставка рефинансирования</a>
        </li>
    </ul>
</div>

<div class="gui-panel">
<form>

Период:
    <span style="white-space: nowrap">
<?= html_select('start_month', $monthList, $startMonth); ?><?= html_select('start_year', $yearList, $startYear); ?>
        </span>

&mdash;
    <span style="white-space: nowrap">
<?= html_select('end_month', $monthList, $endMonth); ?><?= html_select('end_year', $yearList, $endYear); ?>
        </span>

    <?php if ($errors): ?>
        <div class="panel panel-red">
            <?= join('<br>',$errors) ?>
        </div>
    <?php endif ?>

<div id="currencyList">

<?php foreach ($currencies as $currency): ?>
    <label<?php if (!($currency['checked'] ?? false) && $currency['type']!=1):?> style="display: none"<?php endif; ?>>
        <input type="checkbox" name="currency[]" value="<?= $currency['code'] ?>"<?php if ($currency['checked'] ?? false):?> checked<?php endif; ?>>
        <?php if ($currency['country']): ?>
        <span class="flag flag-<?= strtolower($currency['country']) ?>"></span>
        <?php endif; ?>
        <?= $currency['name'] ?> (<?= $currency['code'] ?>)

    </label>
<?php endforeach; ?>

</div>
    <a href="#" class="button" onclick="$(this).hide();$('#currencyList label').show();return false;">Другие валюты &raquo;</a>
    <input type="submit" value="Показать" class="button">
</form>
</div>

<?php if ($rates): ?>
<div class="table-responsive">
<table class="table">
    <thead>
        <th>Дата</th>
        <?php foreach ($selectedCurrencies as $currency): ?>
            <th>
                <?php if ($currency['country']): ?>
                <span class="flag flag-<?= strtolower($currency['country']) ?>"></span>
                <?php endif; ?>
                <?= $currency['code'] ?>
            </th>
        <?php endforeach; ?>
    </thead>
    <tbody>
<?php foreach ($rates as $date => $dateRates): ?>
        <tr>
            <th scope="row"><?= format_date($date) ?></th>
            <?php foreach ($selectedCurrencies as $currency): ?>
                <td>
                    <?php $r = $dateRates[$currency['id']] ?? null;
                    if ($r):
                    ?>
                        <?= $r['rate'] ?>

                        <?php if ($r['diff'] > 0): ?>
                            <span class="rate_up"><i class="fa fa-arrow-up" ></i>
                        <?php elseif ($r['diff'] < 0): ?>
                            <span class="rate_down"><i class="fa fa-arrow-down"></i>
                        <?php endif; ?>

                        <?php if ($r['diff']): ?>
                        <?= $r['diff'] ?><?php
                        $old = $r['rate'] - $r['diff'];
                        if ($old) {
                            $perc = $r['diff']/$old*100;
                            echo '&nbsp;<span style="font-size: smaller; color:#888">('.(($perc>0)?'+':'').number_format($perc,2).'%)</span>';
                        }
                            echo '</span>';
                        ?>

                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

