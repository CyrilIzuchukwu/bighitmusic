@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Create protected document invitation</h2>
    <p>Upload one PDF for one signer. The system generates the QR code and sends it with the access code. The PDF is never attached to the email.</p>
    @if($errors->any())<div class="alert alert-danger">Please correct the highlighted fields.</div>@endif
    <form class="card p-4" method="post" action="{{ route('signing.admin.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-12 mb-3"><label class="form-label" for="document-title">Document title</label><input class="form-control" id="document-title" name="title" value="{{ old('title') }}" required>@error('title')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-12 mb-4">
                <label class="form-label" for="document-file">PDF document</label>
                <div class="signing-upload" id="signing-upload">
                    <input id="document-file" type="file" name="document" accept=".pdf,application/pdf" required aria-describedby="document-help">
                    <span class="material-icons signing-upload-icon" aria-hidden="true">picture_as_pdf</span>
                    <strong>Choose a PDF or drop it here</strong>
                    <span id="document-help">One PDF, up to 10 MB. You can preview it before continuing.</span>
                </div>
                @error('document')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
                <p class="text-danger mt-2 mb-0" id="document-file-error" role="alert" hidden></p>
                <div class="signing-preview mt-3" id="signing-preview" hidden>
                    <div class="signing-preview-header">
                        <div class="signing-preview-file"><span class="material-icons" aria-hidden="true">description</span><div><strong id="signing-file-name"></strong><small id="signing-file-size"></small></div></div>
                        <button class="btn btn-outline-primary btn-sm" type="button" id="replace-document">Change PDF</button>
                    </div>
                    <iframe id="signing-pdf-preview" title="Selected PDF preview" loading="lazy"></iframe>
                </div>
            </div>
            <div class="col-md-6 mb-3"><label class="form-label">Signer name</label><input class="form-control" name="recipient_name" value="{{ old('recipient_name') }}" required>@error('recipient_name')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-md-6 mb-3"><label class="form-label">Signer email</label><input class="form-control" type="email" name="recipient_email" value="{{ old('recipient_email') }}" required>@error('recipient_email')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-md-6 mb-3"><label class="form-label">Email subject</label><input class="form-control" name="subject" value="{{ old('subject') }}" required>@error('subject')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-md-6 mb-3"><label class="form-label" for="access_code">Access code (optional)</label><input class="form-control" id="access_code" name="access_code" type="text" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" autocomplete="off" value="{{ old('access_code') }}" placeholder="Leave blank to generate 6 digits">@error('access_code')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-12 mb-3"><label class="form-label">Email body</label><textarea class="form-control" name="body" rows="5" required>{{ old('body') }}</textarea>@error('body')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-md-4 mb-3"><label class="form-label">Maximum unlocks</label><input class="form-control" type="number" name="max_opens" min="1" max="1000" value="{{ old('max_opens', 3) }}" required>@error('max_opens')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-md-4 mb-3"><label class="form-label" for="expires_at">Expires (optional)</label><input class="form-control signing-expiry" id="expires_at" type="text" name="expires_at" value="{{ old('expires_at') }}" placeholder="Choose date and time" autocomplete="off">@error('expires_at')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-md-2 mb-3"><label class="form-label">Signature X (%)</label><input class="form-control" type="number" name="signature_x" min="0" max="90" value="{{ old('signature_x', 60) }}" required></div>
            <div class="col-md-2 mb-3"><label class="form-label">Signature Y (%)</label><input class="form-control" type="number" name="signature_y" min="0" max="90" value="{{ old('signature_y', 80) }}" required></div>
        </div>
        <p class="text-muted">The signature is placed on the last page. X and Y are its position from the top left. You can review the PDF before sending.</p>
        <div><button class="btn btn-primary">Prepare document</button> <a class="btn btn-outline-secondary" href="{{ route('signing.admin.index') }}">Cancel</a></div>
    </form>
</div>
@include('admin.signing.expiry-picker')
<style>
    .signing-upload{position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;min-height:170px;padding:22px;border:2px dashed #b7a1e5;border-radius:16px;background:#faf7ff;text-align:center;color:#37236b;transition:background .2s,border-color .2s}
    .signing-upload:hover,.signing-upload.is-dragging,.signing-upload:focus-within{background:#f1eaff;border-color:#895de6}
    .signing-upload input{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}
    .signing-upload-icon{font-size:38px;color:#895de6}.signing-upload span:last-child{color:#77718b;font-size:14px}
    .signing-preview{border:1px solid #e5e0ef;border-radius:14px;overflow:hidden;background:#f8f7fb}
    .signing-preview-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 18px;background:white}
    .signing-preview-file{display:flex;align-items:center;gap:12px;min-width:0}.signing-preview-file .material-icons{color:#895de6;font-size:28px}
    .signing-preview-file div{min-width:0}.signing-preview-file strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.signing-preview-file small{display:block;color:#77718b}
    #signing-pdf-preview{display:block;width:100%;height:min(70vh,640px);border:0;background:#e8e8ed}
    @media(max-width:575px){.signing-preview-header{align-items:flex-start;flex-direction:column}#signing-pdf-preview{height:470px}}
</style>
<script>
    (() => {
        const input = document.getElementById('document-file');
        const zone = document.getElementById('signing-upload');
        const preview = document.getElementById('signing-preview');
        const frame = document.getElementById('signing-pdf-preview');
        const error = document.getElementById('document-file-error');
        let previewUrl = null;

        function showFile() {
            const file = input.files[0];
            error.hidden = true;
            input.setCustomValidity('');
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
            preview.hidden = true;
            frame.removeAttribute('src');
            if (!file) return;
            if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                input.setCustomValidity('Choose a PDF file.');
            } else if (file.size > 10 * 1024 * 1024) {
                input.setCustomValidity('The PDF must be 10 MB or smaller.');
            }
            if (input.validationMessage) {
                error.textContent = input.validationMessage;
                error.hidden = false;
                return;
            }
            document.getElementById('signing-file-name').textContent = file.name;
            document.getElementById('signing-file-size').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            previewUrl = URL.createObjectURL(file);
            frame.src = previewUrl + '#toolbar=0&navpanes=0';
            preview.hidden = false;
        }

        input.addEventListener('change', showFile);
        document.getElementById('replace-document').addEventListener('click', () => input.click());
        ['dragenter', 'dragover'].forEach(type => zone.addEventListener(type, event => { event.preventDefault(); zone.classList.add('is-dragging'); }));
        ['dragleave', 'drop'].forEach(type => zone.addEventListener(type, event => { event.preventDefault(); zone.classList.remove('is-dragging'); }));
        zone.addEventListener('drop', event => {
            if (!event.dataTransfer.files.length) return;
            input.files = event.dataTransfer.files;
            showFile();
        });
        window.addEventListener('beforeunload', () => { if (previewUrl) URL.revokeObjectURL(previewUrl); });
    })();
</script>
@endsection
