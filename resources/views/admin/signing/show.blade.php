@extends('layouts.app')
@section('content')
<div class="container-fluid signing-manage-page py-4">
    <a href="{{ route('signing.admin.index') }}" class="signing-back">&larr; All documents</a>
    <div class="signing-page-heading">
        <div>
            <p class="signing-eyebrow">Protected document</p>
            <h2>{{ $document->title }}</h2>
        </div>
        <span class="signing-status {{ $document->signed_at ? 'is-signed' : ($document->revoked_at ? 'is-disabled' : 'is-open') }}">
            {{ $document->signed_at ? 'Signed' : ($document->revoked_at ? 'Access disabled' : ($document->sent_at ? 'Invitation sent' : 'Draft')) }}
        </span>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">Please check the access settings below.</div>@endif

    <div class="signing-summary">
        <div><span>Signer</span><strong>{{ $document->recipient_name }}</strong><small>{{ $document->recipient_email }}</small></div>
        <div><span>Access code</span><strong class="signing-code">{{ $accessCode }}</strong><small>Six digits</small></div>
        <div><span>Unlocks</span><strong>{{ $document->opens }} / {{ $document->max_opens }}</strong><small>Successful code entries</small></div>
        <div><span>Expires</span><strong>{{ $document->expires_at?->format('M j, Y') ?? 'No expiry' }}</strong>@if($document->expires_at)<small>{{ $document->expires_at->format('g:i A') }}</small>@endif</div>
    </div>

    <section class="signing-panel signing-preview-panel" aria-labelledby="preview-heading">
        <div class="signing-panel-heading">
            <div>
                <p class="signing-step">Step 1</p>
                <h3 id="preview-heading">{{ $document->signed_at ? 'View signed document' : 'Review signature placement' }}</h3>
                @if(!$document->signed_at)<p>{{ $document->sent_at ? 'The signature position is locked because this invitation has been sent.' : 'Drag the purple signature area on the last page. Its new position saves automatically.' }}</p>@endif
            </div>
            @if(!$document->signed_at)<button type="button" class="signing-outline-button" id="jump-to-signature">Jump to signature</button>@endif
        </div>
        @if($document->signed_at)
            <iframe class="signing-final-pdf" title="Signed document" src="{{ route('signing.admin.pdf', $document) }}#toolbar=0"></iframe>
        @else
            <div class="signing-preview-toolbar">
                <span id="position-state" class="signing-position-state" role="status" aria-live="polite">Loading preview…</span>
                <button type="button" class="signing-text-button" id="retry-position" hidden>Retry save</button>
            </div>
            <div class="signing-pdf-scroller" id="signing-pdf-scroller"><p class="signing-loading">Preparing PDF preview…</p></div>
            <p class="signing-preview-error" id="preview-error" role="alert" hidden>Unable to display this PDF. Refresh the page and try again before sending.</p>
            <p class="signing-preview-note">The marked area reflects the size and position used on the signed PDF.</p>
        @endif
    </section>

    @if(!$document->signed_at)
    <div class="signing-actions-grid">
        <section class="signing-panel" aria-labelledby="send-heading">
            <p class="signing-step">Step 2</p>
            <h3 id="send-heading">Send invitation</h3>
            <p>The email will contain the QR code and the six-digit code. The PDF stays private.</p>
            <form method="post" action="{{ route('signing.admin.send', $document) }}" id="send-invitation-form">
                @csrf
                <button type="submit" class="signing-primary-button" {{ $document->revoked_at ? 'disabled' : '' }}>{{ $document->sent_at ? 'Resend invitation' : 'Send invitation' }}</button>
            </form>
            @if($document->revoked_at)<p class="signing-hint">Restore access to send this invitation.</p>@endif
            <p class="signing-action-error" id="send-error" role="alert" hidden>Finish loading the preview or retry saving the signature position before sending.</p>
        </section>

        <section class="signing-panel" aria-labelledby="access-heading">
            <p class="signing-step">Access</p>
            <h3 id="access-heading">Code and availability</h3>
            <form method="post" action="{{ route('signing.admin.settings', $document) }}">
                @csrf @method('PUT')
                <label class="form-label" for="new-access-code">Replace code <span>(optional)</span></label>
                <input class="form-control mb-2" id="new-access-code" name="access_code" type="text" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" autocomplete="off" placeholder="Enter a new six-digit code">
                @error('access_code')<small class="text-danger d-block mb-2">{{ $message }}</small>@enderror
                <label class="form-label" for="max-opens">Maximum unlocks</label>
                <input class="form-control mb-2" id="max-opens" type="number" name="max_opens" min="1" max="1000" value="{{ old('max_opens', $document->max_opens) }}" required>
                <label class="form-label" for="expires_at">Expires <span>(optional)</span></label>
                <input class="form-control mb-2 signing-expiry" id="expires_at" type="text" name="expires_at" value="{{ old('expires_at', $document->expires_at?->format('Y-m-d H:i')) }}" placeholder="Choose date and time" autocomplete="off">
                @error('expires_at')<small class="text-danger d-block mb-2">{{ $message }}</small>@enderror
                <button type="submit" class="signing-outline-button signing-settings-save">Save access settings</button>
            </form>
            <form class="signing-revoke-form" method="post" action="{{ route($document->revoked_at ? 'signing.admin.restore' : 'signing.admin.revoke', $document) }}">
                @csrf
                <button type="submit" class="signing-danger-button">{{ $document->revoked_at ? 'Restore access' : 'Disable access' }}</button>
            </form>
        </section>
    </div>
    @endif

    <section class="signing-panel signing-activity-panel" aria-labelledby="activity-heading">
        <h3 id="activity-heading">Activity</h3>
        <div class="table-responsive">
            <table class="table signing-activity-table">
                <thead><tr><th>Time</th><th>Event</th><th>IP address</th><th>Browser</th></tr></thead>
                <tbody>
                @forelse($events as $event)
                    <tr><td>{{ $event->created_at->format('M j, Y g:i A') }}</td><td>{{ str_replace('_', ' ', ucfirst($event->event)) }}</td><td>{{ $event->ip_address }}</td><td class="signing-browser" title="{{ $event->user_agent }}">{{ $event->user_agent }}</td></tr>
                @empty<tr><td colspan="4">No activity yet.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        {{ $events->links() }}
    </section>
