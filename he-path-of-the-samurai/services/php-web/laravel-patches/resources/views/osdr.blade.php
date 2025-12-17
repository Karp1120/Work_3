@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">NASA OSDR</h3>
    <div class="small text-muted mb-2">Источник: {{ $src }}</div>

    {{-- Простой фильтр --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="limit" onchange="this.form.submit()">
                        <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 записей</option>
                        <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20 записей</option>
                        <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 записей</option>
                        <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 записей</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-filter"></i> Применить
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Таблица --}}
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>dataset_id</th>
                    <th>title</th>
                    <th>REST_URL</th>
                    <th>updated_at</th>
                    <th>inserted_at</th>
                    <th>raw</th>
                </tr>
            </thead>
            <tbody>
            @forelse($items as $row)
                <tr>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row['dataset_id'] ?? '—' }}</td>
                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        {{ $row['title'] ?? '—' }}
                    </td>
                    <td>
                        @if(!empty($row['rest_url']))
                            <a href="{{ $row['rest_url'] }}" target="_blank" rel="noopener">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        @else — @endif
                    </td>
                    <td>{{ $row['updated_at'] ?? '—' }}</td>
                    <td>{{ $row['inserted_at'] ?? '—' }}</td>
                    <td>
                        <button class="btn btn-outline-secondary btn-sm" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                            JSON
                        </button>
                    </td>
                </tr>
                <tr class="collapse" id="raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                    <td colspan="7">
                        <pre class="mb-0 small" style="max-height:200px;overflow:auto">{{ 
    json_encode($row['raw'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) 
}}</pre>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">нет данных</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection