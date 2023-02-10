<?php

namespace common\models;

use Yii;
use Exception;
use yii\db\Expression;
use yii\helpers\Html;
use yii\web\UploadedFile;
use common\helpers\ConstHelper;
use common\helpers\ArrayHelper;
use common\exceptions\OrderException;
use common\exceptions\ServiceException;
use common\exceptions\OrderAcceptException;
use common\exceptions\UserNotFoundException;
use common\exceptions\OrderCreateException;
use common\exceptions\PatientNotFoundException;

/**
 * This is the model class for table "{{%order}}".
 * @property string|array $statusList
 * @property string|array $documentList
 */
class Order extends base\Order
{
    /**
     * Setting some attributes automatically after an insert or update of the table
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        // Upload Intake document to the server
        if ($this->intake_file instanceof UploadedFile && is_readable($this->intake_file->tempName)) {
            $orderDocument = new OrderDocument();
            $orderDocument->setAttribute('order_id', $this->id);
            $orderDocument->setAttribute('document_type', OrderDocument::DOCUMENT_TYPE_INTAKE);
            $orderDocument->setAttribute('status', ConstHelper::STATUS_ACTIVE);
            $orderDocument->upload_file = $this->intake_file;
            if (!$orderDocument->save()) {
                Yii::error('Intake Document failed to save' . PHP_EOL . print_r($orderDocument->errors, true) , 'Order::' . __FUNCTION__);
                Yii::$app->session->addFlash('error', Yii::t('app', 'Intake Document failed to save. '));
            }
        }

        // Upload Form-485 document to the server
        if ($this->form_485_file instanceof UploadedFile && is_readable($this->form_485_file->tempName)) {
            $orderDocument = new OrderDocument();
            $orderDocument->setAttribute('order_id', $this->id);
            $orderDocument->setAttribute('document_type', OrderDocument::DOCUMENT_TYPE_FORM485);
            $orderDocument->setAttribute('status', ConstHelper::STATUS_ACTIVE);
            $orderDocument->upload_file = $this->form_485_file;
            if (!$orderDocument->save()) {
                Yii::error('Form-485 Document failed to save' . PHP_EOL . print_r($orderDocument->errors, true) , 'Order::' . __FUNCTION__);
                Yii::$app->session->addFlash('error', Yii::t('app', 'Form-485 Document failed to save. '));
            }
        }

        // Upload Other document to the server
        if ($this->other_file instanceof UploadedFile && is_readable($this->other_file->tempName)) {
            $orderDocument = new OrderDocument();
            $orderDocument->setAttribute('order_id', $this->id);
            $orderDocument->setAttribute('document_type', OrderDocument::DOCUMENT_TYPE_OTHER);
            $orderDocument->setAttribute('status', ConstHelper::STATUS_ACTIVE);
            $orderDocument->upload_file = $this->other_file;
            if (!$orderDocument->save()) {
                Yii::error('Other Document failed to save' . PHP_EOL . print_r($orderDocument->errors, true) , 'Order::' . __FUNCTION__);
                Yii::$app->session->addFlash('error', Yii::t('app', 'Other Document failed to save. '));
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Returns the possible values of status fields.
     *
     * @param null|string $selected
     * @return array|string Array of possible states of status.
     */
    public static function getStatusList($selected = null)
    {
        $data = [
            self::ORDER_STATUS_PENDING => Yii::t('app', 'Pending'),
            self::ORDER_STATUS_SUBMITTED => Yii::t('app', 'Submitted'),
            self::ORDER_STATUS_ACCEPTED => Yii::t('app', 'Accepted'),
            self::ORDER_STATUS_COMPLETED => Yii::t('app', 'Completed'),
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns an array of document links
     *
     * @param string $base_route Sources could be order or provider-order
     * @return array
     */
    public function getDocumentList($base_route = 'order')
    {
        $data = [];
        $route = "$base_route/document";
        if($this->orderIntakeDocument){
            $data[] = Html::a('Intake', [$route, 'id' => $this->orderIntakeDocument->id], ['data-pjax' => '0', 'target' => '_blank', 'class' => 'label label-success', 'style' => 'font-size: 80%;']);
        }
        if($this->orderForm485Document){
            $data[] = Html::a('Form-485', [$route, 'id' => $this->orderForm485Document->id], ['data-pjax' => '0', 'target' => '_blank', 'class' => 'label label-success', 'style' => 'font-size: 80%;']);
        }
        if($this->orderOtherDocument){
            $data[] = Html::a('Other', [$route, 'id' => $this->orderOtherDocument->id], ['data-pjax' => '0', 'target' => '_blank', 'class' => 'label label-success', 'style' => 'font-size: 80%;']);
        }
        return $data;
    }

    /**
     * Create order
     *
     * @param bool $auto_submit
     * @throws OrderCreateException
     * @throws PatientNotFoundException
     * @throws ServiceException
     */
    public function createOrder($auto_submit = false)
    {
        if(($patient = Patient::findOne(['id' => $this->patient_id])) === null) {
            throw new PatientNotFoundException('Patient record not found');
        }

        if (($service = Service::findOne($this->service_id)) === null) {
            throw new ServiceException('Service Not Found');
        }

        $this->setScenario(Order::ORDER_SCENARIO_CREATE);

        $this->intake_file = UploadedFile::getInstance($this, 'intake_file');
        $this->form_485_file = UploadedFile::getInstance($this, 'form_485_file');
        $this->other_file = UploadedFile::getInstance($this, 'other_file');

        $this->setAttributes([
            'order_number' => ConstHelper::uid_gen(8),
            'patient_number' => $patient->patient_number,
            'patient_name' => $patient->patientFullName,
            'service_name' => $service->service_name,
            'frequency_status' => empty($this->service_frequency) ? null : Order::ORDER_FREQUENCY_STATUS_APPROVED,
            'status' => Order::ORDER_STATUS_PENDING
        ]);

        if($auto_submit && !empty($this->patient->start_of_care) && !empty($this->certification_start_date) && !empty($this->certification_end_date)) {
            $this->setAttributes([
                'submitted_by' => Yii::$app->user->id,
                'submitted_at' => new Expression('NOW()'),
                'status' => Order::ORDER_STATUS_SUBMITTED
            ]);
        }

        if (!$this->save()) {
            throw new OrderCreateException('Service Request failed to create');
        }
    }

    /**
     * Submit order
     * @return bool
     */
    public function submitOrder()
    {
        $this->setScenario(self::ORDER_SCENARIO_SUBMIT);
        $this->intake_file = UploadedFile::getInstance($this, 'intake_file');
        $this->form_485_file = UploadedFile::getInstance($this, 'form_485_file');
        $this->other_file = UploadedFile::getInstance($this, 'other_file');
        $this->setAttributes([
            'service_frequency' => $this->service_frequency ?: null,
            'frequency_status' => empty($this->service_frequency) ? null : self::ORDER_FREQUENCY_STATUS_APPROVED,
            'submitted_by' => Yii::$app->user->id,
            'submitted_at' => new Expression('NOW()'),
            'status' => self::ORDER_STATUS_SUBMITTED
        ]);
        return $this->save();
    }

    /**
     * Accept order and assign it to the given RPT or PTA
     *
     * @param int|null $rpt_provider_id RPT Provider ID
     * @param int|null $pta_provider_id PTA Provider ID
     * @return bool
     */
    public function acceptOrder($rpt_provider_id = null, $pta_provider_id = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $providers = [];

            if(!empty($rpt_provider_id)) {
                /** @var User $rpt_provider */
                if (($rpt_provider = User::find()->where(['id' => $rpt_provider_id, 'title' => User::USER_TITLE_RPT])->provider(true)->one()) === null) {
                    throw new UserNotFoundException('RPT Therapist record not found');
                }

                // Check if rpt_provider has a service on order
                if (empty($rpt_provider->service) || $this->service_id !== $rpt_provider->service->id) {
                    throw new OrderAcceptException('This RPT Therapist does not have requested service.');
                }

                // Check if $rpt_provider has all approved credentials
                if (!$rpt_provider->isProviderCredentialsApproved()) {
                    throw new OrderException('This RPT Therapist has not approved credential on the profile.');
                }
                $providers[User::USER_TITLE_RPT] = $rpt_provider->id;
            }

            if(!empty($pta_provider_id)) {
                /** @var User $pta_provider */
                if (($pta_provider = User::find()->where(['id' => $pta_provider_id, 'title' => User::USER_TITLE_PTA])->provider(true)->one()) === null) {
                    throw new UserNotFoundException('PTA Therapist record not found');
                }

                // Check if pta_provider has a service on order
                if (empty($pta_provider->service) || $this->service_id !== $pta_provider->service->id) {
                    throw new OrderAcceptException('This PTA Therapist does not have requested service.');
                }

                // Check if $pta_provider has all approved credentials
                if(!$pta_provider->isProviderCredentialsApproved()) {
                    throw new OrderException('This PTA Therapist has not approved credential on the profile.');
                }
                $providers[User::USER_TITLE_PTA] = $pta_provider->id;
            }

            if(empty($providers)) {
                throw new OrderAcceptException('There are no RPT or PTA found to assign with the order.');
            }

            $this->setScenario(self::ORDER_SCENARIO_ACCEPT);
            $this->setAttributes([
                'accepted_by' => Yii::$app->user->id,
                'accepted_at' => new Expression('NOW()'),
                'status' => self::ORDER_STATUS_ACCEPTED
            ]);

            if(!empty($this->orderRPT) && !empty($this->orderPTA)) {
                $this->setAttributes(['allow_transfer_to' => ConstHelper::FLAG_NO]);
            }else{
                $this->setAttributes(['allow_transfer_to' => null]);
            }

            if(!$this->save()) {
                throw new OrderAcceptException('Order record failed to save.');
            }

            UserOrder::deleteAll(['user_id' => $providers, 'order_id' => $this->id]);

            foreach ($providers as $title => $provider_id) {

                // Deactivate existing RPT or PTA providers assigned to this order
                $existingUserOrders = UserOrder::find()->joinWith('user')->where(['order_id' => $this->id, 'user.title' => $title])->all();
                foreach ($existingUserOrders as $existingUserOrder) {
                    $existingUserOrder->status = ConstHelper::STATUS_PASSIVE;
                    $existingUserOrder->update(false);
                }

                // Assign provider to the order
                $userOrder = new UserOrder();
                $userOrder->setAttributes([
                    'user_id' => $provider_id,
                    'order_id' => $this->id,
                    'status' => ConstHelper::STATUS_ACTIVE
                ]);
                if (!$userOrder->save()) {
                    throw new OrderAcceptException('Order not accepted as '.$title.' therapist not assigned.');
                }
            }

            $transaction->commit();

        } catch(Exception $e) {
            $transaction->rollBack();
            Yii::error('Order not accepted. Error: '. $e->getMessage(), 'Order::' . __FUNCTION__);
            return false;
        }
        return true;
    }

    /**
     * Complete order
     * @return bool
     */
    public function completeOrder()
    {
        $this->setScenario(self::ORDER_SCENARIO_COMPLETE);
        $this->setAttributes([
            'completed_by' => Yii::$app->user->id,
            'completed_at' => new Expression('NOW()'),
            'status' => self::ORDER_STATUS_COMPLETED
        ]);
        return $this->save();
    }

    /**
     * Approves order frequency
     * @return bool
     */
    public function approveFrequency()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if(empty($this->service_frequency)) {
                throw new OrderException('Service Frequency cannot be empty.');
            }

            $this->setAttributes([
                'frequency_status' => self::ORDER_FREQUENCY_STATUS_APPROVED
            ]);

            if(!$this->save()) {
                throw new OrderException('Service request failed to save for frequency approval.');
            }

            $this->evalNotes[0]->setAttribute('frequency', $this->service_frequency);
            if(!$this->evalNotes[0]->save()) {
                throw new OrderException('Eval Note failed to save for frequency.');
            }

            $this->supplementalNotes[0]->setAttribute('frequency', $this->service_frequency);
            if(!$this->supplementalNotes[0]->save()) {
                throw new OrderException('Physician Order failed to save for frequency.');
            }

            $transaction->commit();

        } catch(Exception $e) {
            $transaction->rollBack();
            Yii::error('Service Frequency failed to approve. Error: '. $e->getMessage(), 'Order::' . __FUNCTION__);
            return false;
        }
        return true;
    }

    /**
     * Allow order transfer to another provider
     * @return bool
     */
    public function allowProviderTransfer()
    {
        $this->setAttributes([
            'allow_transfer_to' => ConstHelper::FLAG_YES
        ]);
        return $this->save();
    }

    /**
     * Change the provider on the order
     * @param null|int $rpt_provider_id RPT provider ID
     * @param null|int $pta_provider_id PTA provider ID
     * @return bool
     */
    public function changeProvider($rpt_provider_id = null, $pta_provider_id = null)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try{
            if(!empty($rpt_provider_id)) {
                /** @var User $rpt_provider */
                if (($rpt_provider = User::find()->where(['id' => $rpt_provider_id, 'title' => User::USER_TITLE_RPT])->provider(true)->one()) === null) {
                    throw new UserNotFoundException('RPT Therapist record not found');
                }

                // Check if rpt_provider has a service on order
                if (empty($rpt_provider->service) || $this->service_id !== $rpt_provider->service->id) {
                    throw new OrderException('This RPT Therapist does not have requested service.');
                }

                // Check if $rpt_provider has all approved credentials
                if(!$rpt_provider->isProviderCredentialsApproved()) {
                    throw new OrderException('This RPT Therapist has not approved credential on the profile.');
                }

                // Deactivate existing RPT provider assigned to this order if RPT has visits, otherwise if no visits yet remove RPT from order
                UserOrder::deleteAll(['user_order.user_id' => $rpt_provider->id, 'order_id' => $this->id]);
                $userOrders = UserOrder::find()->joinWith('user')->where(['user_order.order_id' => $this->id, 'user.title' => User::USER_TITLE_RPT])->all();
                foreach ($userOrders as $userOrder) {

                    if(empty($userOrder->visits)) {
                        // If no visits for order then remove assigned RPT
                        $userOrder->delete();
                    } else {
                        $isVisited = false;
                        foreach ($userOrder->visits as $visit) {
                            if($visit->routeSheetNote->provider_id === $userOrder->user_id) {
                                $isVisited = true;
                            }
                        }

                        // If provider has at least one visit (route sheet) then make it passive, otherwise remove from user_order
                        if($isVisited) {
                            $userOrder->status = ConstHelper::STATUS_PASSIVE;
                            $userOrder->update(false);
                        } else {
                            $userOrder->delete();
                        }
                    }
                }

                $userOrder = new UserOrder();
                $userOrder->setAttributes([
                    'user_id' => $rpt_provider->id,
                    'order_id' => $this->id,
                    'status' => ConstHelper::STATUS_ACTIVE
                ]);
                if (!$userOrder->save()) {
                    throw new OrderException('RPT therapist not assigned to the patient.');
                }
            }

            if(!empty($pta_provider_id)) {
                /** @var User $pta_provider */
                if (($pta_provider = User::find()->where(['id' => $pta_provider_id, 'title' => User::USER_TITLE_PTA])->provider(true)->one()) === null) {
                    throw new UserNotFoundException('PTA Therapist record not found');
                }

                // Check if pta_provider has a service on order
                if (empty($pta_provider->service) || $this->service_id !== $pta_provider->service->id) {
                    throw new OrderException('This PTA Therapist does not have requested service.');
                }

                // Check if $pta_provider has all approved credentials
                if(!$pta_provider->isProviderCredentialsApproved()) {
                    throw new OrderException('This PTA Therapist has not approved credential on the profile.');
                }

                // Check if patient evaluated and frequency is approved
                if(empty($this->service_frequency) || $this->frequency_status !== self::ORDER_FREQUENCY_STATUS_APPROVED) {
                    throw new OrderException('Order Evaluation is not finished to be able to transfer to PTA Therapist.');
                }

                // Deactivate existing PTA provider assigned to this order if at least one visit by PTA, otherwise remove PTA from order
                UserOrder::deleteAll(['user_id' => $pta_provider->id, 'order_id' => $this->id]);
                $userOrders = UserOrder::find()->joinWith('user')->where(['user_order.order_id' => $this->id, 'user.title' => User::USER_TITLE_PTA])->all();
                foreach ($userOrders as $userOrder) {
                    if(empty($userOrder->visits)) {
                        // If no visits for order then remove assigned PTA
                        $userOrder->delete();
                    } else {
                        $isVisited = false;
                        foreach ($userOrder->visits as $visit) {
                            if(!empty($visit->routeSheetNote) && $visit->routeSheetNote->provider_id === $userOrder->user_id) {
                                $isVisited = true;
                            }
                        }

                        // If provider has at least one visit (route sheet) then make it passive, otherwise remove from user_order
                        if($isVisited) {
                            $userOrder->status = ConstHelper::STATUS_PASSIVE;
                            $userOrder->update(false);
                        } else {
                            $userOrder->delete();
                        }
                    }
                }

                $userOrder = new UserOrder();
                $userOrder->setAttributes([
                    'user_id' => $pta_provider->id,
                    'order_id' => $this->id,
                    'status' => ConstHelper::STATUS_ACTIVE
                ]);
                if (!$userOrder->save()) {
                    throw new OrderException('PTA therapist not assigned to the patient.');
                }
            }

            if(!empty($this->orderRPT) && !empty($this->orderPTA)) {
                $this->setAttributes(['allow_transfer_to' => ConstHelper::FLAG_NO]);
            } else {
                $this->setAttributes(['allow_transfer_to' => null]);
            }

            if(!$this->save()) {
                throw new OrderException('Order record failed to save.');
            }

            $transaction->commit();

        } catch(Exception $e) {
            $transaction->rollBack();
            Yii::error('Therapist not assigned to the patient. Error: '. $e->getMessage(), 'Order::' . __FUNCTION__);
            return false;
        }
        return true;
    }

    /**
     * Returns boolean true if order is ready to be accepted
     * Otherwise returns false
     * @return bool
     */
    public function isReadyToAccept()
    {
        $current = User::currentLoggedUser();

        // Check if if logged user is provider and all credentials are approved
        if(!$current->isProviderCredentialsApproved()) {
            return false;
        }

        //Check if current user has service of order
        if(empty($current->userService) || $current->userService->service_id !== $this->service_id) {
            return false;
        }

        // Check if provider is active
        if ($current->status !== User::USER_STATUS_ACTIVE){
            return false;
        }

        if($current->title === User::USER_TITLE_PTA) {
            // Check if order frequency is approved
            if(empty($this->service_frequency) || $this->frequency_status !== self::ORDER_FREQUENCY_STATUS_APPROVED) {
                return false;
            }

            // Check if order is allowed to transfer
            if($this->allow_transfer_to !== ConstHelper::FLAG_YES) {
                return false;
            }

            // Check if order evaluation is approved
            if(empty($this->visits) || empty($this->visits[0]->evalNote) || !$this->visits[0]->evalNote->isAccepted()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns boolean result is current logged in provider assigned to the order and active
     * @return bool
     */
    public function isLoggedInProviderActiveOnOrder()
    {
        $currentUser = User::currentLoggedUser();
        if(empty($currentUser->userOrders)) {
            return false;
        }

        $userOrders = ArrayHelper::index($currentUser->userOrders, 'order_id');
        return (!empty($userOrders[$this->id]) && ($userOrders[$this->id]->status === ConstHelper::STATUS_ACTIVE));
    }

}