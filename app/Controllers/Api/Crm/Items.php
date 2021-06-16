<?php
namespace App\Controllers\Api\Crm;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ItemModel;

class Items extends ResourceController
{
    use ResponseTrait;
    // get all item
    public function index()
    {
        $model = new ItemModel();
        $data = $model->findAll();
        return $this->respond($data, 200);
    }

    // get single item
    public function show($id = null)
    {
        $model = new ItemModel();
        $data = $model->getWhere(['id' => $id])->getResult();
        if($data){
            return $this->respond($data);
        }else{
            return $this->failNotFound('No Data Found with id '.$id);
        }
    }

    // create a item
    public function create()
    {
        $model = new ItemModel();
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description')
        ];
        $data = json_decode(file_get_contents("php://input"));

        $model->insert($data);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
            ]
        ];

        return $this->respondCreated($data, 201);
    }

    // update item
    public function update($id = null)
    {
        $model = new ItemModel();
        $json = $this->request->getJSON();
        if($json){
            $data = [
                'name' => $json->name,
                'description' => $json->description
            ];
        }else{
            $input = $this->request->getRawInput();
            $data = [
                'name' => $input['name'],
                'description' => $input['description']
            ];
        }
        // Insert to Database
        $model->update($id, $data);
        $response = [
            'status'   => 200,
            'error'    => null,
            'messages' => [
                'success' => 'Data Updated'
            ]
        ];
        return $this->respond($response);
    }

    // delete item
    public function delete($id = null)
    {
        $model = new ItemModel();
        $data = $model->find($id);
        if($data){
            $model->delete($id);
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Data Deleted'
                ]
            ];
            return $this->respondDeleted($response);
        }else{
            return $this->failNotFound('No Data Found with id '.$id);
        }
    }
}