<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategories extends Migration
{
    public function up()
    {
        // Product categories
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'product_id'    => ['type' => 'int', 'constraint' => 10, 'unsigned' => true],
            'cat_id'        => ['type' => 'int', 'constraint' => 10, 'unsigned' => true],
            'created_at'    => ['type' => 'datetime', 'null' => true],
            'updated_at'    => ['type' => 'datetime', 'null' => true],
            'deleted_at'    => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('product_id', 'products', 'id');
        $this->forge->addForeignKey('cat_id', 'categories', 'id');
        $this->forge->createTable('product_categories');

        // Drop products.cat_id
        $this->forge->dropColumn('products', 'cat_id');
    }

    public function down()
    {
        $this->forge->dropTable('product_categories');
        $this->forge->addColumn('products', [
            'cat_id'        => ['type' => 'int', 'constraint' => 10, 'null' => true, 'after' => 'stock'],
        ]);
    }
}
