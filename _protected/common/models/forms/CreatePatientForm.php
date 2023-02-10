<?php

namespace common\models\forms;

use Yii;
use yii\web\UploadedFile;
use common\models\Order;
use common\models\Patient;
use common\models\Service;
use common\models\OrderDocument;
use common\helpers\ArrayHelper;
use common\helpers\ConstHelper;
use common\exceptions\PatientCreateException;

/**
 * CreatePatientForm is the model.
 */
class CreatePatientForm extends Patient
{
    /** @var int $service_id */
    public $service_id;
    /** @var int $provider_id */
    public $provider_id;
    /** @var int $service_frequency */
    public $service_frequency;
    /** @var string $order_status */
    public $order_status;
    /** @var string $intake_file */
    public $intake_file;
    /** @var string $form_485_file */
    public $form_485_file;
    /** @var string $other_file */
    public $other_file;
    /** @var string $physician_anme */
    public $physician_name;
    /** @var string $physician_address */
    public $physician_address;
    /** @var string $physician_phone_number */
    public $physician_phone_number;
    /** @var string $comment */
    public $comment;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return arrayhelper::merge(parent::rules(), [
            [['service_id'], 'required'],
            [['physician_name', 'physician_address', 'physician_phone_number', 'service_frequency', 'comment'], 'string', 'max' => 255],
            [['order_status'], 'in', 'range' => [Order::ORDER_STATUS_PENDING, Order::ORDER_STATUS_SUBMITTED]],
            [['service_id'], 'safe'],
            [['provider_id'], 'integer'],
            [['intake_file', 'form_485_file', 'other_file'], 'file', 'skipOnEmpty' => true, 'mimeTypes' => 'application/pdf, application/word, plain/text, image/jpg, image/jpeg, image/png', 'extensions' => 'pdf, txt, doc, docx, png, jpg, jpeg'],
        ]);
    }

    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'service_id' => Yii::t('app', 'Service Name'),
            'provider_id' => Yii::t('app', 'Therapist Name'),
            'service_frequency' => Yii::t('app', 'Service Frequency'),
            'order_status' => Yii::t('app', 'Order Status'),
            'intake_file' => Yii::t('app', 'Intake Document'),
            'form_485_file' => Yii::t('app', 'Form-485 Document'),
            'other_file' => Yii::t('app', 'Other Document'),
            'physician_name' => Yii::t('app', 'Physician Name'),
            'physician_address' => Yii::t('app', 'Physician Address'),
            'physician_phone_number' => Yii::t('app', 'Physician Phone Number'),
            'comment' => Yii::t('app', 'Comments')
        ]);
    }

    /**
     * Create patient and service order
     * @throws PatientCreateException
     */
    public function createPatient()
    {
        if(!$this->save()) {
            throw new PatientCreateException('New Patient failed to create');
        }

        $intake_file = UploadedFile::getInstance($this, 'intake_file');
        $form_485_file = UploadedFile::getInstance($this, 'form_485_file');
        $other_file = UploadedFile::getInstance($this, 'other_file');

        // When new patient has been created then iterate by selected services and
        // create order for each service. uploaded documents will be assigned to all orders
        if (!empty($this->service_id) && is_array($this->service_id)) {

            foreach ($this->service_id as $key => $service_id) {
                if (!empty($service_id) && ($service = Service::findOne($service_id)) !== null) {
                    $order = new Order();
                    $order->setScenario(Order::ORDER_SCENARIO_CREATE);
                    $order->setAttributes([
                        'order_number' => ConstHelper::uid_gen(8),
                        'patient_id' => $this->id,
                        'patient_number' => $this->patient_number,
                        'patient_name' => $this->patientFullName,
                        'service_id' => $service->id,
                        'service_name' => $service->service_name,
                        'service_frequency' => null,
                        'physician_name' => $this->physician_name,
                        'physician_address' => $this->physician_address,
                        'physician_phone_number' => $this->physician_phone_number,
                        'comment' => $this->comment,
                        'status' => Order::ORDER_STATUS_PENDING,
                    ]);

                    $deleteTempFile = ($key === array_key_last($this->service_id));

                    if ($order->save()) {
                        // Upload Intake document to the server
                        if ($intake_file instanceof UploadedFile && is_readable($intake_file->tempName)) {
                            $orderDocument = new OrderDocument();
                            $orderDocument->setAttribute('order_id', $order->id);
                            $orderDocument->setAttribute('document_type', OrderDocument::DOCUMENT_TYPE_INTAKE);
                            $orderDocument->setAttribute('status', ConstHelper::STATUS_ACTIVE);
                            $orderDocument->deleteTempFile = $deleteTempFile;
                            $orderDocument->upload_file = $intake_file;
                            if (!$orderDocument->save()) {
                                Yii::error('Intake Document failed to save for order #' . $order->id . PHP_EOL . print_r($orderDocument->errors, true) , 'CreatePatientForm-' . __FUNCTION__);
                                Yii::$app->session->addFlash('error', Yii::t('app', 'Intake Document failed to save for service {service_name}.', [
                                    'service_name' => $service->service_name
                                ]));
                            }
                        }

                        // Upload Form-485 document to the server
                        if ($form_485_file instanceof UploadedFile && is_readable($form_485_file->tempName)) {
                            $orderDocument = new OrderDocument();
                            $orderDocument->setAttribute('order_id', $order->id);
                            $orderDocument->setAttribute('document_type', OrderDocument::DOCUMENT_TYPE_FORM485);
                            $orderDocument->setAttribute('status', ConstHelper::STATUS_ACTIVE);
                            $orderDocument->deleteTempFile = $deleteTempFile;
                            $orderDocument->upload_file = $form_485_file;
                            if (!$orderDocument->save()) {
                                Yii::error('Form-485 Document failed to save for order #' . $order->id . PHP_EOL . print_r($orderDocument->errors, true) , 'CreatePatientForm-' . __FUNCTION__);
                                Yii::$app->session->addFlash('error', Yii::t('app', 'Form-485 Document failed to save for service {service_name}.', [
                                    'service_name' => $service->service_name
                                ]));
                            }
                        }

                        // Upload Other document to the server
                        if ($other_file instanceof UploadedFile && is_readable($other_file->tempName)) {
                            $orderDocument = new OrderDocument();
                            $orderDocument->setAttribute('order_id', $order->id);
                            $orderDocument->setAttribute('document_type', OrderDocument::DOCUMENT_TYPE_OTHER);
                            $orderDocument->setAttribute('status', ConstHelper::STATUS_ACTIVE);
                            $orderDocument->deleteTempFile = $deleteTempFile;
                            $orderDocument->upload_file = $other_file;
                            if (!$orderDocument->save()) {
                                Yii::error('Other Document failed to save for order #' . $order->id  . PHP_EOL . print_r($orderDocument->errors, true) , 'CreatePatientForm-' . __FUNCTION__);
                                Yii::$app->session->addFlash('error', Yii::t('app', 'Other Document failed to save for service {service_name}.', [
                                    'service_name' => $service->service_name
                                ]));
                            }
                        }

                    } else {
                        Yii::$app->session->addFlash('error',
                            Yii::t('app', 'Service request for {service_name} service not created for this patient. Please create it later.', [
                                'service_name' => $service->service_name
                            ])
                        );
                    }
                }
            }
        }
    }
}