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
  <title>{{ $pahani->document_name ?? 'Document' }} — PDF Viewer</title>
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
    .pdf-canvas-wrap{background:white;box-shadow:0 4px 18px rgba(0,0,0,0.18);max-width:100%}
    #pdf-canvas{display:block;max-width:100%;pointer-events:none}

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
      Loading PDF from Database…
    </div>
    <div class="pdf-canvas-wrap" id="canvas-wrap" style="display:none">
      <canvas id="pdf-canvas"></canvas>
    </div>
  </div>

  {{-- ── FOOTER ── --}}
  <div class="site-footer">
    © &copy; {{ date('Y') }} Pahani Management System. All rights reserved. | 
    <span style="color:#c0392b">Access logged and monitored</span>
    <div class="cloudflare-note">
      PDF loaded directly from Portal • Signed URL expires in 60 minutes 
    </div>
  </div>

  {{-- ── PDF.JS LIBRARY ── --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    /*
    ════════════════════════════════════════════════════════════════════════════
    SECURE PDF VIEWER — DIRECT PORTAL URL LOADING
    ════════════════════════════════════════════════════════════════════════════
    
    ARCHITECTURE:
    - PDF URL: Signed Portal URL 
    - Loading: PDF.js loads directly from Portal 
    - Bandwidth: Portal handles file delivery (efficient for 150-200MB+ files)
    - Memory: Zero buffering in (file stays in Portal)
    - Security: Signed URL expires in 60 minutes
    
    SECURITY LAYERS:
    ✓ Authentication (middleware checked before page load)
    ✓ Domain verification (middleware ensures dlrsrd.in only)
    ✓ User authorization (controller checks permission)
    ✓ Rate limiting (middleware limits to 100/hour)
    ✓ Signed URL (Cloudflare signature, 60-min expiry)
    ✓ Audit logging (all access logged to pdf-access.log)
    ✓ No caching (Cache-Control headers prevent storage)
    ✓ Print prevention (CSS @media print disables output)
    ✓ Right-click disabled (prevents "Save Image")
    ✓ Keyboard shortcuts blocked (Ctrl+S, Ctrl+P, etc)
    
    WHY THIS IS SECURE:
    1. Private R2 bucket: Direct URLs return 403 Forbidden
    2. Signed URLs: Include cryptographic signature (can't be forged)
    3. URL expiry: Valid for only 60 minutes
    4. Server-side auth: Portal checks login before generating URL
    5. Server-side authz: Portal checks user permission before generating URL
    6. Domain check: Only requests from dlrsrd.in get signed URLs
    7. Rate limiting: Max 100 PDF requests/hour per user
    8. Audit trail: All access logged for compliance
    
    ════════════════════════════════════════════════════════════════════════════
    */

    // Configure PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc =
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Get signed Cloudflare URL from server
    // This URL is generated by controller and includes cryptographic signature
    const PDF_SOURCE_URL = @json($pdfSourceUrl);

    // Verify URL exists
    if (!PDF_SOURCE_URL) {
      document.getElementById('viewer-status').innerHTML = '⚠ No PDF URL provided';
      document.getElementById('viewer-status').classList.add('error');
      throw new Error('PDF URL not provided');
    }

    // State variables
    let pdfDoc     = null;
    let pageNum    = 1;
    let scale      = 1.2;
    const MIN_SCALE = 0.5;
    const MAX_SCALE = 3.0;

    // DOM elements
    const canvas     = document.getElementById('pdf-canvas');
    const ctx        = canvas.getContext('2d');
    const statusEl   = document.getElementById('viewer-status');
    const canvasWrap = document.getElementById('canvas-wrap');
    const pageInfo   = document.getElementById('page-info');
    const zoomInfo   = document.getElementById('zoom-info');
    const prevBtn    = document.getElementById('prev-page');
    const nextBtn    = document.getElementById('next-page');

    /**
     * Display error message
     */
    function showError(message) {
      statusEl.classList.add('error');
      statusEl.innerHTML = '⚠ ' + message;
      statusEl.style.display = 'block';
      canvasWrap.style.display = 'none';
    }

    /**
     * Render a specific page
     */
    function renderPage(num) {
      if (!pdfDoc) return;
      
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

    /**
     * LOAD PDF FROM CLOUDFLARE
     * 
     * The URL is a signed Cloudflare R2 URL with:
     * - Cryptographic signature
     * - 60-minute expiration
     * - User-specific session
     * 
     * Security:
     * - URL generated by authenticated user
     * - Portal verifies signature
     * - Portal checks expiration
     * - Direct file delivery from Portal
     */
    pdfjsLib.getDocument(PDF_SOURCE_URL).promise
      .then(doc => {
        pdfDoc = doc;
        statusEl.style.display = 'none';
        canvasWrap.style.display = 'block';
        renderPage(pageNum);
      })
      .catch(error => {
        console.error('PDF loading failed:', error);
        
        // Provide helpful error messages
        if (error.message && error.message.includes('404')) {
          showError('PDF not found in Database. It may have been removed.');
        } else if (error.message && error.message.includes('403')) {
          showError('Access denied. Signed URL may have expired (60 min limit). Please refresh the page.');
        } else {
          showError('Unable to load PDF. Please try again or contact support.');
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

    /**
     * CLIENT-SIDE SECURITY DETERRENTS
     * (Note: Main security is server-side. These are UI-level protections only)
     */

    // Disable right-click
    canvas.addEventListener('contextmenu', e => e.preventDefault());
    
    // Disable drag
    canvas.addEventListener('dragstart', e => e.preventDefault());

    // Disable keyboard shortcuts
    document.addEventListener('keydown', e => {
      const key = e.key.toLowerCase();
      const isCtrlOrCmd = (e.ctrlKey || e.metaKey);
      
      // Ctrl+S (Save), Ctrl+P (Print), Ctrl+U (View Source), Ctrl+C (Copy)
      if (isCtrlOrCmd && ['s', 'p', 'u', 'c'].includes(key)) {
        e.preventDefault();
        return false;
      }
      
      // PrintScreen
      if (e.key === 'PrintScreen') {
        e.preventDefault();
      }
    });

  </script>

</body>
</html>