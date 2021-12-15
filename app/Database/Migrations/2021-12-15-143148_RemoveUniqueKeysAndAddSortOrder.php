<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUniqueKeysAndAddSortOrder extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE products DROP INDEX `title`');
        $this->db->query('ALTER TABLE categories DROP INDEX `name`');

        $this->forge->addColumn('product_photos', [
            'sort_order' => ['type' => 'int', 'null' => true, 'after' => 'product_id'],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('products', [
            'title' => [ 'type' => 'varchar', 'constraint' => 20, 'unique' => true],
        ]);

        $this->forge->modifyColumn('categories', [
            'name' => [ 'type' => 'varchar', 'constraint' => 20, 'unique' => true],
        ]);

        $this->forge->dropColumn('product_photos', 'sort_order');
    }
}
