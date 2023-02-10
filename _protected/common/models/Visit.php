<?php

namespace common\models;

use Yii;
use DateInterval;
use yii\base\InvalidConfigException;
use common\helpers\ConstHelper;

/**
 * Class Visit
 * @package common\models
 *
 * @property array $notes
 * @property array $submittedNotes
 * @property array $acceptedNotes
 */
class Visit extends base\Visit
{
    protected $visits_asc;
    protected $visits_desc;

    /**
     * Returns true if current visit is first scheduled one
     * @return bool
     */
    public function isFirstVisit()
    {
        if(!empty($this->id)) {
            if (empty($this->visits_asc)){
                $this->visits_asc = self::find()->where(['order_id' => $this->order_id])->orderBy(['visited_at' => SORT_ASC])->all();
            }
            return ($this->visits_asc[0]->id === $this->id);
        }
        return false;
    }

    /**
     * Returns true if current visit is last scheduled one
     * @return bool
     */
    public function isLastVisit()
    {
        if(!empty($this->id)) {
            if (empty($this->visits_desc)) {
                $this->visits_desc = self::find()->where(['order_id' => $this->order_id])->orderBy(['visited_at' => SORT_DESC])->all();
            }
            return ($this->visits_desc[0]->id === $this->id);
        }
        return false;
    }

    /**
     * Checks if current time is equal or close to scheduled visit date time
     * @return bool
     * @throws InvalidConfigException
     */
    public function isVisitTime()
    {
        if(!empty($this->visited_at)) {
            $current = ConstHelper::dateTime('now');
            $visit_start = ConstHelper::dateTime(Yii::$app->formatter->asDatetime($this->visited_at));
            $visit_end = clone $visit_start;
            $visit_end->add(new DateInterval('PT2H'));
            return ($current >= $visit_start && $current <= $visit_end);
        }
        return false;
    }

    /**
     * Checks if visit started
     * @return bool
     * @throws InvalidConfigException
     */
    public function isVisitStarted()
    {
        if(!empty($this->visited_at)) {
            $current = ConstHelper::dateTime('now');
            $visit_start = ConstHelper::dateTime(Yii::$app->formatter->asDatetime($this->visited_at));
            return ($current >= $visit_start);
        }
        return false;
    }

    /**
     * Checks if logged user is visit creator
     * @return bool
     */
    public function isVisitOwner()
    {
        if(Yii::$app->user->isGuest){
            return false;
        }

        if((!empty($this->created_by) && $this->created_by === Yii::$app->user->id)
            || (!empty($this->routeSheetNote) && $this->routeSheetNote->created_by === Yii::$app->user->id)
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns all notes of visit
     * @return array
     */
    public function getNotes()
    {
        $return = [];
        if(!empty($this->communicationNote)){
            $return[] = $this->communicationNote;
        }
        if(!empty($this->supplementalNote)){
            $return[] = $this->supplementalNote;
        }
        if(!empty($this->evalNote)) {
            $return[] = $this->evalNote;
        }
        if(!empty($this->progressNote)) {
            $return[] = $this->progressNote;
        }
        if(!empty($this->routeSheetNote)) {
            $return[] = $this->routeSheetNote;
        }
        if(!empty($this->dischargeOrderNote)) {
            $return[] = $this->dischargeOrderNote;
        }
        if(!empty($this->dischargeSummaryNote)) {
            $return[] = $this->dischargeSummaryNote;
        }

        return $return;
    }

    /**
     * Returns all submitted notes of visit
     * @return array
     */
    public function getSubmittedNotes()
    {
        $return = [];
        if(!empty($this->communicationNote) && $this->communicationNote->isSubmitted()){
            $return[] = $this->communicationNote;
        }
        if(!empty($this->supplementalNote) && $this->supplementalNote->isSubmitted()){
            $return[] = $this->supplementalNote;
        }
        if(!empty($this->evalNote) && $this->evalNote->isSubmitted()) {
            $return[] = $this->evalNote;
        }
        if(!empty($this->progressNote) && $this->progressNote->isSubmitted()) {
            $return[] = $this->progressNote;
        }
        if(!empty($this->routeSheetNote) && $this->routeSheetNote->isSubmitted()) {
            $return[] = $this->routeSheetNote;
        }
        if(!empty($this->dischargeOrderNote) && $this->dischargeOrderNote->isSubmitted()) {
            $return[] = $this->dischargeOrderNote;
        }
        if(!empty($this->dischargeSummaryNote) && $this->dischargeSummaryNote->isSubmitted()) {
            $return[] = $this->dischargeSummaryNote;
        }

        return $return;
    }

    /**
     * Returns all accepted notes of visit
     * @return array
     */
    public function getAcceptedNotes()
    {
        $return = [];
        if(!empty($this->communicationNote) && $this->communicationNote->isAccepted()){
            $return[] = $this->communicationNote;
        }
        if(!empty($this->supplementalNote) && $this->supplementalNote->isAccepted()){
            $return[] = $this->supplementalNote;
        }
        if(!empty($this->evalNote) && $this->evalNote->isAccepted()) {
            $return[] = $this->evalNote;
        }
        if(!empty($this->progressNote) && $this->progressNote->isAccepted()) {
            $return[] = $this->progressNote;
        }
        if(!empty($this->routeSheetNote) && $this->routeSheetNote->isAccepted()) {
            $return[] = $this->routeSheetNote;
        }
        if(!empty($this->dischargeOrderNote) && $this->dischargeOrderNote->isAccepted()) {
            $return[] = $this->dischargeOrderNote;
        }
        if(!empty($this->dischargeSummaryNote) && $this->dischargeSummaryNote->isAccepted()) {
            $return[] = $this->dischargeSummaryNote;
        }

        return $return;
    }
}