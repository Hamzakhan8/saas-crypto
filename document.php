<?php
/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use app\components\AppHelper;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->title = 'Documents';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$folderArray = Yii::$app->request->get('DocumentSearch');
$path = !empty($folderArray) ? (isset($folderArray['folder']) ? AppHelper::getFolderPath($folderArray['folder']) : '') : '';
$path = isset($path) ? str_replace('/uploads/', '', $path) : '';
$this->params['breadcrumbs'][] = $path;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";

$folderArray = Yii::$app->request->get('DocumentSearch');
$folder_id = !empty($folderArray['folder']) ? $folderArray['folder'] : '';
$this->registerJs($search);
?>


<?php $this->registerJsFile(Yii::$app->homeUrl. 'js/DYMO.Label.Framework.3.0.js?'.time(), ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile(Yii::$app->homeUrl. 'js/PrintImage.js?'.time(), ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<div class="mdc-layout-grid">
    <div class="card-header card-header-icon" data-background-color="red">
        <h4 class="card-title"><i class="mdi mdi-folder-multiple"></i> <?= Html::encode($this->title) ?></h4>
    </div>
    <div class="mdc-layout-grid__inner">

        <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-12">
            <div class="mdc-card" id="documents-listing">
                <?php if (Yii::$app->controller->action->id == 'advanced-search'): ?>
                    <div class="search-form">
                        <?= $this->render('_search', ['model' => $searchModel]); ?>
                    </div>   
                <?php endif; ?>


                <p>
                    <?php if (Yii::$app->controller->action->id == 'index'): ?>
                        <?= Html::a('Create Document', $folder_id ? ['create', 'folder' => $folder_id] : ['create'], ['class' => 'doc-action mdc-button mdc-button--raised filled-button--success mdc-ripple-upgraded']) ?>
                    <?php endif; ?>

                    <span id="multiple_select" style="display:none">
                        <?= Html::a('Delete', ['#'], ['class' => 'doc-action mdc-button mdc-button--raised filled-button--danger mdc-ripple-upgraded deleteSelection', 'title' => 'Delete Selection']) ?>
                        <?= Html::a('Download Zip', ['#'], ['class' => 'doc-action mdc-button mdc-button--raised filled-button--dark mdc-ripple-upgraded bulkDownload', 'title' => 'Bulk Download']) ?>
                    </span>
                </p>


                <?php
                $gridColumn = [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'contentOptions' => ['class' => 'text-center']
                    ],
                    ['class' => 'kartik\grid\CheckboxColumn',
                        'content' => function($model) {
                            return '<div class="mdc-form-field">
                                    <div class="mdc-checkbox mdc-checkbox--success">' . Html::checkBox('selection[]', false, ['class' => 'mdc-checkbox__native-control kv-row-checkbox', 'id' => "customCheck" . $model->id, 'value' => $model->id, 'onclick' => '
                                    $(this).prop("checked", $(this).is(":checked"));']) .
                                    '<div class="mdc-checkbox__background">
                                      <svg class="mdc-checkbox__checkmark"
                                            viewBox="0 0 24 24">
                                        <path class="mdc-checkbox__checkmark-path"
                                              fill="none"
                                              d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
                                      </svg>
                                      <div class="mdc-checkbox__mixedmark"></div>
                                    </div>
                                  </div>                          
                                </div>';
                        },
                        'header' => '',
                        'contentOptions' => ['class' => 'kv-row-select'],
                    ],
                    ['attribute' => 'id', 'visible' => false],
                    [
                        'attribute' => 'document',
                        'format' => 'raw',
                        'label' => '',
                        'value' => function($model) {
                            $file_type = $model->versions ? $model->versions[0]->file_type : 'pdf';
                            $icon = AppHelper::getFileIcon($file_type);
                            return Html::a('<i class="mdi ' . $icon . ' mdi-24px"><i>', ['#'], [
                                        'class' => 'show-info doc-action preview-modal',
                                        'data-title' => Yii::t('yii', $model->title_en),
                                        'data-action' => Url::to(['doc-preview']),
                                        'data-toggle' => "modal",
                                        'data-target' => "#view_document",
                                        'data-key' => $model->id,
                                        'data-pjax' => '0',
                            ]);
                        },
                        'headerOptions' => [
                            'style' => 'width:3.5%'
                        ],
                    ],
                    [
                        'attribute' => 'ud_id',
                        'format' => 'raw',
                        'value' => function($model) {
                                return $model->ud_id. '<br><span style="text-align: center;font-size:19px">&nbsp;&nbsp;&nbsp;&nbsp;' . $model->doc_date . '</span>';
                        },
                        'contentOptions' => ['class' => 'text-right kv-align-center kv-align-middle', 'style' => 'direction: rtl;font-size:19px'],
                        'headerOptions' => [
                            'style' => 'width:6.5%'
                        ],
                    ],
                    [
                        'attribute' => 'title',
                        'format' => 'raw',
                        'label' => 'Title',
                        'value' => function($model) {
                            if ($model->title_en || $model->title_ar) {
                                $title = !empty($model->title_ar) ? substr($model->title_ar, 0, strlen($model->title_ar) < 90 ? strlen($model->title_ar) : 100) : substr($model->title_en, 0, strlen($model->title_en) < 30 ? strlen($model->title_en) : 35);
                                return Html::a($title, Yii::$app->homeUrl . 'document/view?id=' . $model->id, [
                                            'class' => 'show-info doc-action',
                                            'style' => 'font-size: 15px',
                                            'data-pjax' => '0',
                                            'title' => Yii::t('yii', !empty($model->title_ar) ? $model->title_ar : $model->title_en),
                                            'target' => '_blank']);
                            }
                            return NULL;
                        },
                        'contentOptions' => ['class' => 'text-left kv-align-center kv-align-middle'],
                    ],
                    [
                        'attribute' => 'campus',
                        'value' => function($model) {
                            return $model->campus ? ($model->campus == 1 ? 'Al-Ain' : 'Abu-Dhabi') : '';
                        },
                        'filterType' => '\kartik\select2\Select2',
                        'filter' => app\components\AppHelper::getCampusDropList(),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => 'Campus', 'id' => 'grid-campus-search'],
                        'headerOptions' => [
                            'style' => 'width:5.5%',
                        ],
                        'contentOptions' => ['class' => 'text-center kv-align-center kv-align-middle'],
                    ],
                    [
                        'attribute' => 'folder',
                        'label' => 'Folder',
                        'value' => function($model) {
                            return str_replace("/uploads", "", $model->folder0->path);
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => ArrayHelper::map(\app\models\Folder::find()->asArray()->all(), 'id', 'name'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => 'folders', 'id' => 'grid-document-search-folder'],
                        'contentOptions' => ['class' => 'text-left kv-align-center kv-align-middle'],
                        'headerOptions' => [
                            'style' => 'width:9.5%'
                        ],
                    ],
                    /*[
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'status',
                        'label' => 'Status',
                        'refreshGrid' => true,
                        'pageSummary' => true,
                        // end filtering grid
                        'value' => function($model) {
                            
                        }, // assign value from method
                        'filterType' => '\kartik\select2\Select2',
                        'filter' => ArrayHelper::map(\app\models\Status::find()->asArray()->all(), 'id', 'name'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => 'Status', 'id' => 'grid-status'],
                        'editableOptions' => function ($model, $key, $index) {
                            $data = [];
                            $status = \app\models\Status::find()->asArray()->all();
                            foreach ($status as $value) {
                                $data[$value['id']] = $value['name'];
                            }
                            return [
                                'header' => 'Status',
                                'placement' => 'left',
                                'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                                //'asPopover' => false,
//                                'inlineSettings' => [
//                                    'closeButton' => '<button class="kv-editable-close kv-editable-reset mar-lft" title="Cancel Edit"><i class="fa fa-close btn btn-default "></i></button>'
//                                ],
//                                'resetButton' => ['icon' => '<i class="fa fa-undo btn btn-warning "></i>', 'label' => 'Reset Choice'],
                                'submitButton' => ['class' => 'btn mdc-button--outlined shaped-button mdc-ripple-upgraded', 'icon' => 'Save <i class="mdi mdi-content-save"></i>', 'label' => 'OK'],
                                'options' => [
                                    'data' => $data,
                                    'pluginEvents' => [
                                        "editableSuccess" => "function(event, val, form, data) { $.pjax.reload({container: '#documents-listing'});  alert(data); }",
                                    ]
                                ],
                                'size' => 'md',
                                'displayValueConfig' => $data,
//                                'data' => $data,
//                                'afterInput' => function ($form, $widget) use ($model, $index) {
//                                    echo Html::hiddenInput('document_id', $model->id);
//                                },
                            ];
                        },
                                'headerOptions' => [
                            'style' => 'width:5.5%'
                        ],
                    ],*/
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'width' => '40px',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        // uncomment below and comment detail if you need to render via ajax
                        // 'detailUrl'=>Url::to(['/site/book-details']),
                        'detail' => function ($model, $key, $index, $column) {
                            return Yii::$app->controller->renderPartial('versions', ['document_id' => $model->id]);
                        },
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'expandOneOnly' => true,
//                        'enableRowClick' => true,
                        'collapseTitle' => 'Versions',
                        'expandIcon' => '<i class="material-icons">expand_more</i>',
                        'collapseIcon' => '<i class="material-icons">expand_less</i>',
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'contentOptions' => ['class' => 'td-actions text-right'],
                        // 'options' => [
                        //     'width' => '8.5%',
                        // ],
                        'header' => 'Actions',
                        'template' => '{QR}{download}{email}{view}{edit}{delete}',
                        'headerOptions' => ['id' => 'activity-view-link',],
                        'contentOptions' => ['class' => 'td-actions text-center'],
                        'buttons' => [
			   'email' => function ($url, $model, $key) {
                                if ($model->versions && Yii::$app->user->can('/document/send-email')) {
                                    return Html::a('<i class="mdi mdi-email mdi-24px"></i>', '#', [
                                                'class' => 'doc-action email-modal',
                                                'title' => Yii::t('yii', 'Send Email'),
                                                'rel' => 'tooltip',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#send_document',
                                                'data-backdrop'=>'static',
                                                'data-key' => $key,
                                                'data-action' => Url::to(['email']),
                                                'data-title' => $model->title_en ? $model->title_en : $model->title_ar,
                                                'data-attachment' => '<i class="mdi ' . AppHelper::getFileIcon($model->versions[0]->file_type) . ' mdi-36px"></i> ' . str_replace(' ', '_', $model->title_en ? $model->title_en : $model->title_ar) . '.' . $model->versions[0]->file_type,
                                                'data-pjax' => '0',
                                    ]);
                                }
                                return NULL;
                            },
			    'QR' => function ($url, $model, $key) {
                                return Html::a('<i class="mdi mdi-qrcode mdi-24px"></i>', '#', [
                                    'id' => 'printButton'. $key,
                                    'data-action' => Yii::$app->homeUrl . 'document/stamp?id=' . $model->id . '&version=' . (!empty($model->versions) ? $model->versions[0]->version : 1),
                                    'class' => 'printButton doc-action',
                                    'onclick' => 'event.preventDefault();',
                                    'data-pjax' => '0',
                                    'title' => 'Print Stamp with QR Code']);
                            },
                            'download' => function ($url, $model, $key) {
                                if ($model->versions && Yii::$app->user->can('/document/download')) {
                                    return Html::a('<i class="mdi mdi-download mdi-24px"></i> ', Yii::$app->homeUrl . 'document/download?id=' . $key . '&version=' . $model->versions[0]->version, [
                                                'class' => 'doc-action',
                                                'title' => Yii::t('yii', 'Download Document'),
                                                'data-id' => $key,
                                                'data-pjax' => '0',
                                                'target' => '_blank',
                                    ]);
                                }
                                return NULL;
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<i class="mdi mdi-eye mdi-24px"></i> ', Yii::$app->homeUrl . 'document/view?id=' . $key, [
                                            'class' => 'doc-action',
                                            'title' => Yii::t('yii', 'View Document'),
                                            'data-id' => $key,
                                            'data-pjax' => '0',
                                            'target' => '_blank',
                                ]);
                            },
                            'edit' => function ($url, $model, $key) {
                                if (Yii::$app->user->can('/document/update')) {
                                    return Html::a('<i class="mdi mdi-pencil-box mdi-24px"></i> ', Yii::$app->homeUrl . 'document/update?id=' . $key, [
                                                'class' => 'doc-action',
                                                'title' => Yii::t('yii', 'Edit Document'),
                                                'data-id' => $key,
                                                'data-pjax' => '0',
                                                'target' => '_blank',
                                    ]);
                                }
                                return NULL;
                            },
                            'delete' => function ($url, $model) {
                                if (Yii::$app->user->can('/document/delete')) {
                                    return Html::a('<i class="mdi mdi-delete mdi-24px"></i>', '#', [
                                                'class' => 'doc-action',
                                                'title' => Yii::t('yii', 'Delete'),
                                                'rel' => 'tooltip',
                                                'aria-label' => Yii::t('yii', 'Delete'),
                                                'onclick' => "
                                            swal({
                                            title: '" . 'Are you sure you want to delete this document?' . "', icon: 'warning',buttons: [  'Cancel', 'Yes' ],dangerMode: true,
                                            }).then(function(isConfirm) {                        
                                                if (isConfirm) {
                                                    $.ajax('" . Yii::$app->homeUrl . "document/delete?id={$model->id}', {
                                                        type: 'POST'
                                                    }).done(function(data) {
                                                        $.pjax.reload({container: '#documents-listing'});
                                                        notifications();
                                                    });
                                                }
                                                return false;
                                            });",
                                    ]);
                                }
                                return NULL;
                            },
                        ],
                    ],
                ];
                ?>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
