@php
  $title = 'Application ' . $record->application_number;
  $moduleName = $record->moduleMaster?->name ?? $record->module;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="referrer" content="no-referrer">
  <meta name="robots" content="noindex, nofollow">
  <title>{{ $title }} — Bhu Bharathi Secure Viewer</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e;display:flex;flex-direction:column;min-height:100vh;
         user-select:none;-webkit-user-select:none;-ms-user-select:none;-webkit-touch-callout:none}
    ::selection{background:transparent}

    /* ── HEADER ── */
    .gov-header{background:linear-gradient(135deg,#154360 0%,#1a5276 50%,#1e618f 100%);color:white;border-bottom:4px solid #f39c12}
    .gov-top-bar{background:#0d2d47;display:flex;align-items:center;justify-content:space-between;padding:6px 20px;font-size:10px;color:#b8cdd9}
    .gov-logo-row{display:flex;align-items:center;gap:16px;padding:10px 20px 8px}
    .emblem{width:44px;height:44px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:2px solid #f39c12;flex-shrink:0}
    .gov-title-block{flex:1}
    .gov-title-block .dept-name{font-size:14px;font-weight:bold;color:white;line-height:1.2;text-transform:uppercase;letter-spacing:1px}
    .gov-title-block .dept-sub{font-size:10px;color:#aed6f1;margin-top:2px}
    .status-dot{width:8px;height:8px;border-radius:50%;background:#27ae60;display:inline-block;margin-right:4px}

    /* ── SECURITY BAR ── */
    .secure-bar{background:#7d0000;color:#ffd6d6;text-align:center;font-size:10px;padding:5px 10px;letter-spacing:0.3px}
    .security-info{font-size:9px;color:#ffaaaa;margin-top:3px}

    /* ── RECORD STRIP ── */
    .record-bar{background:#e8f5e9;border-bottom:1px solid #a5d6a7;color:#145a45;font-size:10px;padding:6px 16px;display:flex;flex-wrap:wrap;gap:6px 18px;justify-content:center}
    .record-bar b{color:#0b3d2e}

    /* ── TOOLBAR ── */
    .viewer-toolbar{background:#154360;border-bottom:2px solid #f39c12;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:8px 16px}
    .toolbar-left{display:flex;align-items:center;gap:10px;min-width:0}
    .toolbar-left .doc-title{color:white;font-size:12px;font-weight:bold;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:340px}
    .back-link{color:#aed6f1;text-decoration:none;font-size:11px;display:flex;align-items:center;gap:4px;flex-shrink:0}
    .back-link:hover{color:white}
    .toolbar-right{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .tb-btn{background:rgba(255,255,255,0.1);color:white;border:1px solid rgba(255,255,255,0.2);width:28px;height:28px;border-radius:3px;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;transition:background 0.15s}
    .tb-btn:hover{background:rgba(255,255,255,0.22)}
    .tb-btn[disabled]{opacity:0.4;cursor:not-allowed}
    .page-info,.zoom-info{color:#d6eaf8;font-size:11px;padding:0 6px;white-space:nowrap}

    /* ── VIEWER STAGE ── */
    .viewer-stage{flex:1;overflow:auto;display:flex;justify-content:center;padding:24px 12px;background:#dfe8ef;position:relative}
    .pdf-canvas-wrap{background:white;box-shadow:0 4px 18px rgba(0,0,0,0.18);max-width:100%;transition:filter 0.15s}
    #pdf-canvas{display:block;max-width:100%;pointer-events:none}

    /* Hidden while the window is not focused (screenshot tools, screen sharing, switching apps) */
    body.privacy .pdf-canvas-wrap{filter:blur(18px)}
    .privacy-note{display:none;position:fixed;inset:auto 0 40% 0;text-align:center;z-index:50;pointer-events:none}
    .privacy-note span{background:#154360;color:white;padding:10px 18px;border-radius:3px;font-size:12px;font-weight:bold;border-left:4px solid #f39c12}
    body.privacy .privacy-note{display:block}

    .viewer-status{text-align:center;padding:60px 20px;color:#154360;font-size:12px}
    .viewer-status .spinner{width:22px;height:22px;border:3px solid #c8dce9;border-top-color:#154360;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 12px}
    @keyframes spin{to{transform:rotate(360deg)}}
    .viewer-status.error{color:#c0392b}

    /* ── FOOTER ── */
    .site-footer{text-align:center;padding:10px;font-size:9px;color:#889;background:white;border-top:1px solid #e2eaf1}
    .expiry-note{color:#7d0000;font-size:8px;margin-top:4px}

    @media print{
      body::before{content:"Printing is disabled for this confidential government document";display:block;text-align:center;padding:40px;font-size:16px;color:#c0392b;font-weight:bold}
      .gov-header,.secure-bar,.record-bar,.viewer-toolbar,.viewer-stage,.site-footer,.privacy-note{display:none !important}
    }

    @media(max-width:600px){
      .toolbar-left .doc-title{max-width:160px}
      .gov-title-block .dept-name{font-size:12px}
    }
  </style>
</head>
<body oncontextmenu="return false" ondragstart="return false" oncopy="return false" oncut="return false">

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span><span class="status-dot"></span>Portal Status: Active</span>
      <span>Secure Document Viewer — Bhu Bharathi</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">⚖️</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Sangareddy &nbsp;|&nbsp; Revenue Department</div>
      </div>
    </div>
  </div>

  {{-- ── SECURITY BAR ── --}}
  <div class="secure-bar">
    <div>🔒 CONFIDENTIAL GOVERNMENT DOCUMENT</div>
    <div class="security-info">
      This document is for authorized viewing only. Unauthorized access, copying or sharing is prohibited by law.
      Every view is logged with your name, IP address and time.
    </div>
  </div>

  {{-- ── RECORD DETAILS ── --}}
  <div class="record-bar">
    <span>Application No.: <b>{{ $record->application_number }}</b></span>
    <span>Module: <b>{{ $moduleName ?? '—' }}</b></span>
    <span>Mandal: <b>{{ $record->mandal?->name ?? '—' }}</b></span>
    <span>Village: <b>{{ $record->village?->name ?? '—' }}</b></span>
  </div>

  {{-- ── TOOLBAR ── --}}
  <div class="viewer-toolbar">
    <div class="toolbar-left">
      <a class="back-link" href="{{ $backUrl }}">← Back</a>
      <span class="doc-title">📄 {{ $title }}</span>
    </div>
    <div class="toolbar-right">
      <button class="tb-btn" id="prev-page" title="Previous page">‹</button>
      <span class="page-info" id="page-info">— / —</span>
      <button class="tb-btn" id="next-page" title="Next page">›</button>
      <span style="width:1px;height:18px;background:rgba(255,255,255,0.25);margin:0 4px"></span>
      <button class="tb-btn" id="zoom-out" title="Zoom out">−</button>
      <span class="zoom-info" id="zoom-info">100%</span>
      <button class="tb-btn" id="zoom-in" title="Zoom in">+</button>
    </div>
  </div>

  {{-- ── VIEWER STAGE ── --}}
  <div class="viewer-stage" id="viewer-stage">
    <div class="viewer-status" id="viewer-status">
      <div class="spinner"></div>
      Loading secure document…
    </div>
    <div class="pdf-canvas-wrap" id="canvas-wrap" style="display:none">
      <canvas id="pdf-canvas"></canvas>
    </div>
  </div>
  <div class="privacy-note"><span>🔒 Document hidden — click here to continue viewing</span></div>

  {{-- ── FOOTER ── --}}
  <div class="site-footer">
    &copy; {{ date('Y') }} Pahani Management System. All rights reserved. |
    <span style="color:#c0392b">Access logged and monitored</span>
    <div class="expiry-note">Secure link expires in {{ $expiresMinutes }} minutes — reopen the document from the list if it stops loading.</div>
  </div>

  {{-- ── PDF.JS LIBRARY ── --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    /*
    ════════════════════════════════════════════════════════════════════════
    SECURE PDF VIEWER — BHU BHARATHI (same model as the Pahani viewer)
    Server-side (the real protection):
      ✓ Login required, Bhu Bharathi VIEW permission for the mandal (admin: is_admin)
      ✓ Private R2 bucket, short-lived signed URL, every view written to the log
      ✓ No-cache, no-frame, no-referrer response headers
    In the browser (deterrents only):
      ✓ Rendered on a canvas — no text layer, nothing to select or copy
      ✓ Viewer watermark (name · email · IP · time) drawn into every page
      ✓ Print, right-click, drag, copy, save, view-source and DevTools shortcuts blocked
      ✓ Document blurred when the window loses focus (screenshot tools, app switching)
    ════════════════════════════════════════════════════════════════════════
    */
    (function () {
      pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

      let PDF_SOURCE_URL = @json($pdfSourceUrl);
      const WATERMARK    = @json($watermark);

      const canvas     = document.getElementById('pdf-canvas');
      const ctx        = canvas.getContext('2d');
      const statusEl   = document.getElementById('viewer-status');
      const canvasWrap = document.getElementById('canvas-wrap');
      const pageInfo   = document.getElementById('page-info');
      const zoomInfo   = document.getElementById('zoom-info');
      const prevBtn    = document.getElementById('prev-page');
      const nextBtn    = document.getElementById('next-page');

      let pdfDoc = null, pageNum = 1, scale = 1.2, renderTask = null;
      const MIN_SCALE = 0.5, MAX_SCALE = 3.0;

      function showError(message) {
        statusEl.classList.add('error');
        statusEl.textContent = '⚠ ' + message;
        statusEl.style.display = 'block';
        canvasWrap.style.display = 'none';
      }

      if (!PDF_SOURCE_URL) { showError('No document URL provided.'); return; }

      /** Tiled diagonal watermark drawn INTO the page image, so screenshots carry it. */
      function drawWatermark(w, h) {
        const fontSize = Math.max(12, Math.round(w / 48));
        ctx.save();
        ctx.globalAlpha = 0.13;
        ctx.fillStyle = '#7d0000';
        ctx.font = `bold ${fontSize}px Arial`;
        ctx.translate(w / 2, h / 2);
        ctx.rotate(-Math.PI / 6);
        const text = 'CONFIDENTIAL · ' + WATERMARK;
        const stepX = ctx.measureText(text).width + fontSize * 4;
        const stepY = fontSize * 7;
        const span = Math.hypot(w, h);
        for (let row = 0, y = -span; y < span; y += stepY, row++) {
          for (let x = -span + (row % 2 ? stepX / 2 : 0); x < span; x += stepX) {
            ctx.fillText(text, x, y);   // every other row shifted half a step
          }
        }
        ctx.restore();
      }

      function renderPage(num) {
        if (!pdfDoc) return;
        pdfDoc.getPage(num).then(page => {
          if (renderTask) renderTask.cancel();          // fast clicking: cancel the previous render
          const viewport = page.getViewport({ scale });
          canvas.width  = viewport.width;
          canvas.height = viewport.height;
          renderTask = page.render({ canvasContext: ctx, viewport });
          renderTask.promise.then(() => {
            drawWatermark(canvas.width, canvas.height);
            pageInfo.textContent = `Page ${num} of ${pdfDoc.numPages}`;
            zoomInfo.textContent = Math.round(scale / 1.2 * 100) + '%';
            prevBtn.disabled = num <= 1;
            nextBtn.disabled = num >= pdfDoc.numPages;
          }).catch(err => { if (err?.name !== 'RenderingCancelledException') console.error(err); });
        });
      }

      // Load straight from R2 via the signed URL, then forget the URL in this scope
      pdfjsLib.getDocument({ url: PDF_SOURCE_URL, withCredentials: false }).promise
        .then(doc => {
          pdfDoc = doc;
          statusEl.style.display = 'none';
          canvasWrap.style.display = 'block';
          renderPage(pageNum);
        })
        .catch(error => {
          console.error('PDF loading failed:', error);
          const msg = String(error?.message || '');
          if (msg.includes('404'))      showError('Document not found. It may have been removed.');
          else if (msg.includes('403')) showError('Secure link has expired. Please go back and open the document again.');
          else                          showError('Unable to load the document. Please try again or contact support.');
        });
      PDF_SOURCE_URL = null;

      prevBtn.addEventListener('click', () => { if (pageNum > 1) renderPage(--pageNum); });
      nextBtn.addEventListener('click', () => { if (pdfDoc && pageNum < pdfDoc.numPages) renderPage(++pageNum); });
      document.getElementById('zoom-in').addEventListener('click', () => { scale = Math.min(MAX_SCALE, scale + 0.2); renderPage(pageNum); });
      document.getElementById('zoom-out').addEventListener('click', () => { scale = Math.max(MIN_SCALE, scale - 0.2); renderPage(pageNum); });

      // ── CLIENT-SIDE DETERRENTS ──
      canvas.addEventListener('contextmenu', e => e.preventDefault());
      canvas.addEventListener('dragstart', e => e.preventDefault());
      ['copy', 'cut', 'paste', 'selectstart'].forEach(ev => document.addEventListener(ev, e => e.preventDefault()));

      document.addEventListener('keydown', e => {
        const key  = (e.key || '').toLowerCase();
        const mod  = e.ctrlKey || e.metaKey;
        const blocked =
          key === 'f12' ||                                                   // DevTools
          (mod && e.shiftKey && ['i', 'j', 'c', 'k'].includes(key)) ||       // DevTools / console / inspector
          (e.metaKey && e.altKey && ['i', 'j', 'c', 'u'].includes(key)) ||   // macOS DevTools / source
          (mod && ['s', 'p', 'u', 'c', 'a', 'x'].includes(key)) ||           // save / print / source / copy / select all
          key === 'printscreen';
        if (blocked) {
          e.preventDefault();
          e.stopPropagation();
          if (key === 'printscreen') hideDocument();
          return false;
        }
      }, true);

      // PrintScreen fires keyup only on some systems: blank the clipboard best-effort
      document.addEventListener('keyup', e => {
        if ((e.key || '').toLowerCase() === 'printscreen') {
          navigator.clipboard?.writeText?.('').catch(() => {});
          hideDocument();
        }
      });

      window.addEventListener('beforeprint', hideDocument);

      // Blur while the window/tab is not focused
      function hideDocument() { document.body.classList.add('privacy'); }
      function showDocument() { if (document.hasFocus() && !document.hidden) document.body.classList.remove('privacy'); }
      window.addEventListener('blur', hideDocument);
      window.addEventListener('focus', showDocument);
      document.addEventListener('visibilitychange', () => document.hidden ? hideDocument() : showDocument());
      document.addEventListener('click', showDocument);
    })();
  </script>

</body>
</html>