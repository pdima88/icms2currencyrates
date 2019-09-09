<?php

namespace pdima88\icms2currencyrates\hooks;

use cmsAction;
use pdima88\icms2currencyrates\frontend as currencyrates;
use tableUsers;

/**
 * @mixin currencyrates
 */
class cron_rateupdate extends cmsAction {

    public function run(){

        $result = $this->model->updateRates();

        if ($result) {

            $adminIds = tableUsers::selectAs('u')->where('is_admin = ?', 1)
                ->columns(['id'])->query()->fetchAll();

            $messenger = $this->controller_messages;

            foreach ($adminIds as $u) {
                $messenger->addRecipient($u['id']);
            }

            $messenger->sendNoticePM([
                'content' => t('currencyrates:updated', 'Курсы валют обновлены')
            ]);
        }

    }

}
