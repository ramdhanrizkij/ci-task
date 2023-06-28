<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'auto_increment'=>true,
                'unsigned'=>true
            ],
            'judul'=>[
                'type'=>'VARCHAR',
                'constraint'=>'255'
            ],
            'status'=>[
                'type'=>'INT',
                'default'=>0,
                'null'=>true,
            ]
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('tasks');
    }

    public function down()
    {
        $this->forge->dropTable('tasks');
    }
}
