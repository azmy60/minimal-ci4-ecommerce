<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitialTables extends Migration
{    
    public function up()
    {
        // Products
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'title'         => ['type' => 'varchar', 'constraint' => 20],
            'desc'          => ['type' => 'text'],
            'price'         => ['type' => 'double'],
            'stock'         => ['type' => 'int', 'constraint' => 1],
            'cat_id'        => ['type' => 'int', 'constraint' => 10, null => true],
            'created_at'    => ['type' => 'datetime', 'null' => true],
            'updated_at'    => ['type' => 'datetime', 'null' => true],
            'deleted_at'    => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('title');
        $this->forge->createTable('products');

        // Categories
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'name'          => ['type' => 'varchar', 'constraint' => 20],
            'created_at'    => ['type' => 'datetime', 'null' => true],
            'updated_at'    => ['type' => 'datetime', 'null' => true],
            'deleted_at'    => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('categories');

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

        // Store
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'name'          => ['type' => 'varchar', 'constraint' => 20],
            'desc'          => ['type' => 'text', 'null' => true],
            'address'       => ['type' => 'text', 'null' => true],
            'phone'         => ['type' => 'varchar', 'constraint' => 15],
            'status'        => ['type' => 'int', 'constraint' => 1, 'default' => 0],
            'owner_name'    => ['type' => 'varchar', 'constraint' => 50],
            'owner_phone'   => ['type' => 'varchar', 'constraint' => 15, 'null' => true],
            'email'         => ['type' => 'varchar', 'constraint' => 255],
            'socials'       => ['type' => 'text'],
            'created_at'    => ['type' => 'datetime', 'null' => true],
            'updated_at'    => ['type' => 'datetime', 'null' => true],
            'deleted_at'    => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('store');
    }

    public function down()
    {
        $this->forge->dropTable('products');
        $this->forge->dropTable('categories');
        $this->forge->dropTable('store');
        $this->forge->dropTable('message_template');
    }
}
