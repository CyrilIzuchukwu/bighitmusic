<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow"><title>Sign document</title>
    <style>
        :root{font-family:Arial,sans-serif;color:#202124;background:#f5f4f8}*{box-sizing:border-box}body{margin:0;min-height:100vh}main{max-width:960px;margin:0 auto;padding:24px 16px 60px}.access-layout{max-width:620px;margin:clamp(18px,7vh,80px) auto 0}.card{background:white;border:1px solid #ece9f2;border-radius:16px;padding:30px 34px;box-shadow:0 5px 20px #291a4610;margin-bottom:18px}.document-card h1{font-size:clamp(27px,4vw,36px);line-height:1.2;margin:0 0 12px;color:#27203a;overflow-wrap:anywhere}.document-card p{margin:0;color:#6f667e;font-size:16px}.unlock-card h2{font-size:26px;line-height:1.25;margin:0 0 12px;color:#27203a}.muted{color:#6f667e;line-height:1.55;margin:0 0 25px}.field-label{display:block;margin:0 0 12px;font-weight:600;font-size:14px;color:#3c334b}
        input,button{font:inherit}.code-entry{text-align:center}.code-boxes{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:9px;width:min(100%,450px);margin:0 auto 12px}.code-boxes input{width:100%;min-width:0;height:62px;border:1px solid #cfc8db;border-radius:10px;text-align:center;font-size:27px;font-weight:700;color:#352257;background:white}.code-boxes input:focus{outline:2px solid #7854bd;outline-offset:1px;border-color:#7854bd}.code-boxes input[aria-invalid=true]{border-color:#d64d61}
        button{background:#643aab;color:white;border:0;border-radius:9px;padding:13px 22px;font-weight:600;cursor:pointer}button:hover{background:#522c91}button:focus-visible{outline:3px solid #aa90d7;outline-offset:3px}button:disabled{opacity:.55;cursor:not-allowed}button.secondary{background:#eee9f5;color:#48366b}.unlock-button{margin-top:10px;min-width:220px}.privacy-note{color:#91899e;font-size:12px;line-height:1.5;margin:18px 0 0;text-align:center}
        .error{color:#b82e48;font-size:14px}.error:not([hidden]){background:#fff0f2;border:1px solid #ffd6de;border-radius:10px;padding:10px 12px}.page{position:relative;margin:20px auto;box-shadow:0 12px 32px #2c20402a;max-width:100%;background:white}.page canvas{display:block;width:100%}
        #signature-spot{position:absolute;border:2px dashed #7547c3;background:#f3eaffbb;width:28%;height:9%;display:flex;align-items:center;justify-content:center;text-align:center;overflow:hidden;font-size:13px;color:#4d2c83}
        #signature-spot img{max-width:100%;max-height:100%;object-fit:contain}dialog{border:0;border-radius:22px;box-shadow:0 20px 70px #24143b55;width:min(94vw,600px);padding:28px}dialog::backdrop{background:#201235a8}
        #pad{width:100%;height:auto;border:1px solid #d7cde7;border-radius:12px;touch-action:none;background:white}label{display:block;margin:10px 0}
        @media(max-width:600px){main{padding:18px 14px 40px}.access-layout{margin:12px auto 0}.card{padding:25px 20px}.code-boxes{gap:clamp(4px,1.5vw,8px)}.code-boxes input{height:clamp(46px,12vw,60px);font-size:24px}.unlock-button{width:min(100%,450px)}}
    </style>
</head><body><main>
    @if(!$granted)
        <div class="access-layout"><section class="card document-card"><h1>{{ $document->title }}</h1><p>For {{ $document->recipient_name }}</p></section>
        <section class="card unlock-card"><h2>Enter your access code</h2>
            <p class="muted">Use the six-digit code from your invitation email to open the document.</p>
            @error('access_code')<p class="error">{{ $message }}</p>@enderror
            <form class="code-entry" method="post" action="{{ route('signing.unlock', $token) }}" id="unlock-form">@csrf
                <label class="field-label" id="code-label">Your six-digit code</label>
                <div class="code-boxes" role="group" aria-labelledby="code-label">
                    @for($digit = 1; $digit <= 6; $digit++)
                        <input class="code-digit" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit {{ $digit }} of 6" autocomplete="{{ $digit === 1 ? 'one-time-code' : 'off' }}">
                    @endfor
                </div>
                <input type="hidden" id="access-code-value">
                <noscript><label for="access_code_fallback">Enter all six digits</label><input id="access_code_fallback" name="access_code" type="text" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" required></noscript>
                <p class="error" id="code-entry-error" role="alert" hidden>Enter all six digits to continue.</p>
                <button class="unlock-button">Unlock document</button>
            </form>
            <p class="privacy-note">Your access to this private document is recorded for security.</p>
            <script>
            (() => {
                const form = document.getElementById('unlock-form');
                const boxes = Array.from(form.querySelectorAll('.code-digit'));
                const value = document.getElementById('access-code-value');
                const error = document.getElementById('code-entry-error');
                value.name = 'access_code';
                function sync() {
                    value.value = boxes.map(box => box.value).join('');
                    error.hidden = true;
                    boxes.forEach(box => box.setAttribute('aria-invalid', 'false'));
                }
                function fillFrom(start, text) {
                    const digits = text.replace(/\D/g, '').slice(0, 6 - start);
                    for (let n = 0; n < digits.length; n++) boxes[start + n].value = digits[n];
                    sync();
                    boxes[Math.min(start + digits.length, 5)].focus();
                }
                boxes.forEach((box, index) => {
                    box.addEventListener('focus', () => box.select());
                    box.addEventListener('input', () => {
                        const text = box.value;
                        box.value = '';
                        if (text) fillFrom(index, text);
                        else sync();
                    });
                    box.addEventListener('paste', event => {
                        event.preventDefault();
                        fillFrom(index, event.clipboardData.getData('text'));
                    });
                    box.addEventListener('keydown', event => {
                        if (event.key === 'Backspace' && !box.value && index > 0) {
                            boxes[index - 1].value = '';
                            boxes[index - 1].focus();
                            sync();
                        } else if (event.key === 'ArrowLeft' && index > 0) boxes[index - 1].focus();
                        else if (event.key === 'ArrowRight' && index < 5) boxes[index + 1].focus();
                    });
                });
                form.addEventListener('submit', event => {
                    sync();
                    if (!/^[0-9]{6}$/.test(value.value)) {
                        event.preventDefault();
                        error.hidden = false;
                        boxes.forEach(box => box.setAttribute('aria-invalid', 'true'));
                        (boxes.find(box => !box.value) || boxes[0]).focus();
                    }
                });
            })();
            </script>
        </section></div>
    @else
        <div class="card"><p>Read the whole document before signing. The signature will be placed on the last page in the marked area.</p>
            <div id="pages" aria-label="Document pages">Loading document…</div>
            <p class="error" id="load-error" hidden>Unable to show the document. Please refresh the page or contact the sender.</p>
        </div>
        <div class="card"><h2>Sign and submit</h2>
            @if($errors->any())<p class="error">{{ $errors->first() }}</p>@endif
            <p><button type="button" id="open-signature">Add your signature</button></p>
            <form method="post" action="{{ route('signing.sign', $token) }}" enctype="multipart/form-data" id="sign-form">
                @csrf
                <input type="hidden" name="signature_data" id="signature-data">
                <label><input type="checkbox" name="confirmation" value="1" required> I have reviewed this document and agree to apply my signature.</label>
                <button id="submit-signature" disabled>Verify and submit signature</button>
            </form>
        </div>
        <dialog id="signature-dialog"><h2>Add your signature</h2>
            <p>Draw with a mouse, finger, or stylus, or upload a PNG/JPG image.</p>
            <canvas id="pad" width="560" height="190"></canvas>
            <p><button type="button" class="secondary" id="clear-pad">Clear drawing</button></p>
            <label>Or upload signature image <input type="file" id="signature-upload" name="signature_upload" form="sign-form" accept="image/png,image/jpeg"></label>
            <p><button type="button" id="apply-signature">Use this signature</button> <button type="button" class="secondary" id="close-signature">Cancel</button></p>
        </dialog>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" referrerpolicy="no-referrer"></script>
        <script>
        (() => {
            const pages = document.getElementById('pages');
            const dialog = document.getElementById('signature-dialog');
            const pad = document.getElementById('pad');
            const ctx = pad.getContext('2d');
            const upload = document.getElementById('signature-upload');
            const data = document.getElementById('signature-data');
            let drawing = false, marked = false, spot = null;
            const position = {x: @json($document->signature_x), y: @json($document->signature_y)};
            function point(event) {
                const r = pad.getBoundingClientRect();
                return {x: (event.clientX-r.left)*pad.width/r.width, y:(event.clientY-r.top)*pad.height/r.height};
            }
            pad.addEventListener('pointerdown', event => {pad.setPointerCapture(event.pointerId); drawing=true; marked=true; upload.value=''; const p=point(event); ctx.beginPath();ctx.moveTo(p.x,p.y);});
            pad.addEventListener('pointermove', event => {if(!drawing)return;const p=point(event);ctx.lineWidth=2.5;ctx.lineCap='round';ctx.strokeStyle='#111';ctx.lineTo(p.x,p.y);ctx.stroke();});
            pad.addEventListener('pointerup', () => drawing=false);
            pad.addEventListener('pointercancel', () => drawing=false);
            document.getElementById('open-signature').onclick=()=>dialog.showModal();
            document.getElementById('close-signature').onclick=()=>dialog.close();
            document.getElementById('clear-pad').onclick=()=>{ctx.clearRect(0,0,pad.width,pad.height);marked=false;upload.value='';};
            document.getElementById('apply-signature').onclick=()=>{
                if(!marked && !upload.files.length){alert('Draw or upload your signature first.');return;}
                if(upload.files.length){
                    if(upload.files[0].size>1024*1024){alert('Image must be under 1 MB.');return;}
                    data.value='';
                    const reader=new FileReader();reader.onload=()=>preview(reader.result);reader.readAsDataURL(upload.files[0]);
                } else {data.value=pad.toDataURL('image/png');preview(data.value);}
                document.getElementById('submit-signature').disabled=false;
                dialog.close();
            };
            function preview(src){if(!spot)return;spot.innerHTML='';const img=new Image();img.src=src;img.alt='Your signature preview';spot.appendChild(img);}
            async function render(){
                try {
                    if(!window.pdfjsLib)throw Error('PDF viewer unavailable');
                    pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                    const pdf=await pdfjsLib.getDocument({url:@json(route('signing.pdf', $token)),withCredentials:true}).promise;
                    pages.textContent='';
                    for(let n=1;n<=pdf.numPages;n++){
                        const page=await pdf.getPage(n);const viewport=page.getViewport({scale:1.4});
                        const wrapper=document.createElement('div');wrapper.className='page';wrapper.style.width=viewport.width+'px';
                        const canvas=document.createElement('canvas');const scale=window.devicePixelRatio||1;
                        canvas.width=Math.round(viewport.width*scale);canvas.height=Math.round(viewport.height*scale);
                        canvas.style.aspectRatio=viewport.width+'/'+viewport.height;
                        wrapper.appendChild(canvas);pages.appendChild(wrapper);
                        await page.render({canvasContext:canvas.getContext('2d'),viewport,transform:[scale,0,0,scale,0,0]}).promise;
                        if(n===pdf.numPages){spot=document.createElement('div');spot.id='signature-spot';spot.style.left=position.x+'%';spot.style.top=position.y+'%';spot.textContent='Your signature goes here';wrapper.appendChild(spot);}
                    }
                } catch(e){pages.textContent='';document.getElementById('load-error').hidden=false;}
            }
            render();
        })();
        </script>
    @endif
</main></body></html>