//                    'options' => ['class' => 'documents'],
                    'tableOptions' => [
                        'class' => 'documents'
                    ],
                    'layout' => "<div class=\"row\"><div class=\"col-md-12\">{items}</div></div><div class=\"row\"><div class=\"col-md-5\">{summary}</div><div class=\"col-md-7\"><div class='dataTables_paginate paging_full_numbers'>{pager}</div></div>",
                    'pager' => [
                        'firstPageLabel' => 'First',
                        'lastPageLabel' => 'Last',
                    ],
                    'summary' => '<div class="dataTables_info">Showing {begin} to {end} of {totalCount} entries </div>',
                    'responsiveWrap' => false,
                    'pjax' => true,
                    'pjaxSettings' => [
                        'neverTimeout' => true,
                        'options' => [
                            'id' => 'documents-listing',
                        ],
                    ],
                    'columns' => $gridColumn,
                    'export' => false,
                    // your toolbar can include the additional full export menu
                    'toolbar' => [
                        '{export}',
                        ExportMenu::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => $gridColumn,
                            'target' => ExportMenu::TARGET_BLANK,
                            'fontAwesome' => true,
                            'dropdownOptions' => [
                                'label' => 'Full',
                                'class' => 'btn btn-default',
                                'itemsBefore' => [
                                    '<li class="dropdown-header">Export All Data</li>',
                                ],
                            ],
                            'exportConfig' => [
                                ExportMenu::FORMAT_PDF => false
                            ]
                        ]),
                    ],
                ]);
                ?>



            </div>
        </div>
    </div>          

