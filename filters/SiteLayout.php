<?php


namespace app\filters;

use app\commands\RbacController;
use \yii\helpers\Url;
use \app\models\Users;

/**
 * Created by PhpStorm.
 * User: DezMonT
 * Date: 28.02.2015
 * Time: 19:34
 * @method static profile()
 * @method static users()
 * @method static beacons()
 * @method static login()
 * @method static register()
 * @method static groups()
 */
class SiteLayout extends LayoutFilter
{


    const place_left_nav = 'left_nav';
    const place_right_nav = 'right_nav';

    public static  function getActiveMap()
    {
        return  [
            'login' => [SiteLayout::login()]  ,
            'register' => [SiteLayout::register()]  ,
        ];
    }

    public static function getParams()
    {
        $params = isset($_GET['id']) ? ['id'=>$_GET['id']] : [];
        return $params;
    }
    public static function layout($active = array())
    {

        $user_role = self::getRole();
        $nav_bar = [];
        switch($user_role)
        {
            case 'Guest' : $nav_bar = [
                self::place_left_nav =>[],
                self::place_right_nav => self::getGuestRightNav($active),
            ];
                break;
            case RbacController::user : $nav_bar = [
                self::place_left_nav => self::getLeftTabs($active),
                self::place_right_nav => self::getRightNav($active),
            ];
                break;
            case RbacController::admin :
            case RbacController::super_admin : $nav_bar = [
                self::place_left_nav => self::getAdminLeftTabs($active),
                self::place_right_nav => self::getRightNav($active),
            ];
        }

        return $nav_bar;
    }


    public static function getGuestLeftTabs($active)
    {
        return [
            ['label'=>'Login','url'=>Url::to(['site/login']),'active'=>self::getActive($active,self::login())],
            ['label'=>'Register','url'=>Url::to(['site/register']),'active'=>self::getActive($active,self::register())]
        ];
    }

    public static function getLeftTabs($active)
    {

        $tabs = [
            ['label'=>'My Beacons','url'=>Url::to(['beacon/index']),'active'=>self::getActive($active,self::beacons())]
        ];
        if(self::getActive($active,self::profile()))
        {
               $user = Users::getLogged(true);
               $tabs[] =
                   ['label'=>'My profile','url'=>Url::to(['user/view','id'=>$user->id]),'active'=>self::getActive($active,self::profile())];
        }
        return $tabs;
    }

    public static function getAdminLeftTabs($active)
    {
        $tabs = [
            ['label'=>'Users','url'=>Url::to(['user/list']),'active'=>self::getActive($active,self::users())],
            ['label'=>'Beacons','url'=>Url::to(['beacon/list']),'active'=>self::getActive($active,self::beacons())],
            ['label'=>'Groups','url'=>Url::to(['group/list']),'active'=>self::getActive($active,self::groups())],
        ];
        return $tabs;
    }


    public static  function getGuestRightNav($active)
    {
        return [
            ['label'=>'Login','url'=>Url::to(['site/login']),'active'=>self::getActive($active,self::login())],
            ['label'=>'Register','url'=>Url::to(['site/register']),'active'=>self::getActive($active,self::register())]
        ];
    }

    public static function getRightNav()
    {
        $user = Users::getLogged(true);
        return [
            ['label'=>'Hello, '.$user->email,'items'=>[
                ['label'=>'My profile','url'=>Url::to(['user/view','id'=>$user->id])],
                ['label'=>'Log out','url'=>['site/logout']]
            ]],
        ];
    }




}