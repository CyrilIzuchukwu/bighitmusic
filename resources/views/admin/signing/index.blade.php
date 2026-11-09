@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Protected documents</h2>
        <a class="btn btn-primary" href="{{ route('signing.admin.create') }}">Create invitation</a>
    </div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    <div class="card"><div class="card-body table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Document</th><th>Recipient</th><th>Status</th><th>Opens</th><th>Created</th><th></th></tr></thead>
            <tbody>
            @forelse($documents as $document)
                <tr>
                    <td>{{ $document->title }}</td>
                    <td>{{ $document->recipient_name }}<br><small>{{ $document->recipient_email }}</small></td>
                    <td>{{ $document->signed_at ? 'Signed' : ($document->revoked_at ? 'Disabled' : ($document->sent_at ? 'Sent' : 'Draft')) }}</td>
                    <td>{{ $document->opens }} / {{ $document->max_opens }}</td>
                    <td>{{ $document->created_at->format('M j, Y') }}</td>
                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('signing.admin.show', $document) }}">Manage</a></td>
                </tr>
            @empty
                <tr><td colspan="6">No protected documents yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $documents->links() }}
    </div></div>
</div>
@endsection
