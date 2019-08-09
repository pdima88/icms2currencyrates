<?php
/**
 * @var cmsTemplate $this
 * @var string $date
 * @var array $rates
 */
?>

<?php
$startDate = '0000-00-00';
$endDate = '9999-01-01';
foreach($rates as $r) {
    if ($r['rate_date'] > $startDate) $startDate = $r['rate_date'];
    if (isset($r['end_date']) && $r['end_date'] < $endDate && $r['end_date'] > $startDate) $endDate = $r['end_date'];
}
if ($endDate == '9999-01-01') $endDate = false;
if ($endDate < $startDate) $endDate = false;
?>


<h3 style="margin-bottom: 5px;margin-top: 0">Курсы валют
    <?php if ($startDate <> '0000-00-00'): ?>
    <span style="white-space: nowrap">с <?= format_date($startDate) ?></span>
    <?php endif; ?>
    <?php if ($endDate): ?>
        <span style="white-space: nowrap">по <?= format_date(strtotime($endDate.' -1 day')) ?></span>
    <?php endif; ?>
</h3>

<?php foreach($rates as $r): ?>
    <?php if ($r['code'] <> 'XDR'): ?>
        <span class="flag flag-<?= strtolower(substr($r['code'],0,2)) ?>"></span>
    <?php endif; ?>

    <?= $r['code'] ?>

    <b><?= $r['rate'] ?> сум</b>

    <?php if ($r['diff'] > 0): ?>
        <i class="fa fa-arrow-up" style="color:green"></i>
    <?php elseif ($r['diff'] < 0): ?>
        <i class="fa fa-arrow-down" style="color:red"></i>
    <?php endif; ?>

    <?php if ($r['diff']): ?>
        <?= $r['diff'] ?>
        <?php
        $old = $r['rate'] - $r['diff'];
        if ($old) {
            $perc = $r['diff']/$old*100;
            echo ' <span style="font-size: smaller; color:#888">('.(($perc>0)?'+':'').number_format($perc,2).'%)</span>';
        }
        ?>
    <?php endif; ?>
    <br>

<?php endforeach; ?>


<p>

<?php foreach($units as $r): ?>
    <h4 style="margin-bottom: 0;margin-top:10px;"><?= $r['name'] ?>
    <span style="white-space: nowrap">с <?= format_date($r['rate_date']) ?>:</span></h4>
    <b><?= format_num($r['rate']) ?>
    <?= $r['suffix'] ?></b>

<?php if ($r['diff'] > 0): ?>
    <i class="fa fa-arrow-up" style="color:green"></i>
<?php elseif ($r['diff'] < 0): ?>
    <i class="fa fa-arrow-down" style="color:red"></i>
<?php endif; ?>

<?php if ($r['diff']): ?>
    <?= $r['diff'] ?>
    <?php
    $old = $r['rate'] - $r['diff'];
    if ($old) {
        $perc = $r['diff']/$old*100;
        echo ' <span style="font-size: smaller; color:#888">('.(($perc>0)?'+':'').number_format($perc,2).'%)</span>';
    }
    ?>
<?php endif; ?>
    
    <br/>

<?php endforeach; ?>
</p>
