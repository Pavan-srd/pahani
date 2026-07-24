<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $pahani->document_name ?? 'Document' }} — Secure Viewer — Land Record Digitalization</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e;display:flex;flex-direction:column;min-height:100vh}
    ::selection{background:transparent}

    /* ── HEADER (same as portal) ── */
    .gov-header{background:linear-gradient(135deg,#154360 0%,#1a5276 50%,#1e618f 100%);color:white;border-bottom:4px solid #f39c12}
    .gov-top-bar{background:#0d2d47;display:flex;align-items:center;justify-content:space-between;padding:6px 20px;font-size:10px;color:#b8cdd9}
    .gov-top-bar span{display:flex;align-items:center;gap:6px}
    .gov-logo-row{display:flex;align-items:center;gap:16px;padding:10px 20px 8px}
    .emblem{width:44px;height:44px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:2px solid #f39c12;flex-shrink:0}
    .gov-title-block{flex:1}
    .gov-title-block .dept-name{font-size:14px;font-weight:bold;color:white;line-height:1.2;text-transform:uppercase;letter-spacing:1px}
    .gov-title-block .dept-sub{font-size:10px;color:#aed6f1;margin-top:2px}
    .status-dot{width:8px;height:8px;border-radius:50%;background:#27ae60;display:inline-block;margin-right:4px}

    /* ── SECURE BAR ── */
    .secure-bar{background:#7d0000;color:#ffd6d6;text-align:center;font-size:10px;padding:5px 10px;letter-spacing:0.3px}

    /* ── VIEWER TOOLBAR ── */
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
    .viewer-stage{flex:1;overflow:auto;display:flex;justify-content:center;padding:24px 12px;background:#dfe8ef;user-select:none;-webkit-user-select:none}
    .pdf-canvas-wrap{background:white;box-shadow:0 4px 18px rgba(0,0,0,0.18);max-width:100%}
    #pdf-canvas{display:block;max-width:100%;pointer-events:none} /* pointer-events:none blocks easy right-click-save on the image itself */

    .viewer-status{text-align:center;padding:60px 20px;color:#154360;font-size:12px}
    .viewer-status .spinner{width:22px;height:22px;border:3px solid #c8dce9;border-top-color:#154360;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 12px}
    @keyframes spin{to{transform:rotate(360deg)}}
    .viewer-status.error{color:#c0392b}

    /* ── FOOTER (same as portal) ── */
    .site-footer{text-align:center;padding:10px;font-size:10px;color:#889;background:white;border-top:1px solid #e2eaf1}

    /* ── PRINT BLOCK ── */
    @media print{
      body::before{content:"Printing is disabled for this document.";display:block;text-align:center;padding:40px;font-size:16px;color:#c0392b}
      .gov-header,.secure-bar,.viewer-toolbar,.viewer-stage,.site-footer{display:none !important}
    }

    @media(max-width:600px){
      .toolbar-left .doc-title{max-width:160px}
      .gov-title-block .dept-name{font-size:12px}
    }
  </style>
</head>
<body oncontextmenu="return false" ondragstart="return false">

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span><span class="status-dot"></span>Portal Status: Active &nbsp;|&nbsp; Last Updated: {{ date('d-M-Y') }}</span>
      <span>Secure Document Viewer</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">⚖️</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Sangareddy &nbsp;|&nbsp; Revenue Department</div>
      </div>
    </div>
  </div>
  <div class="secure-bar">
    🔒 This document is for official viewing only. Downloading, printing, and copying are restricted.
  </div>

  {{-- ── VIEWER TOOLBAR ── --}}
  <div class="viewer-toolbar">
    <div class="toolbar-left">
      <a class="back-link" href="{{ route('pahani.view') }}">← Back</a>
      <span class="doc-title">📄 {{ $pahani->document_name ?? 'Document' }}</span>
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

  {{-- ── FOOTER ── --}}
  <div class="site-footer">
    © &copy; {{ date('Y') }} Pahani Management System. All rights reserved.
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc =
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const PDF_SOURCE_URL = @json($pdfSourceUrl);

    let pdfDoc     = null;
    let pageNum    = 1;
    let scale      = 1.2;
    const MIN_SCALE = 0.5;
    const MAX_SCALE = 3.0;

    const canvas     = document.getElementById('pdf-canvas');
    const ctx        = canvas.getContext('2d');
    const statusEl   = document.getElementById('viewer-status');
    const canvasWrap = document.getElementById('canvas-wrap');
    const pageInfo   = document.getElementById('page-info');
    const zoomInfo   = document.getElementById('zoom-info');
    const prevBtn    = document.getElementById('prev-page');
    const nextBtn    = document.getElementById('next-page');

    function showError(message) {
      statusEl.classList.add('error');
      statusEl.innerHTML = '⚠ ' + message;
      statusEl.style.display = 'block';
      canvasWrap.style.display = 'none';
    }

    function renderPage(num) {
      pdfDoc.getPage(num).then(page => {
        const viewport = page.getViewport({ scale });
        canvas.width  = viewport.width;
        canvas.height = viewport.height;

        page.render({ canvasContext: ctx, viewport }).promise.then(() => {
          pageInfo.textContent = `Page ${num} of ${pdfDoc.numPages}`;
          zoomInfo.textContent = Math.round(scale / 1.2 * 100) + '%';
          prevBtn.disabled = num <= 1;
          nextBtn.disabled = num >= pdfDoc.numPages;
        });
      });
    }

    // Fetch same-origin (no CORS issues) — see pdfSource() controller method
    pdfjsLib.getDocument(PDF_SOURCE_URL).promise
      .then(doc => {
        pdfDoc = doc;
        statusEl.style.display = 'none';
        canvasWrap.style.display = 'block';
        renderPage(pageNum);
      })
      .catch(() => showError('Unable to load this document. It may have been removed or you may not have permission to view it.'));

    prevBtn.addEventListener('click', () => {
      if (pageNum <= 1) return;
      pageNum--;
      renderPage(pageNum);
    });
    nextBtn.addEventListener('click', () => {
      if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
      pageNum++;
      renderPage(pageNum);
    });
    document.getElementById('zoom-in').addEventListener('click', () => {
      scale = Math.min(MAX_SCALE, scale + 0.2);
      renderPage(pageNum);
    });
    document.getElementById('zoom-out').addEventListener('click', () => {
      scale = Math.max(MIN_SCALE, scale - 0.2);
      renderPage(pageNum);
    });

    /* ── Deterrents (not foolproof, but block the common paths) ── */

    // Right-click already blocked via oncontextmenu on <body>; reinforce on canvas specifically.
    canvas.addEventListener('contextmenu', e => e.preventDefault());
    canvas.addEventListener('dragstart', e => e.preventDefault());

    // Block common save/print/devtools-ish shortcuts.
    document.addEventListener('keydown', e => {
      const k = e.key.toLowerCase();
      const combo = (e.ctrlKey || e.metaKey);
      if (combo && ['s', 'p', 'u', 'c'].includes(k)) {
        e.preventDefault();
      }
      if (e.key === 'PrintScreen') {
        // Cannot truly intercept the OS-level screenshot, but we don't rely on this alone.
      }
    });

    // If the user triggers a print dialog anyway, the @media print rule above hides all content.
    window.addEventListener('beforeprint', () => {
      // no-op — CSS handles the blank output
    });
  </script>

</body>
</html>