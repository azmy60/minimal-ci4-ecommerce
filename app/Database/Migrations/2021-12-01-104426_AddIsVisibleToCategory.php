<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsVisibleToCategory extends Migration
{
    public function up()
    {
        $this->forge->addColumn('categories', [
            'is_visible' => [
                'type' => 'int',
                'constraint' => 1,
                'after' => 'name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('categories', 'is_visible');
    }
}
