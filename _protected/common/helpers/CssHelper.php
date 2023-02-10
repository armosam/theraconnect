<?php

namespace common\helpers;

use common\models\Article;
use function GuzzleHttp\Psr7\str;

/**
 * Css helper class.
 */
class CssHelper
{
    /**
     * Returns the appropriate css class based on the value of user $status.
     * NOTE: used in user/index view.
     *
     * @param  string $status User status.
     * @return string Css class.
     */
    public static function statusCss($status)
    {
        if ($status === ConstHelper::STATUS_ACTIVE) {
            return "boolean-true";
        } else {
            return "boolean-false";
        }      
    }

    /**
     * Returns the appropriate css class based on the value of user status.
     * NOTE: used in user/index view.
     *
     * @param  string $status user status.
     * @return string Css class.
     */
    public static function userStatusCss($status)
    {
        if(empty($status)) {return 'status';}
        $status = mb_strtolower(trim($status));
        return "status user-status-{$status}";
    }

    /**
     * Returns the appropriate css class based on the value of order status.
     * NOTE: used in order/index view.
     *
     * @param  string $status Order status.
     * @return string Css class.
     */
    public static function orderStatusCss($status)
    {
        if(empty($status)) {return 'status';}
        $status = mb_strtolower(trim($status));
        return "status order-status-{$status}";
    }

    /**
     * Returns the appropriate css class based on the frequency_status of order.
     * NOTE: used in order/index view.
     *
     * @param  string $status Order frequency_status.
     * @return string Css class.
     */
    public static function orderFrequencyCss($status)
    {
        if(empty($status)) {return 'status';}
        $status = mb_strtolower(trim($status));
        return "status order-frequency-status-{$status}";
    }

    /**
     * Returns the appropriate css class based on the value of note status.
     * NOTE: used in note-* index views.
     *
     * @param  string $status Note status.
     * @return string Css class.
     */
    public static function noteStatusCss($status)
    {
        if(empty($status)) {return 'status';}
        $status = mb_strtolower(trim($status));
        return "status note-status-{$status}";
    }

    /**
     * Returns the appropriate css class based on the value of YesNo fields.
     * NOTE: used in order/_form view.
     *
     * @param  string $status Selected value.
     * @return string         Css class.
     */
    public static function yesNoCss($status)
    {
        if ($status === ConstHelper::FLAG_YES) {
            return "boolean-true";
        } else {
            return "boolean-false";
        }
    }

    /**
     * Returns the appropriate css class based on the value of role $item_name.
     * NOTE: used in user/index view.
     *
     * @param  string $role Role name.
     * @return string Css class.
     */
    public static function roleCss($role)
    {
        if(empty($status)) {return 'role';}
        $role = mb_strtolower(trim($role));
        return "role role-{$role}";
    }

    /**
     * Returns the appropriate css class based on the value of Article $status.
     * NOTE: used in article/admin view.
     *
     * @param  string $status Article status.
     * @return string Css class.
     */
    public static function articleStatusCss($status)
    {
        if ($status === Article::STATUS_PUBLISHED) {
            return "boolean-true";
        } else {
            return "boolean-false";
        }      
    }  

    /**
     * Returns the appropriate css class based on the value of Article $category.
     * NOTE: used in article/admin view.
     *
     * @param  string $category Article category.
     * @return string Css class.
     */
    public static function articleCategoryCss($category)
    {
        switch ($category){
            case Article::CATEGORY_SOCIETY:
                return "color blue";
            case Article::CATEGORY_DISCOUNT:
                return "color green";
            case Article::CATEGORY_NEWS:
                return "color yellow";
            default:
                return "";
        }
    }
}