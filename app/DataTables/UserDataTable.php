<?php

namespace App\DataTables;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable {
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $request = request();

        return (new EloquentDataTable($query))
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ['%' . $keyword . '%']);
            })
            ->editColumn('created_at', function (User $user) {
                return date('d.m.Y H:i', strtotime($user->created_at));
            })
            ->editColumn('updated_at', function (User $user) {
                return date('d.m.Y H:i', strtotime($user->updated_at));
            })
            ->editColumn('actions', function (User $user) {
                return view('user.partials.actions', ['user' => $user]);
            })
            ->searchPane(
                'role',
                fn() => [
                    [
                        'value' => RoleEnum::USER->value,
                        'label' => __('User')
                    ],
                    [
                        'value' => RoleEnum::ADMIN->value,
                        'label' => __('Admin')
                    ],
                ],
                function (QueryBuilder $query, array $values) {
                    return $query->whereIn('role', $values);
                }
            )
            ->searchPane(
                'email',
                fn() => User::query()->select('email as value', 'email as label')->get(),
                function (QueryBuilder $query, array $values) {
                    return $query->whereIn('email', $values);
                }
            )
            ->searchPane(
                'phone',
                fn() => User::query()->select('phone as value', 'phone as label')->get(),
                function (QueryBuilder $query, array $values) {
                    return $query->whereIn('phone', $values);
                }
            )
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->selectRaw('users.*, CONCAT(first_name, " ", last_name) AS full_name');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->addColumnDef([
                'targets' => '_all',
                'searchPanes' => [
                    'show' => TRUE,
                    'viewTotal' => FALSE,
                    'viewCount' => FALSE,
                ],
            ])
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->addTableClass('table table-sm table-striped table-borderless table-hover small')
            ->pageLength(100)
            ->buttons([
                Button::make('searchPanes'),
                Button::make('excel'),
                Button::make('csv'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make([
                'name' => 'full_name',
                'data' => 'full_name',
                'title' => __('Name'),
                'searchPanes' => false,
            ]),
            Column::make([
                'name' => 'created_at',
                'data' => 'created_at',
                'title' => __('Created'),
                'searchPanes' => false,
            ]),
            Column::make([
                'name' => 'updated_at',
                'data' => 'updated_at',
                'title' => __('Updated'),
                'searchPanes' => false,
            ]),
            Column::make([
                'name' => 'email',
                'data' => 'email',
                'title' => __('Email')
            ]),
            Column::make([
                'name' => 'phone',
                'data' => 'phone',
                'title' => __('Phone')
            ]),
            Column::make([
                'name' => 'role',
                'data' => 'role',
                'title' => __('Role')
            ]),
            Column::computed('actions')
                ->exportable(FALSE)
                ->printable(FALSE)
                ->searchPanes(FALSE)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Timecrack User Export ' . date('Y-m-d-His');
    }
}
