<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table         = 'password_resets';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['email', 'token_hash', 'expires_at', 'created_at'];
    protected $useTimestamps = false;
}
