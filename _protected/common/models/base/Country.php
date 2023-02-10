<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveRecord;
use common\models\queries\CountryQuery;

/**
 * This is the model class for table "{{%country}}".
 *
 * @property int $id
 * @property string $iso
 * @property string $iso3
 * @property string $fips
 * @property string $country_name
 * @property string $continent
 * @property string $currency_code
 * @property string $currency_name
 * @property string $phone_prefix
 * @property string $postal_code
 * @property string $languages
 * @property string $geo_name_id
 * @property int $status
 */
class Country extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%country}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['iso', 'fips', 'continent'], 'string', 'max' => 2],
            [['iso3', 'currency_code'], 'string', 'max' => 3],
            [['country_name', 'currency_name', 'languages'], 'string', 'max' => 255],
            [['phone_prefix', 'geo_name_id'], 'string', 'max' => 20],
            [['postal_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'iso' => Yii::t('app', 'Iso'),
            'iso3' => Yii::t('app', 'Iso3'),
            'fips' => Yii::t('app', 'Fips'),
            'country_name' => Yii::t('app', 'Country Name'),
            'continent' => Yii::t('app', 'Continent'),
            'currency_code' => Yii::t('app', 'Currency Code'),
            'currency_name' => Yii::t('app', 'Currency Name'),
            'phone_prefix' => Yii::t('app', 'Phone Prefix'),
            'postal_code' => Yii::t('app', 'Postal Code'),
            'languages' => Yii::t('app', 'Languages'),
            'geo_name_id' => Yii::t('app', 'Geo Name ID'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return CountryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CountryQuery(get_called_class());
    }
}
