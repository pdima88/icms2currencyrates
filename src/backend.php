<?php

namespace pdima88\icms2currencyrates;

use cmsBackend;

class backend extends cmsBackend {

	public $useDefaultOptionsAction = true;

	public function actionIndex(){
		$this->redirectToAction('options');
	}

}