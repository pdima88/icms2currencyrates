<?php

namespace pdima88\icms2currencyrates;

class manifest {

	public function hooks() {
		return [
		];
	}


	public function getRootPath() {
		return realpath(dirname(__FILE__).'/..');
	}

}
