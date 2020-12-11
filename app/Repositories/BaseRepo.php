<?php   

namespace App\Repositories;   

use Illuminate\Database\Eloquent\Model;   

class BaseRepo
{      
    protected $model;       
   
    public function __construct(Model $model)     
    {         
        $this->model = $model;
    }
 
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function find(int $id): ?Model
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $attributes): bool
    {
       return $this->find($id)->update($attributes);
    }

    public function delete(int $id): bool
    {
        return $this->model->find($id)->delete();
    }

    public function list(): iterable
    {
        return $this->model->get();
    }
}