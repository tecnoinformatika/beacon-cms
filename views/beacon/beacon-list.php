<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BeaconsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Beacons');
?>
<div class="beacons-index">

    <br>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'title',
                'format'=>'raw',
                'value'=>function($data)
                {
                    return Html::a($data->title,Url::to(['update','id'=>$data->id]));
                }
            ],
            'description:html',
            'groupName',
             'uuid',
             'major',
             'minor',

            ['class' => 'yii\grid\ActionColumn',
             'controller' => 'beacon',
             'template' => Yii::$app->user->can(\app\commands\RbacController::delete_beacon) ? '{update}{delete}' : '{update}'
            ],
        ],
    ]); ?>

</div>