</div>

<style>
    .signing-manage-page{max-width:1500px;margin:0 auto;padding-bottom:90px!important}
    .signing-back{display:inline-block;margin-bottom:14px;color:#7354c7;font-weight:600}
    .signing-page-heading{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:20px}
    .signing-page-heading h2{margin:0}.signing-eyebrow,.signing-step{text-transform:uppercase;letter-spacing:.1em;font-size:12px;font-weight:800;color:#8962df;margin:0 0 7px}
    .signing-status{display:inline-flex;align-items:center;padding:8px 14px;border-radius:50px;background:#f1eafe;color:#6740bc;font-weight:700;white-space:nowrap}
    .signing-status.is-signed{background:#e4f8ef;color:#13734e}.signing-status.is-disabled{background:#fce8e8;color:#a12727}
    .signing-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1px;background:#e9e4f2;border:1px solid #e9e4f2;border-radius:18px;overflow:hidden;margin-bottom:24px}
    .signing-summary>div{min-width:0;background:#fff;padding:18px 20px}.signing-summary span,.signing-summary small{display:block;color:#847d96;font-size:13px}.signing-summary strong{display:block;font-size:17px;color:#34264a;margin:6px 0;overflow-wrap:anywhere}.signing-summary .signing-code{letter-spacing:.16em;font-size:22px}
    .signing-panel{min-width:0;background:#fff;border-radius:18px;padding:24px;box-shadow:0 4px 22px #2c1c5d0b;margin-bottom:24px}
    .signing-panel h3{font-size:21px;margin:0 0 10px;color:#2d2540}.signing-panel p{line-height:1.5}.signing-panel-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:15px}.signing-panel-heading p:last-child{margin:0;color:#81788d}
    .signing-outline-button,.signing-primary-button,.signing-danger-button,.signing-text-button{border-radius:9px;padding:11px 17px;font:inherit;font-weight:700;cursor:pointer;white-space:nowrap}
    .signing-outline-button{border:1px solid #8e66e5;background:#fff;color:#7144d3}.signing-outline-button:hover{background:#f6f0ff}
    .signing-primary-button{border:0;background:#8c5bf1;color:#fff}.signing-primary-button:hover{background:#7546db}.signing-primary-button:disabled{background:#d7cbed;cursor:not-allowed}
    .signing-danger-button{border:1px solid #cf5368;background:#fff;color:#b4344e}.signing-danger-button:hover{background:#fff0f2}
    .signing-text-button{padding:3px 0;border:0;background:transparent;color:#7144d3}
    .signing-preview-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 0 12px}.signing-position-state{display:inline-flex;align-items:center;border-radius:30px;padding:7px 12px;background:#eef7f1;color:#207347;font-size:13px;font-weight:700}.signing-position-state.is-saving{background:#f6f1e6;color:#8d6b18}.signing-position-state.is-error{background:#fff0f0;color:#a32f40}
    .signing-pdf-scroller{height:min(72vh,650px);min-height:420px;overflow:auto;background:#eeedf4;border:1px solid #e0dce9;border-radius:12px;padding:18px}.signing-loading{text-align:center;padding:80px 10px;color:#8c849b}
    .signing-pdf-page{position:relative;background:#fff;margin:0 auto 20px;max-width:100%;box-shadow:0 4px 18px #32284b24}.signing-pdf-page canvas{display:block;width:100%;height:auto}
    .signing-position-marker{position:absolute;display:flex;align-items:center;justify-content:center;text-align:center;padding:4px;box-sizing:border-box;border:2px dashed #7c4cda;border-radius:5px;background:#e6d9ffb8;color:#512999;font-weight:800;font-size:12px;line-height:1.1;cursor:grab;touch-action:none;user-select:none;z-index:2}.signing-position-marker:active{cursor:grabbing}.signing-position-marker.is-locked,.signing-position-marker.is-locked:active{cursor:default}
    .signing-preview-note,.signing-hint{font-size:13px;color:#8a8299;margin:11px 0 0}.signing-preview-error,.signing-action-error{color:#b53045;margin:10px 0 0}
    .signing-final-pdf{display:block;width:100%;height:min(75vh,730px);border:1px solid #e0dce9;border-radius:12px}
    .signing-actions-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:24px;align-items:start}.signing-actions-grid .signing-panel{height:auto}
    .signing-actions-grid .signing-panel>p:not(.signing-step){color:#81788d}.signing-actions-grid .form-label{display:block;margin:11px 0 5px;color:#382d4d;font-weight:600}.signing-actions-grid .form-label span{color:#8b839a;font-weight:400}.signing-settings-save{margin-top:10px}.signing-revoke-form{border-top:1px solid #eee9f5;margin-top:22px;padding-top:18px}
    .signing-activity-panel h3{margin-bottom:18px}.signing-activity-table{min-width:620px;table-layout:fixed}.signing-activity-table th:nth-child(1){width:23%}.signing-activity-table th:nth-child(2){width:26%}.signing-activity-table th:nth-child(3){width:19%}.signing-activity-table th:nth-child(4){width:32%}.signing-activity-table td{vertical-align:middle;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.signing-browser{max-width:0}
    @media(max-width:900px){.signing-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.signing-actions-grid{grid-template-columns:1fr}}
    @media(max-width:600px){.signing-page-heading,.signing-panel-heading{flex-direction:column;align-items:flex-start}.signing-summary{grid-template-columns:1fr 1fr}.signing-summary>div{padding:14px}.signing-panel{padding:17px}.signing-pdf-scroller{padding:10px;min-height:380px}}
</style>

@if(!$document->signed_at)
@include('admin.signing.expiry-picker')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" referrerpolicy="no-referrer"></script>
<script>
(() => {
    const scroller = document.getElementById('signing-pdf-scroller');
    const state = document.getElementById('position-state');
    const retry = document.getElementById('retry-position');
    const sendForm = document.getElementById('send-invitation-form');
    const sendError = document.getElementById('send-error');
    const jump = document.getElementById('jump-to-signature');
    let previewReady = false;
    let saving = false;
    let dirty = false;
    let marker = null;
    let position = {x: @json($document->signature_x), y: @json($document->signature_y)};
    const positionLocked = @json($document->sent_at !== null);

    function setState(message, kind = '') {
        state.textContent = message;
        state.className = 'signing-position-state' + (kind ? ' is-' + kind : '');
    }

    async function savePosition() {
        if (!dirty || saving) return;
        saving = true;
        marker.style.pointerEvents = 'none';
        retry.hidden = true;
        setState('Saving signature position…', 'saving');
        try {
            const response = await fetch(@json(route('signing.admin.position', $document)), {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify({signature_x: position.x, signature_y: position.y})
            });
            if (!response.ok) throw new Error('Save failed');
            dirty = false;
            sendError.hidden = true;
            setState('Signature position saved');
        } catch (error) {
            retry.hidden = false;
            setState('Could not save position', 'error');
        } finally {
            marker.style.pointerEvents = '';
            saving = false;
        }
    }

    async function renderPdf() {
        try {
            if (!window.pdfjsLib) throw new Error('PDF viewer unavailable');
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            const pdf = await pdfjsLib.getDocument({url: @json(route('signing.admin.pdf', $document)), withCredentials: true}).promise;
            scroller.innerHTML = '';
            for (let number = 1; number <= pdf.numPages; number++) {
                const page = await pdf.getPage(number);
                const viewport = page.getViewport({scale: 1.35});
                const pageBox = document.createElement('div');
                pageBox.className = 'signing-pdf-page';
                pageBox.style.width = viewport.width + 'px';
                const canvas = document.createElement('canvas');
                const ratio = window.devicePixelRatio || 1;
                canvas.width = Math.round(viewport.width * ratio);
                canvas.height = Math.round(viewport.height * ratio);
                canvas.style.aspectRatio = viewport.width + '/' + viewport.height;
                pageBox.appendChild(canvas);
                scroller.appendChild(pageBox);
                await page.render({canvasContext: canvas.getContext('2d'), viewport, transform: [ratio, 0, 0, ratio, 0, 0]}).promise;
                if (number === pdf.numPages) addMarker(pageBox, viewport);
            }
            previewReady = true;
            setState(positionLocked ? 'Signature position locked after sending' : 'Signature position saved');
        } catch (error) {
            scroller.innerHTML = '';
            document.getElementById('preview-error').hidden = false;
            setState('Preview unavailable', 'error');
        }
    }

    function addMarker(pageBox, viewport) {
        const widthMm = viewport.width / 1.35 * 25.4 / 72;
        const heightMm = viewport.height / 1.35 * 25.4 / 72;
        const markerWidth = Math.min(50, widthMm * .28) / widthMm * 100;
        const markerHeight = 20 / heightMm * 100;
        const maxX = Math.max(0, Math.min(90, 100 - markerWidth - 5 / widthMm * 100));
        const maxY = Math.max(0, Math.min(90, 100 - markerHeight - 5 / heightMm * 100));
        marker = document.createElement('div');
        marker.className = 'signing-position-marker';
        if (positionLocked) marker.classList.add('is-locked');
        marker.textContent = 'SIGNATURE HERE';
        marker.setAttribute('role', 'img');
        marker.setAttribute('aria-label', 'Signature area on last page');
        marker.style.width = markerWidth + '%';
        marker.style.height = markerHeight + '%';
        pageBox.appendChild(marker);
        function draw() {
            marker.style.left = Math.min(position.x, maxX) + '%';
            marker.style.top = Math.min(position.y, maxY) + '%';
        }
        draw();
        jump.addEventListener('click', () => pageBox.scrollIntoView({behavior: 'smooth', block: 'center'}));
        if (positionLocked) return;
        marker.addEventListener('pointerdown', event => {
            event.preventDefault();
            marker.setPointerCapture(event.pointerId);
            const box = marker.getBoundingClientRect();
            const offsetX = event.clientX - box.left;
            const offsetY = event.clientY - box.top;
            function move(pointer) {
                const rect = pageBox.getBoundingClientRect();
                position = {
                    x: Math.floor(Math.max(0, Math.min(maxX, (pointer.clientX - rect.left - offsetX) / rect.width * 100))),
                    y: Math.floor(Math.max(0, Math.min(maxY, (pointer.clientY - rect.top - offsetY) / rect.height * 100)))
                };
                dirty = true;
                draw();
                setState('Position changed', 'saving');
            }
            function finish() {
                marker.removeEventListener('pointermove', move);
                marker.removeEventListener('pointerup', finish);
                marker.removeEventListener('pointercancel', finish);
                savePosition();
            }
            marker.addEventListener('pointermove', move);
            marker.addEventListener('pointerup', finish);
            marker.addEventListener('pointercancel', finish);
        });
    }

    retry.addEventListener('click', savePosition);
    if (sendForm) sendForm.addEventListener('submit', event => {
        if (!previewReady || saving || dirty) {
            event.preventDefault();
            sendError.hidden = false;
            state.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    });
    renderPdf();
})();
</script>
@endif
@endsection
