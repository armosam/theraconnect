<?php

use yii\db\Migration;
use common\helpers\ConstHelper;
use common\models\User;

/**
 * Class m141022_115823_create_user_table
 */
class m141022_115823_create_user_table extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') 
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->unique()->notNull(),
            'email' => $this->string()->unique()->defaultExpression('NULL'),
            'password_hash' => $this->string()->notNull(),
            'status' => $this->string(1)->notNull()->defaultValue(User::USER_STATUS_NOT_ACTIVATED),
            'auth_key' => $this->string(32)->unique()->notNull(),
            'access_token' => $this->string(255)->unique()->defaultExpression('NULL'),
            'password_reset_token' => $this->string(255)->unique()->defaultExpression('NULL'),
            'account_activation_token' => $this->string(255)->unique()->defaultExpression('NULL'),
            'phone_number_validation_code' => $this->integer(8)->unique()->defaultExpression('NULL'),
            'title' => $this->string(15), // For providers only
            'agency_name' => $this->string(255), // For customer only
            'rep_position' => $this->string(255), // For customer only
            'first_name' => $this->string(255),
            'last_name' => $this->string(255),
            'gender' => $this->string(1),
            'phone1' => $this->string(15),
            'phone2' => $this->string(15),
            'lat' => $this->decimal(9, 6),
            'lng' => $this->decimal(9, 6),
            'address' => $this->string(255),
            'city' => $this->string(255),
            'state' => $this->string(255),
            'zip_code' => $this->string(15),
            'country' => $this->string(255),
            'language' => $this->json(),
            'covered_county' => $this->json(),
            'covered_city' => $this->json(),
            'service_rate' => $this->integer(),
            'website_address' => $this->string(255),
            'facebook_id' => $this->string(255)->unique()->defaultExpression('NULL'),
            'google_id' => $this->string(255)->unique()->defaultExpression('NULL'),
            'emergency_contact_name' => $this->string(255),
            'emergency_contact_number' => $this->string(15),
            'emergency_contact_relationship' => $this->string(255),
            'timezone' => $this->string(255)->defaultValue(Yii::$app->timeZone),
            'ip_address' => $this->string(15),
            'note' => $this->string(255),
            'note_email_news_and_promotions' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_YES),
            'note_email_account_updated' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_YES),
            'note_email_order_submitted' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_YES),
            'note_email_order_accepted' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_YES),
            'note_email_order_rejected' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_YES),
            'note_email_order_canceled' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_YES),
            'note_email_order_reminder' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_YES),
            'note_email_rate_service' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_YES),
            'note_sms_news_and_promotions' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
            'note_sms_account_updated' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
            'note_sms_order_submitted' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
            'note_sms_order_accepted' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
            'note_sms_order_rejected' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
            'note_sms_order_canceled' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
            'note_sms_order_reminder' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
            'note_sms_rate_service' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
            'suspended_by' => $this->integer(),
            'suspended_at' => 'TIMESTAMPTZ(0)',
            'suspension_reason' => $this->string(255),
            'terminated_by' => $this->integer(),
            'terminated_at' => 'TIMESTAMPTZ(0)',
            'termination_reason' => $this->string(255),
            'created_by' => $this->integer()->notNull()->defaultExpression("currval('user_id_seq')"),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer()->defaultExpression("currval('user_id_seq')"),
            'updated_at' => 'TIMESTAMPTZ(0)',
        ], $tableOptions);

        $this->createIndex(
            'idx-user-username',
            '{{%user}}',
            'username'
        );

        $this->createIndex(
            'idx-user-email',
            '{{%user}}',
            'email'
        );

        $this->createIndex(
            'idx-user-status',
            '{{%user}}',
            'status'
        );

        $this->createIndex(
            'idx-user-access_token',
            '{{%user}}',
            'access_token'
        );

        $this->createIndex(
            'idx-user-password_reset_token',
            '{{%user}}',
            'password_reset_token'
        );

        $this->createIndex(
            'idx-user-account_activation_token',
            '{{%user}}',
            'account_activation_token'
        );

        $this->createIndex(
            'idx-user-phone_number_validation_code',
            '{{%user}}',
            'phone_number_validation_code'
        );

        $this->createIndex(
            'idx-user-facebook_id',
            '{{%user}}',
            'facebook_id'
        );

        $this->createIndex(
            'idx-user-google_id',
            '{{%user}}',
            'google_id'
        );

        $this->createIndex(
            'idx-user-lat',
            '{{%user}}',
            'lat'
        );

        $this->createIndex(
            'idx-user-lng',
            '{{%user}}',
            'lng'
        );
    }

    public function down()
    {
        $this->dropIndex(
            'idx-user-lat',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-lng',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-google_id',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-facebook_id',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-phone_number_validation_code',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-account_activation_token',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-password_reset_token',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-access_token',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-status',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-email',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-username',
            '{{%user}}'
        );

        $this->dropTable('{{%user}}');
    }
}