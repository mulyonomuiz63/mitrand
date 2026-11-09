<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropAiAssistant extends Migration
{
    public function up(): void
    {
        $this->forge->dropTable('ai_requests', true);

        if ($this->db->tableExists('app_settings')) {
            $this->db->table('app_settings')->whereIn('setting_key', [
                'ai_enabled',
                'ai_api_base',
                'ai_api_key',
                'ai_model',
            ])->delete();
        }
    }

    public function down(): void
    {
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
        $this->forge->createTable('ai_requests', true);
    }
}
