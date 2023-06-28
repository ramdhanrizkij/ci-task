<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\TaskEntity;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $returnType = TaskEntity::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['judul', 'status'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'judul'       => 'required|min_length[10]|max_length[60]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    const ORDERABLE = [
        1 => 'judul',
        2 => 'status',
    ];

    public $orderable = ['judul', 'status',];

    /**
     * Get resource data.
     *
     * @param string $search
     *
     * @return \CodeIgniter\Database\BaseBuilder
     */
    public function getResource(string $search = '')
    {
        $builder = $this->builder()
            ->select('tasks.id, tasks.judul, tasks.status');

        $condition = empty($search)
            ? $builder
            : $builder->groupStart()
                ->like('judul', $search)
            ->groupEnd();

        return $condition;
    }

}

