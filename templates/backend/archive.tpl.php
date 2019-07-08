<?php
/** @var cmsTemplate $this */
$action = 'archive';
$this->renderAsset('icms2ext/backend/treeandgrid', [
    'tree' => $currencies,
    'grid' => $grid,
    'id' => $currency_id,
    'page_title' => $page_title,
    'page_url' => $page_url,
    'toolbar' => [
        'excel' => [
            'title' => 'Экспорт',
            'export' => 'csv',
            'target' => '_blank',
        ]
    ],
]);

?>
<style>
    .cp_toolbar {
        float: right;
        margin-top: -50px;
    }
</style>
