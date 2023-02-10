<?php

namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * TranslateBehavior Behavior. Allows to maintain translations of model.
 *
 * TranslationBehavior for Yii2
 * ==============================
 *
 * This behavior has been inspired by the great work of Mikehaertl's
 * [Translatable Behavior](https://github.com/mikehaertl/translatable) for Yii 1.*.
 *
 * It eases the translation of ActiveRecord's attributes as it maps theme from a translation table into the main record. It
 * also automatically loads application language by default.
 *
 * Sample of use:
 *
 * ```
 * <?php
 *
 * // create a record
 * $tour = new Tour;
 *
 * $tour->title = "English title";
 *
 * // save both the new Tour and a related translation record with the title
 * $tour->save();
 *
 *
 * // change language
 * $tour->language = 'fr';
 *
 * $tour->title = "French title";
 *
 * // save translation only
 * $tour->saveTranslation();
 *
 * ```
 *
 *
 * Usage
 * ----------
 *
 * First you need to move all the attributes that require to be translated into a separated table. For example, imagine we
 * wish to keep translations of title and description from our tour entity. Our schema should result on the following:
 *
 * ```
 *     +--------------+        +--------------+        +-------------------+
 *     |     tour     |        |     tour     |        |      tour_lang    |
 *     +--------------+        +--------------+        +-------------------+
 *     |           id |        |           id |        |                id |
 *     |        title |  --->  |   created_at |   +    |           tour_id |
 *     |  description |        |   updated_at |        |             title |
 *     |   updated_at |        |   updated_at |        |          language |
 *     |   created_at |        +--------------+        |       description |
 *     +--------------+                                +-------------------+
 *
 * ```
 *
 * After we have modified our schema, now we need to define a relation in our `ActiveRecord` object. The following example
 * assumes that we have already created a `TourLang` model (see the schema above):
 *
 *
 * **
 * * @return ActiveQuery
 * *
 * public function getTranslations()
 * {
 *     return $this->hasMany(TourLang::className(), ['tour_id' => 'id']);
 * }
 *
 *
 * use common\components\TranslationBehavior;
 *
 * \\ ...
 *
 * public function behaviors()
 * {
 *     return [
 *         'trans' => [ // name it the way you want
 *             'class' => TranslationBehavior::className(),
 *             // in case you named your relation differently, you can setup its relation name attribute
 *             // 'relation' => 'translations',
 *             // in case you named the language column differently on your translation schema
 *             // 'languageField' => 'language',
 *             'translationAttributes' => [
 *                 'title', 'description'
 *             ]
 *         ],
 *     ];
 * }
 *
 *
 * @author Armen Bablanyan <thera@gmail.com>
 * @link http://www.armos.am/
 * @package armos\translate
 */
class TranslationBehavior extends Behavior
{
    /**
     * @var string the name of the translations relation
     */
    public $relation = 'translations';

    /**
     * @var string the language field used in the related table. Determines the language to query | save.
     */
    public $languageField = 'language';

    /**
     * @var array the list of attributes to translate. You can add validation rules on the owner.
     */
    public $translationAttributes = [];

    /**
     * @var ActiveRecord[] the models holding the translations.
     */
    private $_models = [];

    /**
     * @var string the language selected.
     */
    private $_language;


    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    /**
     * Make [[$translationAttributes]] writable
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->translationAttributes)) {
            $this->getTranslation()->$name = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Make [[$translationAttributes]] readable
     * @inheritdoc
     */
    public function __get($name)
    {
        if (!in_array($name, $this->translationAttributes) && !isset($this->_models[$name])) {
            return parent::__get($name);
        }

        if (isset($this->_models[$name])) {
            return $this->_models[$name];
        }

        $model = $this->getTranslation();
        return $model->$name;
    }

    /**
     * Expose [[$translationAttributes]] writable
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->translationAttributes) ? true : parent::canSetProperty($name, $checkVars);
    }

    /**
     * Expose [[$translationAttributes]] readable
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->translationAttributes) ? true : parent::canGetProperty($name, $checkVars);
    }

    /**
     * @param Event $event
     */
    public function afterFind($event)
    {
        $this->populateTranslations();
        $this->getTranslation($this->getLanguage());
    }

    /**
     * @param Event $event
     */
    public function afterInsert($event)
    {
        $this->saveTranslation();
    }

    /**
     * @param Event $event
     */
    public function afterUpdate($event)
    {
        $this->saveTranslation();
    }

    /**
     * Sets current model's language
     *
     * @param $value
     */
    public function setLanguage($value)
    {
        if (!isset($this->_models[$value])) {
            $this->_models[$value] = $this->loadTranslation($value);
        }
        $this->_language = $value;
    }

    /**
     * Returns current models' language. If null, will return app's configured language.
     * @return string
     */
    public function getLanguage()
    {
        if ($this->_language === null) {
            $this->setLanguage(Yii::$app->language);
        }
        return $this->_language;
    }

    /**
     * Saves current translation model
     * @return bool
     */
    public function saveTranslation()
    {
        $model = $this->getTranslation();
        $dirty = $model->getDirtyAttributes();
        if (empty($dirty)) {
            return true; // we do not need to save anything
        }
        /** @var ActiveQuery $relation */
        $relation = $this->owner->getRelation($this->relation);
        $model->{key($relation->link)} = $this->owner->getPrimaryKey();
        return $model->save();

    }

    /**
     * Returns a related translation model
     *
     * @param string|null $language the language to return. If null, current sys language
     *
     * @return ActiveRecord
     */
    public function getTranslation($language = null)
    {
        if ($language === null) {
            $language = $this->getLanguage();
        }

        if (!isset($this->_models[$language])) {
            $this->_models[$language] = $this->loadTranslation($language);
        }

        return $this->_models[$language];
    }

    /**
     * Loads all specified languages. For example:
     *
     * ```
     * $model->loadTranslations("en-US");
     *
     * $model->loadTranslations(["en-US", "es-ES"]);
     *
     * ```
     *
     * @param string|array $languages
     */
    public function loadTranslations($languages)
    {
        $languages = (array)$languages;

        foreach ($languages as $language) {
            $this->loadTranslation($language);
        }
    }

    /**
     * Loads a specific translation model
     *
     * @param string $language the language to return
     *
     * @return null|ActiveQuery|ActiveRecord|static
     */
    private function loadTranslation($language)
    {
        $translation = null;
        /** @var ActiveQuery $relation */
        $relation = $this->owner->getRelation($this->relation);
        /** @var ActiveRecord $class */
        $class = $relation->modelClass;
        if ($this->owner->getPrimarykey()) {
            $translation = $class::findOne(
                [$this->languageField => $language, key($relation->link) => $this->owner->getPrimarykey()]
            );
        }
        if ($translation === null) {
            $translation = new $class;
            $translation->{key($relation->link)} = $this->owner->getPrimaryKey();
            $translation->{$this->languageField} = $language;
        }

        return $translation;
    }

    /**
     * Populates already loaded translations
     */
    private function populateTranslations()
    {
        //translations
        $aRelated = $this->owner->getRelatedRecords();
        if (isset($aRelated[$this->relation]) && $aRelated[$this->relation] != null) {
            if (is_array($aRelated[$this->relation])) {
                foreach ($aRelated[$this->relation] as $model) {
                    $this->_models[$model->getAttribute($this->languageField)] = $model;
                }
            } else {
                $model = $aRelated[$this->relation];
                $this->_models[$model->getAttribute($this->languageField)] = $model;
            }
        }
    }
} 
