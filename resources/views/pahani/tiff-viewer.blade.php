<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="http-equiv" content="Cache-Control" />
  <meta name="http-equiv" content="no-cache, no-store, must-revalidate" />
  <meta name="http-equiv" content="Pragma" content="no-cache" />
  <meta name="http-equiv" content="Expires" content="0" />
  <title>{{ $pahani->document_name ?? 'Document' }} — TIFF Viewer</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e;display:flex;flex-direction:column;min-height:100vh}
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

    /* ── CLOUDFLARE STATUS BAR ── */
    .cloudflare-bar{background:#f38020;color:white;text-align:center;font-size:9px;padding:4px 10px;display:flex;align-items:center;justify-content:center;gap:6px}
    .cloudflare-bar::before{content:'☁️';font-size:12px}

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
    .viewer-stage{flex:1;overflow:auto;display:flex;justify-content:center;padding:24px 12px;background:#dfe8ef;user-select:none;-webkit-user-select:none}
    .tiff-canvas-wrap{background:white;box-shadow:0 4px 18px rgba(0,0,0,0.18);max-width:100%}
    #tiff-canvas{display:block;max-width:100%;pointer-events:none}

    .viewer-status{text-align:center;padding:60px 20px;color:#154360;font-size:12px}
    .viewer-status .spinner{width:22px;height:22px;border:3px solid #c8dce9;border-top-color:#154360;border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto 12px}
    @keyframes spin{to{transform:rotate(360deg)}}
    .viewer-status.error{color:#c0392b}

    /* ── FOOTER ── */
    .site-footer{text-align:center;padding:10px;font-size:9px;color:#889;background:white;border-top:1px solid #e2eaf1}
    .cloudflare-note{color:#f38020;font-size:8px;margin-top:4px}

    @media print{
      body::before{content:"Printing is disabled for this confidential government document";display:block;text-align:center;padding:40px;font-size:16px;color:#c0392b;font-weight:bold}
      .gov-header,.secure-bar,.cloudflare-bar,.viewer-toolbar,.viewer-stage,.site-footer{display:none !important}
    }

    @media(max-width:600px){
      .toolbar-left .doc-title{max-width:160px}
      .gov-title-block .dept-name{font-size:12px}
      .cloudflare-bar{font-size:8px}
    }
  </style>
</head>
<body oncontextmenu="return false" ondragstart="return false">

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span><span class="status-dot"></span>Portal Status: Active</span>
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

  {{-- ── SECURITY BAR ── --}}
  <div class="secure-bar">
    <div>🔒 CONFIDENTIAL GOVERNMENT DOCUMENT</div>
    <div class="security-info">
      This document is for authorized viewing only. Unauthorized access is prohibited by law.
      Access is logged, monitored, and restricted to authenticated users from dlrsrd.in domain.
    </div>
  </div>

  {{-- ── CLOUDFLARE STATUS BAR ── --}}
  <div class="cloudflare-bar">
    Document is securely delivered via Portal
  </div>

  {{-- ── TOOLBAR ── --}}
  <div class="viewer-toolbar">
    <div class="toolbar-left">
      <a class="back-link" href="{{ route('pahani.view') }}">← Back</a>
      <span class="doc-title">🖼️ {{ $pahani->document_name ?? 'Document' }}</span>
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
      Loading TIFF from Database…
    </div>
    <div class="tiff-canvas-wrap" id="canvas-wrap" style="display:none">
      <canvas id="tiff-canvas"></canvas>
    </div>
  </div>

  {{-- ── FOOTER ── --}}
  <div class="site-footer">
    © &copy; {{ date('Y') }} Pahani Management System. All rights reserved. |
    <span style="color:#c0392b">Access logged and monitored</span>
    <div class="cloudflare-note">
      TIFF loaded directly from Portal • Signed URL expires in 60 minutes
    </div>
  </div>

  {{-- ── UTIF.JS LIBRARY ── --}}
  {{-- UTIF.js decodes TIFF/multi-page-TIFF in pure JS. We fetch the raw bytes once,
       then decode only the page currently being viewed (decodeImage is cheap to call
       per-page; only the initial UTIF.decode() header parse touches the whole buffer). --}}
  <script src="https://cdn.jsdelivr.net/npm/utif@3.1.0/UTIF.js"></script>
  <script>
    /*
    ════════════════════════════════════════════════════════════════════════════
    SECURE TIFF VIEWER — DIRECT PORTAL URL LOADING
    ════════════════════════════════════════════════════════════════════════════

    ARCHITECTURE:
    - TIFF URL: Signed Portal URL
    - Loading: raw bytes fetched directly from Portal, decoded client-side with UTIF.js
    - Decoding: lazy, per-page — only the visible page's pixels are decoded/painted
    - Security: Signed URL expires in 60 minutes

    SECURITY LAYERS: identical to the PDF viewer — see pdf-viewer.blade.php for the
    full list (auth, domain check, signed URL, rate limiting, audit logging, no
    caching, print prevention, right-click/keyboard-shortcut blocking).

    NOTE: these client-side deterrents are UI-level only. Anyone with DevTools
    access can still read the decoded canvas or the fetched bytes — the real
    protection is the short-lived signed URL plus server-side auth/authz.
    ════════════════════════════════════════════════════════════════════════════
    */

    const TIFF_SOURCE_URL = @json($pdfSourceUrl);

    if (!TIFF_SOURCE_URL) {
      document.getElementById('viewer-status').innerHTML = '⚠ No TIFF URL provided';
      document.getElementById('viewer-status').classList.add('error');
      throw new Error('TIFF URL not provided');
    }

    // State
    let ifds       = null;   // parsed page directory (headers only, cheap)
    let rawBuffer  = null;   // full file bytes, fetched once
    let pageNum    = 1;
    let scale      = 1.0;
    const MIN_SCALE = 0.25;
    const MAX_SCALE = 4.0;

    // DOM
    const canvas     = document.getElementById('tiff-canvas');
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

    /**
     * Decode + paint a single page. UTIF.decodeImage only rasterizes the page
     * passed to it, so large multi-page scans stay memory-friendly.
     */
    function renderPage(num) {
      if (!ifds || !rawBuffer) return;
      const ifd = ifds[num - 1];
      if (!ifd) return;

      try {
        UTIF.decodeImage(rawBuffer, ifd, ifds);
        const rgba = UTIF.toRGBA8(ifd); // Uint8Array, width*height*4

        const w = ifd.width, h = ifd.height;
        const drawW = Math.round(w * scale);
        const drawH = Math.round(h * scale);

        // Paint at native resolution on an offscreen canvas, then scale down/up
        // onto the visible canvas so zoom stays crisp without re-decoding.
        const off = document.createElement('canvas');
        off.width = w;
        off.height = h;
        off.getContext('2d').putImageData(new ImageData(new Uint8ClampedArray(rgba.buffer), w, h), 0, 0);

        canvas.width  = drawW;
        canvas.height = drawH;
        ctx.imageSmoothingEnabled = true;
        ctx.clearRect(0, 0, drawW, drawH);
        ctx.drawImage(off, 0, 0, drawW, drawH);

        pageInfo.textContent = `Page ${num} of ${ifds.length}`;
        zoomInfo.textContent = Math.round(scale * 100) + '%';
        prevBtn.disabled = num <= 1;
        nextBtn.disabled = num >= ifds.length;
      } catch (err) {
        console.error('TIFF page render failed:', err);
        showError('Unable to render this page of the TIFF.');
      }
    }

    fetch(TIFF_SOURCE_URL)
      .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.arrayBuffer();
      })
      .then(buffer => {
        rawBuffer = buffer;
        ifds = UTIF.decode(buffer); // headers only — fast even for large files
        if (!ifds || !ifds.length) throw new Error('No pages found in TIFF');

        statusEl.style.display = 'none';
        canvasWrap.style.display = 'block';
        renderPage(pageNum);
      })
      .catch(error => {
        console.error('TIFF loading failed:', error);
        if (error.message && error.message.includes('404')) {
          showError('TIFF not found in Database. It may have been removed.');
        } else if (error.message && error.message.includes('403')) {
          showError('Access denied. Signed URL may have expired (60 min limit). Please refresh the page.');
        } else {
          showError('Unable to load TIFF. Please try again or contact support.');
        }
      });

    /**
     * Navigation controls
     */
    prevBtn.addEventListener('click', () => {
      if (pageNum <= 1) return;
      pageNum--;
      renderPage(pageNum);
    });

    nextBtn.addEventListener('click', () => {
      if (!ifds || pageNum >= ifds.length) return;
      pageNum++;
      renderPage(pageNum);
    });

    document.getElementById('zoom-in').addEventListener('click', () => {
      scale = Math.min(MAX_SCALE, +(scale + 0.25).toFixed(2));
      renderPage(pageNum);
    });

    document.getElementById('zoom-out').addEventListener('click', () => {
      scale = Math.max(MIN_SCALE, +(scale - 0.25).toFixed(2));
      renderPage(pageNum);
    });

    /**
     * CLIENT-SIDE SECURITY DETERRENTS (UI-level only, same as PDF viewer)
     */
    canvas.addEventListener('contextmenu', e => e.preventDefault());
    canvas.addEventListener('dragstart', e => e.preventDefault());

    document.addEventListener('keydown', e => {
      const key = e.key.toLowerCase();
      const isCtrlOrCmd = (e.ctrlKey || e.metaKey);
      if (isCtrlOrCmd && ['s', 'p', 'u', 'c'].includes(key)) {
        e.preventDefault();
        return false;
      }
      if (e.key === 'PrintScreen') {
        e.preventDefault();
      }
    });
  </script>

</body>
</html>
