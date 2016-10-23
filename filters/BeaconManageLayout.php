<?php
namespace app\filters;

use app\commands\RbacController;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: DezMonT
 * Date: 28.02.2015
 * Time: 19:34
 * @method static content_elements()
 * @method static content_element_create()
 * @method static beacon_create_button()
 * @method static content_element_create_button()
 */
class BeaconManageLayout extends BeaconLayout
{

    public $layout = 'subTabbedLayout';


    public static function getActiveMap() {
          $map =[  'list' => [BeaconManageLayout::content_elements(),BeaconManageLayout::content_element_create_button(),BeaconManageLayout::listing()],
            'create' => [BeaconManageLayout::content_elements(),BeaconManageLayout::content_elements(),BeaconManageLayout::create()],
            'update' => [BeaconManageLayout::update()],
        ];
        return $map;
    }

    public static function layout($active = []) {
        return parent::layout(ArrayHelper::merge($active,[TabbedLayout::update()])); // TODO: Change the autogenerated stub
    }





    public static function getLeftSubTabs($active) {
        $x = 3;
        $tabs = [
            ['label' => Yii::t('beacon_layout', ':beacon_update'), 'url' => Url::to(['beacon/update'] + $_GET),
             'active' => self::getActive($active, BeaconManageLayout::update())],
            ['label' => Yii::t('beacon_layout', ':beacon_content_elements'),
             'url' => Url::to(['beacon-content-element/list'] + $_GET),
             'active' => self::getActive($active, BeaconManageLayout::content_elements())]
        ];
        return $tabs;
    }



}