<?php

namespace Braceyourself\EloquentVueTable\Http\Controllers;

use Braceyourself\EloquentVueTable\Http\Requests\EloquentTableDataRequest;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TCG\Voyager\Models\Role;

class ModelDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(EloquentTableDataRequest $request, $namespace, $model)
    {
        $query = $request->query();
        $namespace = Str::studly($namespace);
        $model = Str::studly($model);

        /** @var Model $class */
        $class = $this->getModelClass("$namespace\\$model");


        if ($scope = Arr::pull($query, 'scope')) {
            $scope = explode('scope', $request->scope)[1];

            $builder = $class::$scope();
        } else $builder = $class::query();


        foreach ($query as $key => $value) {
            if (Schema::connection((new $class)->getConnectionName())->hasColumn((new $class)->getTable(), Str::upper($key))) {
                $value = [$value];
                if (Str::contains($value[0], ':')) {
                    $value = explode(':', $value[0]);
                }

                if ($value[0] === 'like') {
                    $value[1] = '%' . $value[1] . '%';
                }
//                dd($key, ...$value);

                $builder = $builder->where($key, ...$value);
            }
        }

        if ($sort = Arr::pull($query, 'sort_by')) {

        }

        return $builder->paginate();
    }

    public function store()
    {

    }

    public function show(Request $request, $model)
    {
        return $model;
    }

    public function update(\Illuminate\Http\Request $request, $namespace, $model, $id)
    {
        $namespace = Str::studly($namespace);
        $model = $namespace."\\".Str::studly(Str::singular($model));
        $instance = $model::find($id);

        $instance->fill($request->all());
	$instance->save();

        return response([
            'message' => 'Updated',
            'status' => 'success',
            'model' => $instance->getAttributes()
        ]);

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

    public function meta(Request $request, $model)
    {
        $class = $this->getModelClass($model);



        /** @var Model $instance */
        $instance = new $class();
        $reflect = new \ReflectionClass($class);
        $methods = collect($reflect->getMethods());





        $data = [
            'table' => $instance->getTable(),
            'resource_slug' => $this->getSlug($model),
//            'create' => false,
//            'bulk_delete' => false,
            'total_count' => $class::count(),
            'casts' => $instance->getCasts(),
            'actions' => $instance->actions
        ];

        $data['columns'] = Schema::connection($instance->getConnectionName())
            ->getColumnListing($data['table']);
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
        if (!Str::startsWith($model, 'App\\')) {
            $model = "App\\$model";
        }


        if (class_exists($model)) {
            return $model;
        }


        return Str::singular(Str::studly($model));
    }

    private function getInstance($model, $id)
    {
        $class = $this->getModelClass($model);
        return $class::find($id);
    }

    private function getSlug($model)
    {
        if (!Str::startsWith(Str::lower($model), 'app')) {
            $model = "App\\$model";
        }

        $parts = explode('\\', $model);


        foreach ($parts as $k => $part) {
            $parts[$k] = Str::kebab($part);
        }


        $parts[array_key_last($parts)] = Str::plural(Arr::last($parts));


        // don't use 'datum'
        if (Str::endsWith(Arr::last($parts), 'datas')) {
            $parts[array_key_last($parts)] = str_replace('datas', 'data', $part);
        }


        return implode('/', $parts);
    }

}
