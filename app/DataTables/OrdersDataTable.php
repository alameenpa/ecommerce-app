<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class OrdersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('order', function ($row) {
                return 'ORD-' . $row->id;
            })
            ->addColumn('status', function ($row) {
                return ($row->status == 0) ? 'Active' : 'Closed';
            })
            ->addColumn('action', function ($row) {
                $btn = '';
                if ($row->status == 0) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" onclick="editFunc(' . $row->id . ')" data-original-title="Edit" class="edit btn btn-success edit">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0);" id="cancel-order" onclick="cancelOrder(' . $row->id . ')" data-toggle="tooltip" data-original-title="Cancel" class="cancel btn btn-danger">Cancel</a>';
                }
                $btn = $btn . ' <a href="javascript:void(0);" id="view-order" onclick="viewOrder(' . $row->id . ')" data-toggle="tooltip" data-original-title="View Details" class="view btn btn-primary">View Details</a>';
                return $btn;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('list-orders')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => ['excel', 'csv', 'print'],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('order'),
            Column::computed('status'),
            Column::make('amount')->addClass('text-center'),
            Column::make('address'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Orders_' . date('YmdHis');
    }
}
