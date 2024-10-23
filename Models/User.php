<?php

use HideMe\Model;

/**
 * Exemple de model
 */
class User extends Model
{
    protected $table = 'users';
    public $columns = [
        // 'id' => 'uuid',
        'first_name' => 'firstName',
        'last_name' => 'lastName',
        'mobile_number' => 'mobileNumber',
        'phone_number' => 'landlineNumber',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->columns['password'] = function () {
            return password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]);
        };
        $this->columns['username'] = function () {
            return $this->faker->unique()->userName();
        };
        $this->columns['email'] = function () {
            return $this->faker->unique()->safeEmail();
        };
    }
}
