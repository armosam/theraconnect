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
use common\models\events\NoteProgressEvent;
use common\exceptions\OrderNotFoundException;
use common\exceptions\UserNotFoundException;
use common\exceptions\VisitNotFoundException;

/**
 * Class NoteProgress
 * @package common\models
 */
class NoteProgress extends base\NoteProgress
{
    public function init()
    {
        $this->on(NoteProgressEvent::EVENT_NOTE_PROGRESS_SUBMITTED_BY_PTA, [NoteProgressEvent::class, 'noteProgressSubmittedByPtaEventHandler'], ['model' => $this]);
        parent::init();
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
     * Checks if note is submitted by PTA only
     * @return bool
     */
    public function isSubmittedByPTA()
    {
        return (!empty($this->status) && $this->status === ConstHelper::NOTE_STATUS_SUBMITTED_NO_RPT);
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

        return self::findOne(['order_id' => $order->id, 'visit_id' => $visit->id]);
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
        if (empty($this->order->orderRPT)){
            throw new UserNotFoundException('RPT Therapist not found.');
        }

        /**
         * @TODO Here should be added therapist_name0 and therapist_title0 ... as RPT
         * There Should be methods to get RPT or PTA from user_order
         */

        // Sets pre-defined attributes in from the order, provider and visit
        $this->setAttributes([
            'provider_id' => $this->provider->id,
            'order_id' => $this->order->id,
            'visit_id' => $this->visit->id,
            'patient_name' => $this->order->patient->patientFullName,
            'mrn' => $this->order->patient_number,
            'dob' => $this->order->patient->birth_date,
            'gender' => $this->order->patient->gender,
            'therapist_name' => !empty($this->order->orderPTA) ? $this->order->orderPTA->getUserFullName() : $this->provider->getUserFullName(),
            'therapist_title' => !empty($this->order->orderPTA) ? $this->order->orderPTA->title : $this->provider->title,
            'therapist_name0' => $this->order->orderRPT->getUserFullName(),
            'therapist_title0' => $this->order->orderRPT->title
        ]);

        // Submit current note
        if (!empty($this->submit)) {
            $this->setAttributes([
                'status' => ($this->order->orderPTA->id === Yii::$app->user->id) ?  ConstHelper::NOTE_STATUS_SUBMITTED_NO_RPT : ConstHelper::NOTE_STATUS_SUBMITTED,
                'submitted_by' => Yii::$app->user->id,
                'submitted_at' => new Expression('NOW()')
            ]);
        }

        /**
         * - Check if progress note is submitted by PTA
         * - Check if note status is submitted
         * - Check id therapist_signature0 is empty
         * Then send notification to the RPT to sign Progress note
         */
        if($this->order->orderPTA->id === Yii::$app->user->id && $this->isSubmittedByPTA() && empty($this->therapist_signature0)) {
            // Send notification to the RPT of order to sign this progress note
            $this->trigger(NoteProgressEvent::EVENT_NOTE_PROGRESS_SUBMITTED_BY_PTA);
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
        // convert signature.png -resize 70% -transparent white -page Letter+320+25 -quality 75 signature_progress.pdf
        // $signature = Yii::getAlias('@common/pdf/signature_progress.pdf');
        if(empty($this->id) || !is_file($signature) || !is_readable($signature)){
            return false;
        }

        try {
            if(empty($this->therapist_signature) && empty($this->therapist_signature0)) {
                throw new NoteException('There are no signature data for this note.');
            }

            $images = new Imagick();

            if (!empty($this->therapist_signature)) {

                $svgTherapistSignature = Signature::getSignatureService()->base30ToSVG($this->therapist_signature);
                $image1 = new Imagick();
                $image1->readImageBlob($svgTherapistSignature);
                $image1->scaleImage(110, 30, true);
                $image1->setImagePage(816, 1055, 0, 0);
                $image1->transparentPaintImage('white', 0.0, 1, false);

                $images->addImage($image1);
                $image1->clear();
                $image1->destroy();
            }

            if (!empty($this->therapist_signature0)) {

                $svgTherapistSignature0 = Signature::getSignatureService()->base30ToSVG($this->therapist_signature0);
                $image2 = new Imagick();
                $image2->readImageBlob($svgTherapistSignature0);
                $image2->scaleImage(110, 30, true);
                $image2->setImagePage(816, 1055, 0, 30);
                $image2->transparentPaintImage('white', 0.0, 1, false);

                $images->addImage($image2);
                $image2->clear();
                $image2->destroy();
            }

            $image = $images->mergeImageLayers(Imagick::LAYERMETHOD_OPTIMIZE);
            $images->clear();
            $images->destroy();

            $image->setImagePage(816, 1055, 320, 30);
            $image->transparentPaintImage('white', 0.0, 1, false);
            $image->setFormat('pdf');
            $image->writeImage($signature);
            $image->clear();
            $image->destroy();

        } catch(Exception $e) {
            Yii::error('Signature PDF for Progress Note failed to create. '. $e->getMessage(), 'NoteProgress::'.__FUNCTION__);
            return false;
        }
        return true;
    }

    /**
     * Note visibility, editable, and required functionality
     *
     * -- If Signed RPT and active on the order
     * if order owner
     *  - RPT Signature: required, editable, shows RPT name
     *  - PTA Signature: notRequired, hidden,  no name
     * if not order owner
     *  - RPT Signature: required, editable, shows RPT name
     *  - PTA Signature: required, readOnly, shows PTA name
     *
     * -- If Signed PTA and active on the order
     * if order owner
     *  - RPT Signature: notRequired, hidden, no name
     *  - PTA Signature: required, editable, shows PTA name
     * if not order owner
     *  - RPT Signature: not required, readOnly, shows RPT name
     *  - PTA Signature: not required, readOnly, shows PTA name
     */

    /**
     * Checks if RPT signature is visible for given RPT user on the note
     * @param int $user_id User ID to be checked
     * @return bool
     */
    public function isRPTSignatureVisible(int $user_id):bool
    {
        if ($this->isRPTSignatureEditable($user_id)){
            return true;
        }
        return false;
    }

    /**
     * Checks if given RPT can edit RPT signature on the note
     * @param int $user_id User ID to be checked
     * @return bool
     */
    public function isRPTSignatureEditable(int $user_id):bool
    {
        if ($this->isRPTSignatureRequired($user_id)){
            return true;
        }
        return false;
    }

    /**
     * Returns RPT therapist name and title
     * @param int $user_id User ID
     * @return string|null
     */
    public function rptTherapistNameTitle(int $user_id)
    {
        if($this->isNewRecord) {
            $user = $this->_getUser($user_id);
            $name = $user->getUserFullName();
            $title = $user->title;
        }else{
            $name = $this->therapist_name0;
            $title = $this->therapist_title0;
        }
        return Html::tag('div', Yii::t('app', 'Name: {name}', ['name' => $name])) .  Html::tag('div', Yii::t('app', 'Title: {title} ', ['title' => $title]));
    }

    /**
     * Checks if RPT signature is visible for given RPT user on the note
     * @param int $user_id User ID to be checked
     * @return bool
     */
    public function isPTASignatureVisible(int $user_id):bool
    {
        if ($this->isPTASignatureRequired($user_id) || $this->isPTASignatureEditable($user_id)){
            return true;
        }

        $user = $this->_getUser($user_id);
        if ($user->isActivePTAOnOrder($this->order->id)){
            return true;
        }

        return false;
    }

    /**
     * Checks if given RPT can edit RPT signature on the note
     * @param int $user_id User ID to be checked
     * @return bool
     */
    public function isPTASignatureEditable(int $user_id):bool
    {
        $user = $this->_getUser($user_id);
        // If Active PTA on the order and owner of the note or it is a new note but owner of visit
        if(!empty($user) && $user->isActivePTAOnOrder($this->order->id)) {

            // Note is not created
            if (empty($this->provider_id)){
                // PTA is owner of visit
                if (!empty($this->visit->created_by) && ($this->visit->created_by === $user->id)){
                    return true;
                }
            }
            // PTA is owner of the note
            if (($this->provider_id === $user->id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns PTA therapist name and title
     * @param int $user_id User ID
     * @return string
     */
    public function ptaTherapistNameTitle(int $user_id)
    {
        if($this->isNewRecord) {
            $user = $this->_getUser($user_id);
            $name = $user->getUserFullName();
            $title = $user->title;
        }else{
            $name = $this->therapist_name;
            $title = $this->therapist_title;
        }
        return Html::tag('div', Yii::t('app', 'Name: {name}', ['name' => $name])) . Html::tag('div', Yii::t('app', 'Title: {title} ', ['title' => $title]));
    }
}