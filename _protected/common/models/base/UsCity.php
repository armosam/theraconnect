<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveRecord;
use common\models\queries\UsCityQuery;

/**
 * This is the model class for table "{{%us_city}}".
 *
 * @property int $id
 * @property string $city_name
 * @property string|null $city_ascii
 * @property string|null $state_code
 * @property string|null $state_name
 * @property string|null $county_code
 * @property string|null $county_name
 * @property string|null $lat
 * @property string|null $lng
 * @property int|null $population
 * @property int|null $density
 * @property string $military
 * @property string $incorporated
 * @property string|null $time_zone
 * @property int|null $ranking
 * @property string|null $zips
 */
class UsCity extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%us_city}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_name', 'military', 'incorporated'], 'required'],
            [['population', 'density', 'ranking'], 'default', 'value' => null],
            [['population', 'density', 'ranking'], 'integer'],
            [['zips'], 'string'],
            [['city_name', 'city_ascii', 'state_name', 'county_code', 'county_name', 'lat', 'lng', 'time_zone'], 'string', 'max' => 255],
            [['state_code'], 'string', 'max' => 2],
            [['military', 'incorporated'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'city_name' => Yii::t('app', 'City Name'),
            'city_ascii' => Yii::t('app', 'City Ascii'),
            'state_code' => Yii::t('app', 'State Code'),
            'state_name' => Yii::t('app', 'State Name'),
            'county_code' => Yii::t('app', 'County Code'),
            'county_name' => Yii::t('app', 'County Name'),
            'lat' => Yii::t('app', 'Lat'),
            'lng' => Yii::t('app', 'Lng'),
            'population' => Yii::t('app', 'Population'),
            'density' => Yii::t('app', 'Density'),
            'military' => Yii::t('app', 'Military'),
            'incorporated' => Yii::t('app', 'Incorporated'),
            'time_zone' => Yii::t('app', 'Time Zone'),
            'ranking' => Yii::t('app', 'Ranking'),
            'zips' => Yii::t('app', 'Zips'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return UsCityQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsCityQuery(get_called_class());
    }
}
