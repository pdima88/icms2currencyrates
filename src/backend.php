<?php

namespace pdima88\icms2currencyrates;

use cmsBackend;

class backend extends cmsBackend {

	public $useDefaultOptionsAction = true;

	public function actionIndex(){
		$this->redirectToAction('options');
	}

	protected function validateParamsCount($class, $method_name, $params)
	{
		return true;
	}

	public function getBackendMenu(){
		return array(

			array(
				'title' => 'Курсы валют ЦБ РУз',
				'url' => href_to($this->root_url, 'rates')
			),
			array(
				'title' => 'Архив курсов валют',
				'url' => href_to($this->root_url, 'archive')
			),
			array(
				'title' => 'Другие единицы',
				'url' => href_to($this->root_url, 'units')
			),
			array(
				'title' => 'Настройки',
				'url' => href_to($this->root_url, 'options')
			),
		);
	}

}