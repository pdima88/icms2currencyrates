<?php
/** @var cmsTemplate $this */
$action = 'units';
$this->renderAsset('icms2ext/backend/treeandgrid', [
    'tree' => $units,
    'grid' => $grid,
    'id' => $unit_id,
    'page_title' => $page_title,
    'page_url' => $page_url,
    'treeitem_detail_url' => $this->href_to($action, 'unit_info'),
    'toolbar' => [
        'add' => [
            'title' => 'Новое значение',
            'href'  => $this->href_to($action, ['add_value', '{id}']).'?back={returnUrl}',
        ],
        'add_folder' => [
            'title' => 'Новая единица',
            'href'  => $this->href_to($action, 'add').'?back={returnUrl}',
        ],
        'edit' => [
            'title' => 'Редактировать единицу',
            'href'  => $this->href_to($action, ['edit', '{id}']).'?back={returnUrl}',
            'hide' => true,
        ],
        'delete' => [
            'title' => 'Удалить единицу',
            'href'  => $this->href_to($action, ['delete', '{id}']).'?csrf_token='.cmsForm::getCSRFToken(),
            'onclick' => "return confirm('Все значения этой единицы также будут удалены!')",
            'hide' => true,
        ],
        'excel' => [
            'title' => 'Экспорт',
            'export' => 'csv',
            'target' => '_blank'
        ]
    ],
]);

