<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropMessageTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('store', [
            'message' => [
                'type' => 'text',
            ],
        ]);

        $this->forge->modifyColumn('store', [
            'socials' => [
                'name' => 'instagram',
                'type' => 'text',
            ],
        ]);

        $this->forge->dropTable('message_template');
    }

    public function down()
    {
        // Message template
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'name'          => ['type' => 'varchar', 'constraint' => 20],
            'desc'          => ['type' => 'text'],
            'created_at'    => ['type' => 'datetime', 'null' => true],
            'updated_at'    => ['type' => 'datetime', 'null' => true],
            'deleted_at'    => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('message_template');
    }
}
