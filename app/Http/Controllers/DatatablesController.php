<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Log;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\ShipmentLine;
use App\Models\Stock;
use App\Models\StockGroupType;
use App\Models\Task;
use App\Models\TaskLine;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DatatablesController extends Controller
{
    /**
     * @var int Default amount of records per page
     */
    var $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Return orders for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function orders(Request $request)
    {

        $direction = $request->input('direction', '');
        if ($direction == 'inbound') {
            $return = Order::with(['type', 'status', 'contact']);
            $return = $return->whereHas('type', function ($query) {
                $query->where('inbound', 1, 'and');
            });
        } elseif ($direction == 'outbound') {
            $return = Order::with(['type', 'status', 'contact']);
            $return = $return->whereHas('type', function ($query) {
                $query->where('outbound', 1, 'and');
            });
        } else {
            return $this->processRequestResponse(Order::with(['type', 'status', 'contact']), $request);
        }
        if (!$request->get('query', false)) {
            $return->where('order_status_id', '<>', '99');
        }
        return $this->processRequestResponse($return->withCount('orderlines'), $request);
    }

    /**
     * Process the pagination / filtering variables for datatables and returns the data
     * @param Builder $builder
     * @param Request $request
     * @return JsonResponse
     */
    public function processRequestResponse(Builder $builder, Request $request)
    {
        $model = $builder->getModel();
        /**
         * Filter
         */
        // Global
        $search = $request->get('search')['value'] ?? false;
        if (false !== $search) {
            $whereBuilder = [];
            if (is_array($model->searchableColumns)) {
                /**
                 * Fill the $whereBuilder, we use this later to create a nested WHERE statement
                 */
                $whereBuilder = $model->searchableColumns;
            }
            /**
             * Fill the $whereHasBuilder, we use this later to create a nested WHERE statement
             */
            $whereHasBuilder = [];
            foreach ($builder->getEagerLoads() as $relationName => $relationBuilder) {
                if (!Str::contains($relationName, '.')) {
                    $relationModel = $builder->getRelation($relationName)->getModel();
                    if (is_array($relationModel->searchableColumns)) {
                        $whereHasBuilder[$relationName] = $relationModel->searchableColumns;
                    }
                }
            }
            if (count($whereHasBuilder) > 0 || count($whereBuilder) > 0) {
                $builder->where(function ($query) use ($whereHasBuilder, $whereBuilder, $search) {
                    /**
                     * Wherehas builder (for relations)
                     */
                    foreach ($whereHasBuilder as $relationName => $relationColumns) {
                        $query->orwhereHas($relationName, function ($query) use ($relationColumns, $search) {
                            $i = 0;
                            foreach ($relationColumns as $relationColumn) {
                                if ($i == 0) {
                                    $query->where($relationColumn, 'LIKE', '%' . $search . '%');
                                } else {
                                    $query->orWhere($relationColumn, 'LIKE', '%' . $search . '%');
                                }
                                $i++;
                            }
                        });
                    }
                    /**
                     * Where builder (for own columns)
                     */
                    foreach ($whereBuilder as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $search . '%');
                    }
                });
            }
        }
        // Per column
        $requestColumns = $request->get('columns');
        if (null !== $requestColumns) {
            foreach ($requestColumns as $requestColumn) {
                $search = $requestColumn['search']['value'] ?? '';
                if (!empty($search)) {
                    // See if the column is searchable, then execute a where on that column
                    /**
                     * Check if this is a search on relations
                     */
                    $column = explode('.', $requestColumn['data']);
                    if (count($column) <= 1) {
                        // Column isn't a relation, check the searchablecolumn directly
                        if (is_array($model->searchableColumns)) {
                            if (in_array($column, $model->searchableColumns)) {
                                $builder->where(function ($query) use ($column, $search) {
                                    $query->where($column, 'LIKE', '%' . $search . '%');
                                });
                            }
                        }
                    } else {
                        // Column is looking for a relation column, see if it exists and search for the value
                        foreach ($builder->getEagerLoads() as $relationName => $relationBuilder) {
                            $relationModel = $builder->getRelation($relationName)->getModel();
                            if ($relationName === $column[0] && is_array($relationModel->searchableColumns) && in_array($column[1],
                                    $relationModel->searchableColumns, true)) {
                                $builder->where(function ($query) use ($relationName, $column, $search) {
                                    $query->whereHas($relationName, function ($query) use ($column, $search) {
                                        $query->where($column[1], 'LIKE', '%' . $search . '%');
                                    });
                                });
                            }
                        }
                    }
                }
            }
        }
        /**
         * Sorting
         */
        $sorting = $request->get('order')[0]['column'] ?? null;
        if (null !== $sorting) {
            // Check if sorting is on the main table or a relation
            $field = $request->get('columns')[$sorting]['data'];
            if (!empty($field)) {

                $direction = $request->get('order')[0]['dir'] ?? 'asc';
                $column = explode('.', $field);
                if (count($column) <= 1) {
                    $builder->orderBy($field, $direction);
                } else {
                    // Sorting is on a relation model / table, search for the correct table and sort on that
                    // TODO: make this possible for many to many relations (with reference tables)
                    foreach ($builder->getEagerLoads() as $relationName => $relationBuilder) {
                        if ($column[0] === $relationName) {
                            $tableName = $builder->getRelation($relationName)->getModel()->getTable();
                            $foreignKey = $builder->getRelation($relationName)->getModel()->getForeignKey();
                            $builder->leftJoin($tableName, $tableName . '.id', '=', $foreignKey);
                            $builder->orderBy($tableName . '.' . $column[1], $direction);
                        }
                    }

                }
            } else {
                // Default sorting
                $builder->orderByDesc('id');
            }
        } else {
            // Default sorting
            $builder->orderByDesc('id');
        }

        /**
         * Pagination
         */
        $meta = [];
        if ($request->get('length') !== '-1') {
            // Datatables wants pages
            $totalCount = $builder->count();
            $perPage = ((int)$request->get('length')) === 0 ? $this->defaultPerPage : (int)$request->get('length');
            $pageNum = ((int)$request->get('start') > 0) ? ((int)$request->get('start') / (int)$request->get('length')) + 1 : 1;
            $data = $builder->select(empty($builder->getQuery()->columns) ? $model->getTable() . '.*' : $builder->getQuery()->columns)->paginate($perPage,
                ['*'], 'page',
                $pageNum)->items();
            if ($request->get('requestIds', false)) {
                // Add rowIds for selecting all checkboxes
                $meta['rowIds'] = $builder->pluck('id')->toArray();
            }
            return response()->json([
                'recordsTotal' => $totalCount,
                'recordsFiltered' => $totalCount,
                'draw' => (int)$request->get('draw'),
                'data' => $data
            ]);
        } else {
            $data = $builder->select(empty($builder->getQuery()->columns) ? $model->getTable() . '.*' : $builder->getQuery()->columns)->get();
            if ($request->get('requestIds', false)) {
                // Add rowIds for selecting all checkboxes
                $meta['rowIds'] = $builder->pluck($model->getTable() . '.id')->toArray();
            }
            $count = $builder->count();
            return response()->json([
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'draw' => (int)$request->get('draw'),
                'data' => $data,
            ]);
        }

    }

    /**
     * Return orderlines for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function orderlines(Request $request)
    {
        $order_id = $request->post('order_id');
        return $this->processRequestResponse(Order::find($order_id)
            ->orderlines()
            ->with(['product', 'productuom', 'stockgroups'])->getQuery(), $request);
    }

    /**
     * Return shipment lines
     * @param Request $request
     * @return JsonResponse
     */
    public function shipmentlines(Request $request)
    {
        $order_id = $request->post('order_id');
        return $this->processRequestResponse(ShipmentLine::where(['order_id' => $order_id])
            ->with(['product', 'productuom', 'shipment', 'user']), $request);

    }

    /**
     * Return users for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $data = User::with(['roles', 'contact'])->get();
        return response()->json([
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'draw' => (int)$request->get('draw'),
            'data' => $data,
        ]);
    }

    /**
     * Return contacts for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function contacts(Request $request)
    {
        return $this->processRequestResponse(Contact::query(), $request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function tasks(Request $request): JsonResponse
    {
        $types = explode(',', $request->type);
        $data = Task::with('status', 'user')
            ->whereHas('type', function ($query) use ($types) {
                $query->whereIn('id', $types);
            })
            ->get();
        return response()->json([
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'draw' => (int)$request->get('draw'),
            'data' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function tasklines(Request $request): JsonResponse
    {
        $type = $request->get('type', false);
        $task_id = $request->get('task_id', false);
        $tasklines = TaskLine::with('stock.location', 'stock.product', 'stock.productuom', 'destination', 'order')
            ->whereHas('task', function ($query) use ($type, $task_id) {
                if ($type) {
                    $query->where('task_type_id', $type);
                }
                if ($task_id) {
                    $query->where('id', $task_id);
                }
            })
            ->get();
        return response()->json([
            'recordsTotal' => $tasklines->count(),
            'recordsFiltered' => $tasklines->count(),
            'draw' => (int)$request->get('draw'),
            'data' => $tasklines,
        ]);
    }

    /**
     * Return stock for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function stock(Request $request)
    {
        if ($request->user()->hasRole('owner')) {
            // User is product owner, only show their product
            $query = Stock::whereHas('product', static function ($query) use ($request) {
                $query->where('owner_contact_id', $request->user()->contact_id);
            })
                ->with('location', 'product', 'productuom', 'stockgroups.type');
            return $this->processRequestResponse($query, $request);
        } else {
            return $this->processRequestResponse(Stock::with('location', 'product', 'productuom', 'order',
                'stockgroups.type'), $request);
        }
    }

    /**
     * Return products for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function Products(Request $request)
    {
        return $this->processRequestResponse(Product::query(), $request);
    }

    /**
     * Return locations for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function Locations(Request $request)
    {
        return $this->processRequestResponse(Location::with('type'), $request);
    }

    /**
     * Return logs for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logs(Request $request)
    {
        return $this->processRequestResponse(Log::with('user', 'order', 'orderline', 'orderstatus', 'task', 'taskline',
            'location', 'product'), $request);
    }

    /**
     * Return product UOMs
     * @param Request $request
     * @return JsonResponse
     */
    public function productuoms(Request $request)
    {
        return $this->processRequestResponse(ProductUom::where(['product_id' => $request->get('product_id')]),
            $request);
    }

    /**
     * Return invoicelines for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function invoicelines(Request $request): JsonResponse
    {
        $invoice_id = $request->post('invoice_id');
        return $this->processRequestResponse(Invoice::find($invoice_id)
            ->invoicelines()->getQuery(), $request);
    }

    /**
     * Return invoices for datatables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function invoices(Request $request): JsonResponse
    {
        return $this->processRequestResponse(Invoice::with(['type', 'status', 'contact']), $request);
    }

    /**
     * Return all fixed locations for a product
     * @param Request $request
     * @return JsonResponse
     */
    public function fixedlocations(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        $product = Product::findOrFail($request->get('product_id'));

        return response()->json([
            'recordsTotal' => $product->fixedlocations()->count(),
            'recordsFiltered' => $product->fixedlocations()->count(),
            'draw' => (int)$request->get('draw'),
            'data' => $product->fixedlocations(),
        ]);
    }

    /**
     * Returns stockGroups
     * @param Request $request
     * @return JsonResponse
     */
    public function stockgrouptypes(Request $request)
    {
        return $this->processRequestResponse(StockGroupType::with('finallocationtype'), $request);
    }
}
