<?php

namespace backend\controllers;

use Yii;
use Exception;
use yii\web\NotFoundHttpException;
use common\helpers\ConstHelper;
use common\components\PDFToolKit;
use common\models\NoteEval;
use common\models\NoteProgress;
use common\models\NoteRouteSheet;
use common\models\NoteSupplemental;
use common\models\NoteCommunication;
use common\models\NoteDischargeOrder;
use common\models\NoteDischargeSummary;
use common\exceptions\NoteNotFoundException;

/**
 * DocumentController implements the functionality to print Notes in PDF format.
 */
class DocumentController extends BackendController
{
    /**
     * Print Communication Note.
     * @param int $id Note ID
     * @throws NotFoundHttpException
     */
    public function actionNoteCommunication($id)
    {
        /** @TODO Add Access Check for this note for customer and provider */

        try {
            if (($model = NoteCommunication::findOne($id)) === null) {
                throw new NoteNotFoundException(Yii::t('app', 'The requested record does not exist.'));
            }

            $template = Yii::getAlias('@common/templates/communication_note.pdf');
            $filled = new PDFToolKit($template);
            $data = $model->toArray();
            $data['note_date'] = Yii::$app->formatter->asDate($model->note_date);
            $data['time_in'] = Yii::$app->formatter->asTime($model->time_in, 'short');
            $data['time_out'] = Yii::$app->formatter->asTime($model->time_out, 'short');

            if (!empty($model)) {
                $output_name = 'communication_note_' . Yii::$app->formatter->asTimestamp($model->submitted_at) . '.pdf';
                $filled->fillForm($data)->flatten();
                $pdf = new PDFToolKit($filled);

                $signature = tempnam('/tmp', 'communication_signature_');
                if ($model->getRPTProviderSignature($signature)) {
                    $pdf->stamp($signature);
                }

                $pdf->send($output_name, true);
                unlink($signature);
            }
        } catch (Exception $e) {
            Yii::error('Communication Note by ID: '.$id.' failed to print. ' . $e->getMessage(), $this->id . '::' . __FUNCTION__);
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    /**
     * Print Discharge Order.
     * @param int $id Note ID
     * @throws NotFoundHttpException
     */
    public function actionNoteDischargeOrder($id)
    {
        /** @TODO Add Access Check for this note for customer and provider */

        try {
            if (($model = NoteDischargeOrder::findOne($id)) === null) {
                throw new NoteNotFoundException(Yii::t('app', 'The requested record does not exist.'));
            }

            $template = Yii::getAlias('@common/templates/discharge_order.pdf');
            $filled = new PDFToolKit($template);
            $data = $model->toArray();
            $data['note_date'] = Yii::$app->formatter->asDate($model->note_date);
            $data['time_in'] = Yii::$app->formatter->asTime($model->time_in, 'short');
            $data['time_out'] = Yii::$app->formatter->asTime($model->time_out, 'short');

            if (!empty($model)) {
                $output_name = 'discharge_order_' . Yii::$app->formatter->asTimestamp($model->submitted_at) . '.pdf';
                $filled->fillForm($data)->flatten();
                $pdf = new PDFToolKit($filled);

                $signature = tempnam('/tmp', 'discharge_order_signature_');
                if ($model->getRPTProviderSignature($signature)) {
                    $pdf->stamp($signature);
                }

                $pdf->send($output_name, true);
                unlink($signature);
            }
        } catch (Exception $e) {
            Yii::error('Discharge Order by ID: '.$id.' failed to print. ' . $e->getMessage(), $this->id . '::' . __FUNCTION__);
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    /**
     * Print Discharge Summary.
     * @param int $id Note ID
     * @throws NotFoundHttpException
     */
    public function actionNoteDischargeSummary($id)
    {
        /** @TODO Add Access Check for this note for customer and provider */

        try {
            if (($model = NoteDischargeSummary::findOne($id)) === null) {
                throw new NoteNotFoundException(Yii::t('app', 'The requested record does not exist.'));
            }

            $template = Yii::getAlias('@common/templates/discharge_summary.pdf');
            $filled = new PDFToolKit($template);
            $data = $model->toArray();
            $data['note_date'] = Yii::$app->formatter->asDate($model->note_date);

            if (!empty($model)) {
                $output_name = 'discharge_summary_' . Yii::$app->formatter->asTimestamp($model->submitted_at) . '.pdf';
                $filled->fillForm($data)->flatten();
                $pdf = new PDFToolKit($filled);

                $signature = tempnam('/tmp', 'discharge_summary_signature_');
                if ($model->getRPTProviderSignature($signature)) {
                    $pdf->stamp($signature);
                }

                $pdf->send($output_name, true);
                unlink($signature);
            }
        } catch (Exception $e) {
            Yii::error('Discharge Summary by ID: '.$id.' failed to print. ' . $e->getMessage(), $this->id . '::' . __FUNCTION__);
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    /**
     * Print Eval Note.
     * @param int $id Note ID
     * @throws NotFoundHttpException
     */
    public function actionNoteEval($id)
    {
        /** @TODO Add Access Check for this note for customer and provider */

        try {
            if (($model = NoteEval::findOne($id)) === null) {
                throw new NoteNotFoundException(Yii::t('app', 'The requested record does not exist.'));
            }

            $template = Yii::getAlias('@common/templates/eval_note.pdf');
            $filled = new PDFToolKit($template);
            $data = $model->toArray();
            $data['note_date'] = Yii::$app->formatter->asDate($model->note_date);
            $data['time_in'] = Yii::$app->formatter->asTime($model->time_in, 'short');
            $data['time_out'] = Yii::$app->formatter->asTime($model->time_out, 'short');

            if (!empty($model)) {
                $output_name = 'eval_note_' . Yii::$app->formatter->asTimestamp($model->submitted_at) . '.pdf';
                $filled->fillForm($data)->flatten();
                $pdf = new PDFToolKit($filled);

                $signature = tempnam('/tmp', 'eval_signature_');
                if ($model->getRPTProviderSignature($signature)) {
                    $pdf->stamp($signature);
                }

                $pdf->send($output_name, true);
                unlink($signature);
            }
        } catch (Exception $e) {
            Yii::error('Evaluation Note by ID: '.$id.' failed to print. ' . $e->getMessage(), $this->id . '::' . __FUNCTION__);
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    /**
     * Print Progress Note.
     * @param int $id Note ID
     * @throws NotFoundHttpException
     */
    public function actionNoteProgress($id)
    {
        /** @TODO Add Access Check for this note for customer and provider */

        try {
            if (($model = NoteProgress::findOne($id)) === null) {
                throw new NoteNotFoundException(Yii::t('app', 'The requested record does not exist.'));
            }

            $template = Yii::getAlias('@common/templates/progress_note.pdf');
            $filled = new PDFToolKit($template);
            $data = $model->toArray();
            $data['note_date'] = Yii::$app->formatter->asDate($model->note_date);
            $data['time_in'] = Yii::$app->formatter->asTime($model->time_in, 'short');
            $data['time_out'] = Yii::$app->formatter->asTime($model->time_out, 'short');

            if (!empty($model)) {
                $output_name = 'progress_note_' . Yii::$app->formatter->asTimestamp($model->submitted_at) . '.pdf';
                $filled->fillForm($data)->flatten();
                $pdf = new PDFToolKit($filled);

                $signature = tempnam('/tmp', 'progress_signature_');
                if ($model->getRPTProviderSignature($signature)) {
                    $pdf->stamp($signature);
                }

                $pdf->send($output_name, true);
                unlink($signature);
            }
        } catch (Exception $e) {
            Yii::error('Progress Note by ID: '.$id.' failed to print. ' . $e->getMessage(), $this->id . '::' . __FUNCTION__);
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    /**
     * Print Route Sheet.
     * @param int $id Note ID
     * @throws NotFoundHttpException
     */
    public function actionNoteRouteSheet($id)
    {
        /** @TODO Add Access Check for this note for customer and provider */

        try {
            if (($model = NoteRouteSheet::findOne($id)) === null) {
                throw new NoteNotFoundException(Yii::t('app', 'The requested record does not exist.'));
            }

            $template = Yii::getAlias('@common/templates/route_sheet.pdf');
            $filled = new PDFToolKit($template);
            $data = $model->toArray();
            $data['note_date'] = Yii::$app->formatter->asDate($model->note_date);
            $data['time_in'] = Yii::$app->formatter->asTime($model->time_in, 'short');
            $data['time_out'] = Yii::$app->formatter->asTime($model->time_out, 'short');
            $data['visit_total_time'] = Yii::$app->formatter->asDuration(ConstHelper::calculateDuration($model->time_in, $model->time_out));

            if (!empty($model)) {
                $output_name = 'route_sheet_' . Yii::$app->formatter->asTimestamp($model->submitted_at) . '.pdf';
                $filled->fillForm($data)->flatten();
                $pdf = new PDFToolKit($filled);

                $signature = tempnam('/tmp', 'route_sheet_signature_');
                if ($model->getRPTProviderSignature($signature)) {
                    $pdf->stamp($signature);
                }

                $pdf->send($output_name, true);
                unlink($signature);
            }
        } catch (Exception $e) {
            Yii::error('Route Sheet by ID: '.$id.' failed to print. ' . $e->getMessage(), $this->id . '::' . __FUNCTION__);
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    /**
     * Print Physician Order.
     * @param int $id Note ID
     * @throws NotFoundHttpException
     */
    public function actionNoteSupplemental($id)
    {
        /** @TODO Add Access Check for this note for customer and provider */

        try {
            if (($model = NoteSupplemental::findOne($id)) === null) {
                throw new NoteNotFoundException(Yii::t('app', 'The requested record does not exist.'));
            }

            $template = Yii::getAlias('@common/templates/supplemental_order.pdf');
            $filled = new PDFToolKit($template);
            $data = $model->toArray();
            $data['note_date'] = Yii::$app->formatter->asDate($model->note_date);
            $data['time_in'] = Yii::$app->formatter->asTime($model->time_in, 'short');
            $data['time_out'] = Yii::$app->formatter->asTime($model->time_out, 'short');

            if (!empty($model)) {
                $output_name = 'physician_order_' . Yii::$app->formatter->asTimestamp($model->submitted_at) . '.pdf';
                $filled->fillForm($data)->flatten();
                $pdf = new PDFToolKit($filled);

                $signature = tempnam('/tmp', 'physician_order_signature_');
                if ($model->getRPTProviderSignature($signature)) {
                    $pdf->stamp($signature);
                }

                $pdf->send($output_name, true);
                unlink($signature);
            }
        } catch (Exception $e) {
            Yii::error('Physician Order by ID: '.$id.' failed to print. ' . $e->getMessage(), $this->id . '::' . __FUNCTION__);
            throw new NotFoundHttpException($e->getMessage());
        }
    }

}