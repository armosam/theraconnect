<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use yii\db\Expression;
use common\models\User;

/**
 * This is the ActiveQuery class for [[\common\models\User]].
 *
 * @see \common\models\User
 */
class UserQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * Get Active or nonActive users
     * If $state is true it will return all active users if not returns all non active users
     *
     * @param bool $state
     * @return UserQuery
     */
    public function active($state = true):self
    {
        if($state===true){
            return $this->andWhere(['[[status]]' => User::USER_STATUS_ACTIVE]);
        }
        return $this->andWhere('[[status]] != :stat', [':stat' => User::USER_STATUS_ACTIVE]);
    }

    /**
     * Get NotActivated or non NotActivated users
     * If $state is true it will return all notActivated users if not returns all non notActivated users
     *
     * @param bool $state
     * @return UserQuery
     */
    public function notActivated($state = true):self
    {
        if($state===true){
            return $this->andWhere(['[[status]]' => User::USER_STATUS_NOT_ACTIVATED]);
        }
        return $this->andWhere('[[status]] != :stat', [':stat' => User::USER_STATUS_NOT_ACTIVATED]);
    }

    /**
     * Get Inactive or non Inactive users
     * If $state is true it will return all inactive users if not returns all non inactive users
     *
     * @param bool $state
     * @return UserQuery
     */
    public function inactive($state = true):self
    {
        if($state===true){
            return $this->andWhere(['[[status]]' => User::USER_STATUS_INACTIVE]);
        }
        return $this->andWhere('[[status]] != :stat', [':stat' => User::USER_STATUS_INACTIVE]);
    }

    /**
     * Get suspended or non suspended users
     * If $state is true it will return all suspended users if not returns all non suspended users
     *
     * @param bool $state
     * @return UserQuery
     */
    public function suspended($state = true):self
    {
        if($state===true){
            return $this->andWhere(['[[status]]' => User::USER_STATUS_SUSPENDED]);
        }
        return $this->andWhere('[[status]] != :stat', [':stat' => User::USER_STATUS_SUSPENDED]);
    }

    /**
     * Get terminated or non terminated users
     * If $state is true it will return all terminated users if not returns all non terminated users
     *
     * @param bool $state
     * @return UserQuery
     */
    public function terminated($state = true):self
    {
        if($state===true){
            return $this->andWhere(['[[status]]' => User::USER_STATUS_TERMINATED]);
        }
        return $this->andWhere('[[status]] != :stat', [':stat' => User::USER_STATUS_TERMINATED]);
    }

    /**
     * Get admin: active when given arg true, inactive when given arg false or all when not arg given
     *
     * @param null|bool $state Status of provider
     * @return UserQuery Query for admin
     */
    public function admin($state = null):self
    {
        $this->joinWith('role')->andWhere(['[[auth_assignment.item_name]]' => [User::USER_ADMIN, User::USER_SUPER_ADMIN]]);

        if($state===true){
            $this->andWhere(['[[user.status]]' => User::USER_STATUS_ACTIVE]);
        } elseif($state===false) {
            $this->andWhere('[[user.status]] != :stat', [':stat' => User::USER_STATUS_ACTIVE]);
        }
        return $this;
    }

    /**
     * Get providers: active when given arg true, inactive when given arg false or all when not arg given
     * Second argument is about provider type and if missing then means all providers
     *
     * @param null|bool $state Status of provider
     * @param null|string $type Provider Type [RPT, PTA]
     * @return UserQuery Query for provider
     */
    public function provider($state = null, $type = null):self
    {
        $this->joinWith('role')->andWhere(['[[auth_assignment.item_name]]' => User::USER_PROVIDER]);

        if($state===true){
            $this->andWhere(['[[user.status]]' => User::USER_STATUS_ACTIVE]);
        } elseif($state===false) {
            $this->andWhere('[[user.status]] != :stat', [':stat' => User::USER_STATUS_ACTIVE]);
        }
        if(!empty($type)) {
            $this->andWhere(['[[user.title]]' => $type]);
        }
        return $this;
    }

    /**
     * Get customers: active when given arg true, inactive when given arg false or all when not arg given
     *
     * @param bool|null $state Status of customers
     * @return UserQuery Query for customer
     */
    public function customer($state = null):self
    {
        $this->joinWith('role')->andWhere(['[[auth_assignment.item_name]]' => User::USER_CUSTOMER]);

        if($state===true){
            $this->andWhere(['[[user.status]]' => User::USER_STATUS_ACTIVE]);
        } elseif($state===false) {
            $this->andWhere('[[user.status]] != :stat', [':stat' => User::USER_STATUS_ACTIVE]);
        }
        return $this;
    }

    /**
     * Get editor: active when given arg true, inactive when given arg false or all when not arg given
     *
     * @param bool|null $state Status of editor
     * @return UserQuery Query for editors
     */
    public function editor($state = null):self
    {
        $this->joinWith('role')->andWhere(['[[auth_assignment.item_name]]' => User::USER_EDITOR]);

        if($state===true){
            $this->andWhere(['[[user.status]]' => User::USER_STATUS_ACTIVE]);
        } elseif($state===false) {
            $this->andWhere('[[user.status]] != :stat', [':stat' => User::USER_STATUS_ACTIVE]);
        }
        return $this;
    }

    /**
     * Returns users having avatar photo or without avatar depending from given argument
     * If argument is true it returns users with avatar, otherwise users without avatar
     * @param bool $with_avatar
     * @return $this
     */
    public function hasAvatar($with_avatar = true):self
    {
        $this->joinWith('avatar');

        if($with_avatar){
            $this->andWhere(['IS', "(user_avatar.file_content = '')", new Expression('FALSE')]);
        } else {
            $this->andWhere(['IS NOT', "(user_avatar.file_content = '')", new Expression('FALSE')]);
        }
        return $this;
    }

    /**
     * Returns users having or missing service assigned
     * If argument is true it returns users with service, otherwise users without service
     * @param bool $with_service
     * @return $this
     */
    public function hasService($with_service = true):self
    {
        $this->joinWith('userService');

        if($with_service){
            $this->andWhere(['IS NOT', 'user_service.user_id', new Expression('NULL')]);
        } else {
            $this->andWhere(['IS', 'user_service.user_id', new Expression('NULL')]);
        }
        return $this;
    }

    /**
     * Returns users (providers) having given service
     * @param int $service_id Service ID
     * @return $this
     */
    public function withService($service_id)
    {
        return $this
            ->joinWith('userService')
            ->andWhere(['=', 'user_service.service_id', $service_id]);
    }

    /**
     * Returns customer users having or not having patients assigned
     * If argument is true it returns customer users with patients, otherwise customer users without patients
     * @param bool $with_patient
     * @return $this
     */
    public function hasPatient($with_patient = true):self
    {
        $this->joinWith('customerPatients');
        if($with_patient){
            $this->andWhere(['IS NOT', '[[patient.id]]', new Expression('NULL')]);
        } else {
            $this->andWhere(['IS', '[[patient.id]]', new Expression('NULL')]);
        }
        $this->groupBy('[[user.id]]');
        return $this;
    }
}
