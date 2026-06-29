<?php
/**
 * @var AdminController $this
 * @var Comment $model
 * @var string $wsUrl
 */

$this->pageTitle = 'Admin Comments';
?>

<h1>Admin Comments</h1>

<p class="meta" id="ws-status">Connecting to realtime updates...</p>

<?php $this->widget('zii.widgets.grid.CGridView', [
    'id' => 'comment-grid',
    'htmlOptions' => ['class' => 'grid-view comments-grid'],
    'itemsCssClass' => 'items comments-grid-items',
    'rowCssClassExpression' => '$data->isDeleted ? "comment-row deleted-row" : "comment-row active-row"',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => [
        [
            'name' => 'id',
            'header' => 'Comment ID',
            'filter' => CHtml::activeTextField($model, 'id', ['placeholder' => 'Filter by ID']),
            'htmlOptions' => ['class' => 'col-id'],
            'headerHtmlOptions' => ['class' => 'col-id'],
            'filterHtmlOptions' => ['class' => 'col-id'],
        ],
        [
            'name' => 'name',
            'header' => 'Guest Name',
            'filter' => CHtml::activeTextField($model, 'name', ['placeholder' => 'Filter by guest name']),
            'htmlOptions' => ['class' => 'col-name'],
            'headerHtmlOptions' => ['class' => 'col-name'],
            'filterHtmlOptions' => ['class' => 'col-name'],
        ],
        [
            'name' => 'message',
            'header' => 'Comment Message',
            'filter' => CHtml::activeTextField($model, 'message', ['placeholder' => 'Filter by message text']),
            'htmlOptions' => ['class' => 'col-message'],
            'headerHtmlOptions' => ['class' => 'col-message'],
            'filterHtmlOptions' => ['class' => 'col-message'],
        ],
        [
            'name' => 'status',
            'header' => 'Comment Status',
            'type' => 'raw',
            'value' => 'CHtml::tag("span", ["class" => "status-badge status-" . $data->status], CHtml::encode(ucfirst($data->status)))',
            'filter' => CHtml::activeDropDownList($model, 'status', [
                Comment::STATUS_ACTIVE => 'Active only',
                Comment::STATUS_DELETED => 'Deleted only',
            ], ['prompt' => 'All statuses']),
            'htmlOptions' => ['class' => 'col-status'],
            'headerHtmlOptions' => ['class' => 'col-status'],
            'filterHtmlOptions' => ['class' => 'col-status'],
        ],
        [
            'name' => 'created_at',
            'header' => 'Created At',
            'filter' => CHtml::activeTextField($model, 'created_at', ['placeholder' => 'YYYY-MM-DD or time']),
            'htmlOptions' => ['class' => 'col-date'],
            'headerHtmlOptions' => ['class' => 'col-date'],
            'filterHtmlOptions' => ['class' => 'col-date'],
        ],
        [
            'name' => 'updated_at',
            'header' => 'Last Updated At',
            'filter' => CHtml::activeTextField($model, 'updated_at', ['placeholder' => 'YYYY-MM-DD or time']),
            'htmlOptions' => ['class' => 'col-date'],
            'headerHtmlOptions' => ['class' => 'col-date'],
            'filterHtmlOptions' => ['class' => 'col-date'],
        ],
        [
            'class' => 'CButtonColumn',
            'header' => 'Actions',
            'template' => '{update} {delete}',
            'updateButtonUrl' => 'Yii::app()->createUrl("admin/update", ["id" => $data->id])',
            'deleteButtonUrl' => 'Yii::app()->createUrl("admin/delete", ["id" => $data->id])',
            'deleteConfirmation' => 'Soft-delete this comment?',
            'htmlOptions' => ['class' => 'col-actions'],
            'headerHtmlOptions' => ['class' => 'col-actions'],
            'filterHtmlOptions' => ['class' => 'col-actions'],
        ],
    ],
]); ?>

<script>
(function () {
    var status = document.getElementById('ws-status');
    var ws = new WebSocket(<?= CJavaScript::encode($wsUrl); ?>);

    ws.onopen = function () {
        status.textContent = 'Realtime updates connected.';
    };

    ws.onclose = function () {
        status.textContent = 'Realtime updates disconnected. Refresh page to retry.';
    };

    ws.onerror = function () {
        status.textContent = 'Realtime updates error.';
    };

    ws.onmessage = function (event) {
        var payload = JSON.parse(event.data);

        if (payload.type !== 'comment.created') {
            return;
        }

        refreshGrid();
    };

    function refreshGrid() {
        if (window.jQuery && jQuery.fn.yiiGridView) {
            jQuery('#comment-grid').yiiGridView('update');
            return;
        }

        window.location.reload();
    }
})();
</script>
