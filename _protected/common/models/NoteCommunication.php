<?php

namespace common\models;

use Yii;
use Imagick;
use Exception;
use yii\db\Expression;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use common\helpers\ConstHelper;
use common\widgets\signature\Signature;
use common\exceptions\NoteException;
use common\exceptions\OrderNotFoundException;
use common\exceptions\UserNotFoundException;
use common\exceptions\VisitNotFoundException;

/**
 * Class NoteCommunication
 * @package common\models
 */
class NoteCommunication extends base\NoteCommunication
{
    /**
     * Checks if note is pending
     * @return bool
     */
    public function isPending()
    {
        return (empty($this->status) || $this->status === ConstHelper::NOTE_STATUS_PENDING);
    }

    /**
     * Checks if note is submitted
     * @return bool
     */
    public function isSubmitted()
    {
        return (!empty($this->status) && $this->status === ConstHelper::NOTE_STATUS_SUBMITTED);
    }

    /**
     * Checks if note is accepted
     * @return bool
     */
    public function isAccepted()
    {
        return (!empty($this->status) && $this->status === ConstHelper::NOTE_STATUS_ACCEPTED);
    }

    /**
     * Find model by existing provider, order and visit
     * @param null|int $provider_id
     * @return $this|null
     * @throws NotFoundHttpException
     */
    public function findExistingModel($provider_id = null)
    {
        $provider_id = $this->provider_id ?: $provider_id;
        if (($provider = User::find()->provider(true)->where(['id' => $provider_id])->one()) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Therapist not found.'));
        }

        if (($order = Order::find()->joinWith('orderUsers')->where(['id' => $this->order_id, 'user_order.user_id' => $provider->id])->one()) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Service Request not found.'));
        }

        if (($visit = Visit::findOne(['id' => $this->visit_id, 'order_id' => $order->id])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Visit not found.'));
        }

        return self::findOne(['provider_id' => $provider->id, 'order_id' => $order->id, 'visit_id' => $visit->id]);
    }

    /**
     * Sets attributes from existing model
     * @throws OrderNotFoundException
     * @throws UserNotFoundException
     * @throws VisitNotFoundException
     * @return void
     */
    public function setExistingAttributes()
    {
        if (empty($this->provider)){
            throw new UserNotFoundException('Therapist not found.');
        }
        if (empty($this->order)){
            throw new OrderNotFoundException('Service Request not found.');
        }
        if (empty($this->visit)){
            throw new VisitNotFoundException('Visit not found.');
        }

        // Sets pre-defined attributes in from the order, provider and visit
        $this->setAttributes([
            'provider_id' => $this->provider->id,
            'order_id' => $this->order->id,
            'visit_id' => $this->visit->id,
            'patient_name' => $this->order->patient->patientFullName,
            'patient_address' => $this->order->patient->patientAddress,
            'patient_phone_number' => $this->order->patient->phone_number,
            'mrn' => $this->order->patient_number,
            'dob' => $this->order->patient->birth_date,
            'health_agency' => $this->order->patient->customer->agency_name,
            'physician_name' => $this->order->physician_name,
            'physician_address' => $this->order->physician_address,
            'physician_phone_number' => $this->order->physician_phone_number,
            'therapist_name' => $this->provider->getUserFullName(),
            'therapist_title' => $this->provider->title
        ]);

        // Submit current note
        if (!empty($this->submit)) {
            $this->setAttributes([
                'status' => ConstHelper::NOTE_STATUS_SUBMITTED,
                'submitted_by' => Yii::$app->user->id,
                'submitted_at' => new Expression('NOW()')
            ]);
        }

        // Save signature for current logged user
        if (!empty($this->save_signature) && Yii::$app->user->identity->isProvider) {
            UserSignature::saveSignature(Yii::$app->user->id, $this->therapist_signature);
        }
    }

    /**
     * Gets stored signature and converts it to pdf file. Finally stores it by given file name
     * @param &string $signature Temp file name to store signature pdf file
     * @return null|bool
     */
    public function getRPTProviderSignature(&$signature)
    {
        // convert signature.png -resize 70% -transparent white -page Letter+100-5 -quality 75 signature_communication.pdf
        // $signature = Yii::getAlias('@common/pdf/signature_communication.pdf');
        if(empty($this->id) || !is_file($signature) || !is_readable($signature)){
            return false;
        }

        try {
            if(empty($this->therapist_signature)) {
                throw new NoteException('There are no signature data for this note.');
            }

            $svgSignature = Signature::getSignatureService()->base30ToSVG($this->therapist_signature);

            $image = new Imagick();
            $image->readImageBlob($svgSignature);
            $image->scaleImage(200,60, true);
            $image->setImagePage(816, 1055, 120, 315);
            $image->transparentPaintImage('white', 0.0, 1, false);
            $image->setFormat('pdf');
            $image->writeImage($signature);
            $image->clear();
            $image->destroy();

        } catch(Exception $e) {
            Yii::error('Signature PDF for Communication Note failed to create. '. $e->getMessage(), 'NoteCommunication::'.__FUNCTION__);
            return false;
        }
        return true;
    }

    /**
     * Returns therapist name and title
     * @return string
     */
    public function therapistNameTitle()
    {
        if($this->isNewRecord) {
            $user = User::currentLoggedUser();
            $name = $user->getUserFullName();
            $title = $user->title;
        }else{
            $name = $this->therapist_name;
            $title = $this->therapist_title;
        }

        return Html::tag('div', Yii::t('app', 'Name: {name}', ['name' => $name])) .  Html::tag('div', Yii::t('app', 'Title: {title} ', ['title' => $title]));
    }


}