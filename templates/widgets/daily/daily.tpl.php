<?php $this->addJSFromContext('templates/default/js/jquery-ui.js'); ?>
<?php $this->addJSFromContext('templates/default/js/i18n/jquery-ui/'.cmsCore::getLanguageName().'.js'); ?>
<?php $this->addCSSFromContext('templates/default/css/jquery-ui.css'); ?>

<div class="currencyrates_form">

    <div>
        Дата:
    <input type="text" id="dpCurrencyRatesDate" style="width:0;height:0px;border:0;position: absolute">
    <label for="dpCurrencyRatesDate"><?= format_date(now()) ?></label>
    </div>

    <script>
        $( function() {
            $( "#dpCurrencyRatesDate" ).datepicker({
                changeMonth: true,
                changeYear: true
            });
        } );
    </script>

    <h3>Курсы валют на <?= format_date(now()) ?></h3>

<?php foreach($rates as $r): ?>
<?= $r['code'] ?>

<?= $r['rate'] ?>

<?php endforeach; ?>
	

</div>