</div>



<div id="emailCompose"></div>

<!-- Show Document on modal -->
<div id="view_document" class="modal bs-example-modal-lg" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 950px">
        <div class="modal-content">
            <div class="modal-header">                    
                <h3 id="doc_title"></h3>

                <!--                    <div class="text-center">
                                        <button type="button" name="next" class="btn mdc-button mdc-button--raised icon-button filled-button--light" id="btnprev"><i class="mdi mdi-arrow-left-bold-circle"></i></button>
                                        <button type="button" name="next" class="btn mdc-button mdc-button--raised icon-button filled-button--light" id="btnnext"> <i class="mdi mdi-arrow-right-bold-circle"></i></button>
                                    </div>-->
                <button class="btn mdc-button mdc-button--raised icon-button filled-button--light modal-close" data-dismiss="modal"><i class="mdi mdi-close"></i></button>
            </div>
            <div class="modal-body">
                <div id="viewDoc"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>  


<?php
$script = "

var documentChecked = [];
$(document).on('change', '.kv-row-checkbox', function (event) {
    event.preventDefault();
    if ($(this).is(':checked')) {
        $('#multiple_select').show();    
        documentChecked.push(parseInt($(this).val()));
        //console.log(documentChecked);
    }else {        
        $('#multiple_select').hide();
        var itemtoRemove = parseInt($(this).val());
        documentChecked.splice($.inArray(itemtoRemove, documentChecked), 1);
        //console.log(documentChecked);
        
    }
    return false;
});


 $(document).on('click', '.deleteSelection', function(e) {
        e.preventDefault();    
        swal({
            title: 'Are you sure you want to delete this document?', icon: 'warning',buttons: [  'Cancel', 'Yes' ],dangerMode: true,
            }).then(function(isConfirm) {                        
                if (isConfirm) {
                console.log(documentChecked);
                    $.ajax('" . Yii::$app->homeUrl . "document/delete-all', {
                        type: 'POST',
                        data: {ids : documentChecked},
                       
                    }).done(function(data) {
                        
                        $.pjax.reload({container: '#documents-listing'});   
                        $('#multiple_select').hide();
                        documentChecked = [];
                    });
                }
                return false;
            });
});


