<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNdtaxTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'         => ['type' => 'INT', 'unsigned' => true],
            'member_code'     => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'full_name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 30],
            'address'         => ['type' => 'TEXT'],
            'photo'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'approved_by'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_at'     => ['type' => 'DATETIME', 'null' => true],
            'rejected_reason' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('member_code');
        $this->forge->addKey('status');
        $this->forge->createTable('mitra_profiles');

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'mitra_id'    => ['type' => 'INT', 'unsigned' => true],
            'card_number' => ['type' => 'VARCHAR', 'constraint' => 32],
            'qr_token'    => ['type' => 'VARCHAR', 'constraint' => 64],
            'issued_at'   => ['type' => 'DATETIME'],
            'valid_until' => ['type' => 'DATE'],
            'file_path'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('mitra_id');
        $this->forge->addUniqueKey('qr_token');
        $this->forge->addUniqueKey('card_number');
        $this->forge->createTable('membership_cards');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 180],
            'description'   => ['type' => 'TEXT', 'null' => true],
            'category'      => ['type' => 'VARCHAR', 'constraint' => 80],
            'file_path'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'mime'          => ['type' => 'VARCHAR', 'constraint' => 120],
            'file_size'     => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'is_published'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_by'    => ['type' => 'INT', 'unsigned' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('is_published');
        $this->forge->createTable('marketing_assets');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'mitra_id'   => ['type' => 'INT', 'unsigned' => true],
            'jenis'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'nominal'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'periode'    => ['type' => 'VARCHAR', 'constraint' => 7],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'created_by' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['mitra_id', 'periode']);
        $this->forge->createTable('komisi_bonus');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'jenis'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'prompt'     => ['type' => 'TEXT'],
            'hasil'      => ['type' => 'TEXT'],
            'sumber'     => ['type' => 'VARCHAR', 'constraint' => 20],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('ai_requests');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 254],
            'token_hash' => ['type' => 'VARCHAR', 'constraint' => 64],
            'expires_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token_hash');
        $this->forge->addKey('email');
        $this->forge->createTable('password_resets');
    }

    public function down(): void
    {
        $this->forge->dropTable('password_resets', true);
        $this->forge->dropTable('ai_requests', true);
        $this->forge->dropTable('komisi_bonus', true);
        $this->forge->dropTable('marketing_assets', true);
        $this->forge->dropTable('membership_cards', true);
        $this->forge->dropTable('mitra_profiles', true);
    }
}
