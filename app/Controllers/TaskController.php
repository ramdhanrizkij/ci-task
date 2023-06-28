<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskModel;
use CodeIgniter\API\ResponseTrait;

class TaskController extends BaseController
{
    use ResponseTrait;
    protected $model;

    public function __construct()
    {
        $this->model = new TaskModel();
    }

    public function index()
    {
        return view('TaskView');
    }

    public function datatable()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getPost('start');
            $length = $this->request->getPost('length');
            $search = $this->request->getPost('search[value]');
            $order = TaskModel::ORDERABLE[$this->request->getPost('order[0][column]')];
            $dir = $this->request->getPost('order[0][dir]');

            return $this->respond([
                'draw'            => $this->request->getPost('draw'),
                'recordsTotal'    => $this->model->getResource()->countAllResults(),
                'recordsFiltered' => $this->model->getResource($search)->countAllResults(),
                'data'            => $this->model->getResource($search)->orderBy($order, $dir)->limit($length, $start)->get()->getResultObject(),
            ]);
        }

        return $this->respondNoContent();
    }

    function create() 
    {
        if ($this->model->insert($this->request->getPost())) {
            return $this->respond([
                'status'=>200,
                'message'=>"Task berhasil ditambahkan"
            ]);
        }

        return $this->fail($this->model->errors());
    }

    function updateStatus($id) 
    {
        if ($data = $this->model->find($id)) {
            $data->status = $data->status==0?1:0;
            $this->model->update($id, $data);
            return $this->respond([
                'status'=>200,
                'message'=>"Berhasil melakukan update status",
                'data' => $data]);
        }

        return $this->respond([
            'status'=>404,
            'message'=>'Data status tidak ditemukan'
        ]);
    }

    function update($id) 
    {
        if ($data = $this->model->find($id)) {
            $data->judul = $this->request->getRawInput();
            $this->model->update($id, $this->request->getRawInput());
            return $this->respond([
                'status'=>200,
                'message'=>"Berhasil melakukan update data",
                'data' => $data,
            ]);
        }

        return $this->respond([
            'status'=>404,
            'message'=>'Data task tidak ditemukan'
        ]);
    }

    function delete($id)
    {
        $this->model->delete($id);
        return $this->respond([
            'status'=>200,
            'message'=>"Data berhasil dihapus",
            'result'=>[]
        ]);
    }

    function getById($id)
    {
        $data = $this->model->find($id);    
        return $this->respond([
            'status'=>200,
            'data'=>$data
        ]);
    }
}
