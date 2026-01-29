<?php

namespace App\Admin\DataTables;

use BenSampo\Enum\Enum;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;

abstract class BaseDataTable extends DataTable
{
    protected $repository;
    /**
     * ['pageLength', 'excel', 'reset', 'reload']
     *
     * @var array
     */
    protected array $actions = ['reset', 'reload'];
    /**
     * Mảng chứa đường dẫn tới views
     *
     * @var array
     */
    protected $view;
    /**
     * Current Object instance
     *
     * @var object|array|mixed
     */
    protected $instanceDataTable;
    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    protected $instanceHtml;
    /**
     * make columns
     *
     * @var array
     */
    protected $buildColumns = [];

    protected $customColumns = [];

    protected $customEditColumns = [];

    protected $customAddColumns = [];

    protected $customFilterColumns = [];

    protected $customRawColumns = [];

    protected $removeColumns = [];

    protected $columnAllSearch = [];

    protected $columnSearchDate = [];

    protected $columnSearchSelect = [];

    protected $columnSearchSelect2 = [];

    protected $nameTable = 'tableID';
    /**
     * config search columns
     *
     * @var array
     */
    protected $parameters;



    public function __construct(){

        $this->setView();

        $this->setCustomColumns();

        $this->setCustomEditColumns();

        $this->setCustomAddColumns();

        $this->setCustomFilterColumns();

        $this->setCustomRawColumns();

        $this->setParameters();

        $this->setColumnSearch();

        if(!empty($this->removeColumns)){
            foreach($this->removeColumns as $value){
                unset($this->customEditColumns[$value], $this->customAddColumns[$value], $this->customFilterColumns[$value], $this->customRawColumns[$value]);
            }
        }
    }

    abstract protected function setColumnSearch();

    abstract protected function setCustomColumns();

    protected function setCustomEditColumns(){
        $this->customEditColumns = [];
    }

    protected function setCustomAddColumns(){
        $this->customAddColumns = [];
    }

    protected function setCustomFilterColumns(){
        $this->customFilterColumns = [];
    }

    protected function setCustomRawColumns(){
        $this->customRawColumns = [];
    }

    public function getParameters(){
        return $this->parameters ?? [
            // 'responsive' => true,
            // 'ordering' => false,
            'autoWidth' => false,
            // 'searching' => false,
            // 'searchDelay' => 350,
            // 'lengthMenu' => [ [3, 25, 50, -1], [20, 50, 100, "All"] ],
            'language' => [
                'url' => asset('/public/libs/datatables/lang/'.trans()->getLocale().'.json')
            ]
        ];
    }


    public function setParameters(){
        return $this->parameters = $this->getParameters();
    }

    public function setView(){
        $this->view = [];
    }

    protected function getColumns()
    {
        $this->exportVisiableColumns();

        foreach($this->customColumns as $key => $items){
            if(!in_array($key, $this->removeColumns)){

                $buildColumn = Column::make($key);
                foreach($items as $key => $item){
                    if($key == 'title'){
                        $buildColumn = $buildColumn->$key(__($item));
                    }else{
                        $buildColumn = $buildColumn->$key($item);
                    }
                }
                array_push($this->buildColumns, $buildColumn);
            }
        }
        return $this->buildColumns;

    }

    protected function customEditColumns(){
        foreach($this->customEditColumns as $key => $value){
            $this->instanceDataTable = $this->instanceDataTable->editColumn($key, $value);
        }
    }

    protected function customAddColumns(){
        foreach($this->customAddColumns as $key => $value){
            $this->instanceDataTable = $this->instanceDataTable->addColumn($key, $value);
        }
    }

    protected function customFilterColumns(){
        foreach($this->customFilterColumns as $key => $value){
            $this->instanceDataTable = $this->instanceDataTable->filterColumn($key, $value);
        }
    }

    protected function customRawColumns(){
        $this->instanceDataTable = $this->instanceDataTable->rawColumns($this->customRawColumns);
    }

    protected function exportVisiableColumns(){
        if ($this->request && in_array($this->request->get('action'), ['excel', 'csv'])) {
            if ($this->request->get('visible_columns')) {
                foreach ($this->customColumns as $key => $item) {
                    if (!in_array($key, $this->request->get('visible_columns'))) {
                        $this->customColumns[$key]['exportable'] = false;
                    }
                }
            }
        }
    }

    protected function startBuilderDataTable($query){
        $this->instanceDataTable = datatables()->eloquent($query);
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->startBuilderDataTable($query);
        $this->customEditColumns();
        $this->customAddColumns();
        $this->customFilterColumns();
        $this->customRawColumns();
        return $this->instanceDataTable;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $this->instanceHtml = $this->builder()
            ->setTableId($this->nameTable)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0)
            ->selectStyleSingle();

        $this->htmlParameters();

        return $this->instanceHtml;
    }

    protected function htmlParameters(){

        $this->parameters['buttons'] = $this->actions;

        $this->parameters['initComplete'] = "function () {

            moveSearchColumnsDatatable('#{$this->nameTable}');

            searchColumsDataTable(this, ".json_encode($this->columnAllSearch).", ".json_encode($this->columnSearchDate).", ".json_encode($this->columnSearchSelect).", ".json_encode($this->columnSearchSelect2).");

            addWrapTableScroll('#{$this->nameTable}');

            ".( !empty($this->columnSearchSelect2) ? 'addSelect2(); select2LoadDataMany();' : '' )."
        }";

        $this->instanceHtml = $this->instanceHtml
            ->parameters($this->parameters);
    }

    protected function filename(): string
    {
        return $this->nameTable .'-' . date('YmdHis');
    }

    /**
     * Overwrite custom export to CSV (Streaming)
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    /**
     * Export results to Excel file using FastExcel (Streamed)
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string
     */
    /**
     * Export results to Excel file using FastExcel (Streamed)
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string
     */
    public function excel()
    {
        $headers = $this->getExportColumns();
        $filename = $this->filename() . '.csv';

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Write headers
            fputcsv($file, $headers);

            // Write data rows
            foreach ($this->query()->cursor() as $record) {
                $row = $this->mapExportRow($record);
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }

    protected function getExportColumns()
    {
        $columns = [];
        foreach($this->customColumns as $key => $item){
            if (isset($item['exportable']) && $item['exportable'] === false) {
                continue;
            }
            $columns[] = $item['title'] ?? $key;
        }
        return $columns;
    }

    protected function mapExportRow($row)
    {
        $data = [];
        foreach($this->customColumns as $key => $item){
            if (isset($item['exportable']) && $item['exportable'] === false) {
                continue;
            }

            $value = $this->getExportValue($key, $row);

            // Handle if value is array or object, convert to string
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            $data[] = $value;
        }
        return $data;
    }

    protected function getExportValue($key, $row)
    {
        $value = $row->{$key} ?? '';

        // Handle Native Enums (PHP 8.1+) using custom Trait
        if ($value instanceof \BackedEnum && method_exists($value, 'description')) {
            return $value->description();
        }

        // Handle Enums (BenSampo)
        if ($value instanceof Enum) {
            return $value->description ?? $value->key;
        }

        // Handle DateTime objects (Carbon)
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }
}