$(document).on('click', '.bulkDownload', function(e) {
        e.preventDefault();            
        
        if(documentChecked){
            $.ajax('" . Yii::$app->homeUrl . "document/bulk-download', {
                    type: 'POST',
                    data: {ids : documentChecked},
                    
                }).done(function(response) {
                    documentChecked = [];
                    window.location = response;
//                    var link=document.createElement('a');
//                    document.body.appendChild(link);
//                    link.href=url;
//                    link.click();                    
                    $.pjax.reload({container: '#documents-listing'});     
                    $('#multiple_select').hide();
                    
                });
        }            
                return false;
        
});




// open modal to Send email 
$(document).on('click', '.email-modal', function (event) {
    event.preventDefault();   
    var url = $(this).attr('data-action');
    var id = $(this).attr('data-key');
    var title = $(this).attr('data-title');
    var attachment = $(this).attr('data-attachment');
    $('#emailCompose').empty();
    var url = '" . Url::to(['email']) . "';
    $.post(url, {id: id}, function (data) {

        $('#emailCompose').html(data);
        $('#send_document').modal(); 
        $('#doc_id').val(id);
        $('#document_subject').val(title);
        $('#email_list').val(id);
        $('#doc_attachment').html(attachment); 
        
        // show list of the email sent
        $.each( data, function( key, value ) {
            $('#show_email').html('<tr><td class=\"pr-0 text-left\">'+ value.subject +'</td><td class=\"pr-0\">'+ value.version +'</td><td class=\"pr-0\">'+ value.email_receiver +'</td><td class=\"pr-0\">'+ value.sender +'</td><td class=\"pr-0\">'+ value.created_at +'</td></tr>');
        });
    });
   
     return false;
    
});







