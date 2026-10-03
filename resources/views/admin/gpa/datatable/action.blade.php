@if($row->children)
    <a href="{{ route('admin.children.edit', ['id' => $row->children->id, 'tab' => 'report-card']) }}"
       class="btn btn-sm btn-outline-primary" title="Xem học bạ của trẻ">
        <i class="ti ti-eye me-1"></i>Xem học bạ
    </a>
@endif
