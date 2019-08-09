<?php
/**
 * @var cmsTemplate $this
 * @var array $monthList
 * @var int $startMonth
 * @var int $startYear
 * @var int $endMonth
 * @var int $endYear
 * @var array $units
 * @var array $rates
 * @var array $errors
 */
?>
<style>
    select {
        width:auto;
        margin-bottom: 10px;
    }

    .period_form .button {
        vertical-align: inherit;
    }

    .period_form.gui-panel {
        padding-bottom: 5px;
    }
    .end_year {
        margin-right: 10px;
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
        <li role="presentation">
            <a href="<?= $this->href_to('rates').'?'.http_build_query([
                'start_year' => $startYear,
                'start_month' => $startMonth,
                'end_year' => $endYear,
                'end_month' => $endMonth,
            ]) ?>" aria-controls="tabRates"
               role="tab" data-toggle="tab">Динамика курсов валют</a>
        </li>
        <li class="active" role="presentation">
            <a href="#" aria-controls="tabUnits"
               role="tab" data-toggle="tab">МРЗП - МРОТ - Ставка рефинансирования</a>
        </li>
    </ul>
</div>

<div class="gui-panel period_form">
<form>

Период:
    <span style="white-space: nowrap">
<?= html_select('start_month', $monthList, $startMonth); ?><?= html_select('start_year', $yearList, $startYear); ?>
        </span>

&mdash;
    <span style="white-space: nowrap">
<?= html_select('end_month', $monthList, $endMonth); ?><?= html_select('end_year', $yearList, $endYear,['class' => 'end_year']); ?>
        </span>
    <input type="submit" value="Показать" class="button">

    <?php if ($errors): ?>
        <div class="panel panel-red">
            <?= join('<br>',$errors) ?>
        </div>
    <?php endif ?>
</form>
</div>

<?php if ($rates): ?>
<div class="table-responsive">
<table class="table">
    <thead>
        <th>Дата</th>
        <?php foreach ($units as $unit): ?>
            <th>
                <?= $unit['name'] ?>, <?= $unit['suffix'] ?>
            </th>
        <?php endforeach; ?>
    </thead>
    <tbody>
<?php foreach ($rates as $date => $dateRates): ?>
        <tr>
            <th scope="row"><?= format_date($date) ?></th>
            <?php foreach ($units as $unit): ?>
                <td>
                    <?php $r = $dateRates[$unit['id']] ?? null;
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