// preview document in modal
$(document).on('click', '.preview-modal', function (event) {
    event.preventDefault();   
    
    var url = $(this).attr('data-action');
    var key = $(this).attr('data-key');    
    var title = $(this).attr('data-title');
    
//    $.each($('input[name=\"selection[]\"]:checked'), function () {
//        $('#customCheck' + parseInt($(this).val())).prop('checked', false);
//        $(this).closest('tr').removeClass('success');
//    });
//    $('#customCheck' + key).prop('checked', true);
//    $(this).closest('tr').addClass('success');
    
    $.post(url, {id: key}, function (data) {                      
       $('#viewDoc').html(data);    
       $('#doc_title').html(title);
    });
    return false;
});







// Next document in view modal
$(document).on('click','#btnnext',function(e){
    e.preventDefault();   
    var tr = $('.documents').find('tr.success');
    if($(tr).closest('tr').next('tr').is('tr')){
        var nexttr = $(tr).closest('tr').next('tr');
        $(tr).closest('tr').removeClass('success');
        $(nexttr).closest('tr').addClass('success');
        var document = $(nexttr).attr('data-key');
        
        $('#customCheck' + document).prop('checked', true);
        $.each($('input[name=\"selection[]\"]:checked'), function () {
            $('#customCheck' + parseInt($(this).val())).prop('checked', false);
            $(nexttr).closest('tr').removeClass('success');
        });
        $('#customCheck' + document).prop('checked', true);
        $(nexttr).closest('tr').addClass('success');
        var url = $(nexttr).find('.preview-modal').attr('data-action');
        var title = $(nexttr).find('.preview-modal').attr('data-title');
        $.post(url, {id: document}, function (data) {
            if (data) {
                $('#viewDoc').empty();
                $('#viewDoc').html(data);
                $('#doc_title').html(title);
                $('#view_document').modal('show');                
            } else {
                $('#view_document').modal('hide');
            }
        });        
    }
    return false;
});


// Prev documents in view modal
$(document).on('click','#btnprev',function(e){
    e.preventDefault();   
    var tr = $('.documents').find('tr.success');
    if($(tr).closest('tr').prev('tr').is('tr')){
        var prevtr = $(tr).closest('tr').prev('tr');
        $(tr).closest('tr').removeClass('success');
        $(prevtr).closest('tr').addClass('success');
        var document = $(prevtr).attr('data-key');        
        $('#customCheck' + document).prop('checked', true);
        
        var url = $(prevtr).find('.preview-modal').attr('data-action');
        var title = $(prevtr).find('.preview-modal').attr('data-title');
        $.post(url, {id: document}, function (data) {
            if (data) {
                $('#viewDoc').empty();
                $('#viewDoc').html(data);
                $('#doc_title').html(title);
                $('#view_document').modal('show');                
            } else {
                $('#view_document').modal('hide');
            }
        });        
    }
    return false;
});

$(document).on('pjax:end', function(e) {
        e.preventDefault();
   $('.popover').hide();     
});

";
$this->registerJs($script, \yii\web\View::POS_END, "script11");
?>
