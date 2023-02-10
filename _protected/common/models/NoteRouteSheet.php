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
 * Class NoteRouteSheet
 * @package common\models
 *
 * @property array|string $visit_code
 */
class NoteRouteSheet extends base\NoteRouteSheet
{
    /**
     * Returns list of possible visit codes or selected one
     * @param false $selected
     * @return array|mixed
     */
    public static function getVisitCode($selected = false)
    {
        $data = [
            self::ROUTE_SHEET_VISIT_CODE_EVAL=>Yii::t('app', 'Eval'),
            self::ROUTE_SHEET_VISIT_CODE_RECERTIFICATION=>Yii::t('app', 'Re-certification'),
            self::ROUTE_SHEET_VISIT_CODE_FOLLOW_UP=>Yii::t('app', 'Follow up visit'),
            self::ROUTE_SHEET_VISIT_CODE_RESUMPTION_OF_CARE=>Yii::t('app', 'Resumption of care'),
            self::ROUTE_SHEET_VISIT_CODE_DISCHARGE=>Yii::t('app', 'Discharge'),
            self::ROUTE_SHEET_VISIT_CODE_OTHER=>Yii::t('app', 'Other'),
        ];
        if($selected !== false){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

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
            'visit_total_time' => ConstHelper::calculateDuration($this->time_in, $this->time_out),
            'patient_name' => $this->order->patient->patientFullName,
            'mrn' => $this->order->patient_number,
            'health_agency' => $this->order->patient->customer->agency_name,
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
        // convert signature.png -resize 70% -transparent white -page Letter+400+370 -quality 75 signature_route.pdf
        // $signature = Yii::getAlias('@common/pdf/signature_route.pdf');
        if(empty($this->id) || !is_file($signature) || !is_readable($signature)){
            return false;
        }

        try {
            if(empty($this->patient_signature) && empty($this->therapist_signature)) {
                throw new NoteException('There are no signature data for this note.');
            }

            $images = new Imagick();

            if (!empty($this->patient_signature)) {

                $svgPatientSignature = Signature::getSignatureService()->base30ToSVG($this->patient_signature);
                $image1 = new Imagick();
                $image1->readImageBlob($svgPatientSignature);
                $image1->scaleImage(150, 50, true);
                $image1->setImagePage(816, 1055, 70, 0);
                $image1->transparentPaintImage('white', 0.0, 1, false);

                $images->addImage($image1);
                $image1->clear();
                $image1->destroy();
            }

            if (!empty($this->therapist_signature)) {

                $svgTherapistSignature = Signature::getSignatureService()->base30ToSVG($this->therapist_signature);
                $image2 = new Imagick();
                $image2->readImageBlob($svgTherapistSignature);
                $image2->scaleImage(200, 50, true);
                $image2->setImagePage(816, 1055, 0, 250);
                $image2->transparentPaintImage('white', 0.0, 1, false);

                $images->addImage($image2);
                $image2->clear();
                $image2->destroy();
            }

            $image = $images->mergeImageLayers(Imagick::LAYERMETHOD_OPTIMIZE);
            $images->clear();
            $images->destroy();

            $image->setImagePage(816, 1055, 390, 330);
            $image->transparentPaintImage('white', 0.0, 1, false);
            $image->setFormat('pdf');
            $image->writeImage($signature);
            $image->clear();
            $image->destroy();

        } catch(Exception $e) {
            Yii::error('Signature PDF for Route Sheet failed to create. '. $e->getMessage(), 'NoteRouteSheet::'.__FUNCTION__);
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

        return Html::tag('div', Yii::t('app', 'Name: {name}', ['name' => $name])) . Html::tag('div', Yii::t('app', 'Title: {title} ', ['title' => $title]));
    }
}