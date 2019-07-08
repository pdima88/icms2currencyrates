<?php

namespace pdima88\icms2currencyrates\backend\forms;

use cmsForm;
use fieldCheckbox;

class form_options extends cmsForm {

    public function init() {

	    return array(

            array(
                'type' => 'fieldset',
                'childs' => array(
					new fieldCheckbox('auto_update', [
						'title' => 'Автоматическое обновление с cbu.uz',
					]),
                )
            ),

        );

    }

}
