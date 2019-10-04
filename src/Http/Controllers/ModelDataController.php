<?php

namespace Braceyourself\EloquentVueTable\Http\Controllers;

use Braceyourself\EloquentVueTable\Http\Requests\EloquentTableDataRequest;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(EloquentTableDataRequest $request, $model)
    {
        /** @var Model $class */
        $class = $this->getModelClass($model);

        if ($request->query('sort_by')) {

        }


        return $class::paginate($request->count);
    }

    public function store()
    {

    }

    public function show()
    {

    }

    public function update()
    {

    }

    public function destroy(Request $request, $model, $id)
    {
        /** @var Model $instance */
        $instance = $this->getInstance($model, $id);

        try {
            $instance->delete();
        } catch (\Exception $e) {
            $message = "Couldn't delete row";
        }

        return response([
            'message' => $message ?? "$model deleted.",
            'instance' => $instance,
        ]);

    }

    public function meta($model)
    {
        $class = $this->getModelClass($model);


        /** @var Model $instance */
        $instance = new $class();
        $reflect = new \ReflectionClass($class);
        $methods = collect($reflect->getMethods())->filter(function ($method) use ($class) {
            return $method->class == $class;
        });

        $data = [
            'table' => $instance->getTable(),
            'resource_slug' => $this->getSlug($model),
            'create' => false,
            'bulk_delete' => true,
            'total_count' => $class::count(),
            'casts' => $instance->getCasts(),
            'actions' => $instance->actions
        ];

        $data['columns'] = Schema::getColumnListing($data['table']);
        $data['fillable'] = $instance->getFillable();

        $data['scopes'] = $methods->filter(function ($method) {
            return Str::startsWith($method->name, 'scope');
        });

        $appends = $instance->appends;
        $data['columns'] = array_diff($data['columns'], $instance->getHidden());
        $data['columns'] = array_merge($data['columns'], $appends ?? []);


        return $data;
    }

    public function doAction(Request $request, $model, $id, $action)
    {

        try {
            $instance = $this->getInstance($model, $id);
            $output = $instance->$action();
            $instance->refresh();

            $message = 'Done';
            $status = 'success';

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $status = 'error';
        }


        return response([
            'message' => $message,
            'data' => $instance instanceof Arrayable ? $instance : null,
            'output' => $output,
            'status' => $status
        ]);

    }

    private function getModelClass($model)
    {
        if (class_exists("App\\$model")) {
            return "App\\$model";
        }


        return "App\\" . Str::singular(Str::studly($model));
    }

    private function getInstance($model, $id)
    {
        $class = $this->getModelClass($model);
        return $class::find($id);
    }

    private function getSlug($model)
    {
        $slug = Str::kebab(Str::plural($model));

        if (Str::endsWith($slug, 'datas')) {
            $slug = str_replace('datas', 'data', $slug);
        }

        return $slug;
    }

}
