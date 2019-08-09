<?php
/**
 * @var cmsTemplate $this
 * @var array $rates
 */
use pdima88\icms2currencyrates\frontend as currencyrates;

$this->addJSFromContext('templates/default/js/jquery-ui.js');
$this->addJSFromContext('templates/default/js/i18n/jquery-ui/'.cmsCore::getLanguageName().'.js');
$this->addCSSFromContext('templates/default/css/jquery-ui.css');
$this->addCSS('static/assets/flags/flags.css', false);
$controllerName = currencyrates::getInstance()->name;
?>
<style>
    #lblCurrencyRatesDate {
        border-bottom:1px dashed #2980b9;
        cursor: pointer;
    }
</style>
<div class="currencyrates_form">

    <div id="currency_daily_widget_title" style="display: none">
        Дата:
    <input type="text" id="dpCurrencyRatesDate" style="width:0;height:0px;border:0;position: absolute">
    <label for="dpCurrencyRatesDate" id="lblCurrencyRatesDate"><?= format_date(now()) ?></label>

        <div class="links">
            <a href="<?= href_to($controllerName, 'rates') ?>">
                За период                        </a>
        </div>
    </div>

    <script>
        $( function() {
            var $t = $('#currency_daily_widget_title');
            var $w = $t.parents('.widget');
            if ($w.length > 0) {
                $($w.get(0)).find('.title').html('').prepend($t);
                $t.show();
            }
            $( "#dpCurrencyRatesDate" ).datepicker({
                changeMonth: true,
                changeYear: true,
            }).on('change', function() {
                $.ajax({
                  url: '<?= href_to($controllerName, 'widget_daily') ?>/'+$('#dpCurrencyRatesDate').val(),
                  //cache: false,
                  //type: 'POST',
                  complete: function(xhr, status) {

                  },
                  success: function(data, status, xhr) {
                      if (data.date) {
                          $('#lblCurrencyRatesDate').text(data.date);
                          $('#widget_daily_currency_rates').html(data.html);
                      }
                  },
                  error: function(xhr, status, err) {

                  }
                });

            });
        } );
    </script>

    <div id="widget_daily_currency_rates">

    <?php $this->renderControllerChild($controllerName, 'widget_fragment', [
        'date' =>today(),
        'rates' => $rates,
        'units' => $units,
    ]); ?>

    </div>
</div>