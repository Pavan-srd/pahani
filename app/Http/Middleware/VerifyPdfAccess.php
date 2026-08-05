<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VerifyPdfAccess
{
    /**
     * Verify PDF access with multiple security checks
     * 
     * Security checks performed:
     * 1. Authentication - User must be logged in
     * 2. Domain verification - Request must come from authorized domain
     * 3. Rate limiting - Max 100 requests per hour per user
     * 4. IP verification (optional) - User IP must be whitelisted
     */
    public function handle(Request $request, Closure $next)
    {
        // Check 1: Must be authenticated
        if (!Auth::check()) {
            Log::warning('PDF Access Denied - Not Authenticated', [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'referer' => $request->header('Referer'),
            ]);
            return response()->json(['error' => 'Authentication required'], 401);
        }

        // Check 2: Must be from authorized domain
        if (!$this->isAuthorizedDomain($request)) {
            Log::warning('PDF Access Denied - Unauthorized Domain', [
                'user_id' => Auth::id(),
                'referer' => $request->header('Referer'),
                'origin' => $request->header('Origin'),
                'host' => $request->getHost(),
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Access denied - Unauthorized domain'], 403);
        }

        // Check 3: Rate limiting (prevent abuse)
        if (!$this->checkRateLimit($request)) {
            Log::warning('PDF Access Rate Limit Exceeded', [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'error' => 'Rate limit exceeded - Maximum 100 PDF requests per hour',
                'retry_after' => 3600
            ], 429);
        }

        // Check 4: Verify user IP (optional, if enabled in config)
        if (config('filesystems.disks.r2.verify_ip', false)) {
            if (!$this->isWhitelistedIp($request)) {
                Log::warning('PDF Access Denied - Unauthorized IP', [
                    'user_id' => Auth::id(),
                    'ip' => $request->ip(),
                ]);
                return response()->json(['error' => 'Access denied - Unauthorized IP'], 403);
            }
        }

        // All checks passed - log access and continue
        $this->logPdfAccess($request);

        return $next($request);
    }

    /**
     * Verify request is from authorized domain
     * 
     * Checks multiple headers for domain verification:
     * - Referer: Browser sends this when user clicks a link
     * - Origin: Sent for CORS requests
     * - Host: Server request header
     */
    private function isAuthorizedDomain(Request $request): bool
    {
        $allowedDomains = config('filesystems.disks.r2.allowed_domains', ['dlrsrd.in']);
        
        $referer = $request->header('Referer');
        $origin = $request->header('Origin');
        $host = $request->getHost();

        foreach ($allowedDomains as $domain) {
            // Check Referer header
            if ($referer && strpos($referer, $domain) !== false) {
                return true;
            }

            // Check Origin header (for AJAX/fetch requests)
            if ($origin && strpos($origin, $domain) !== false) {
                return true;
            }

            // Check Host header (for direct requests)
            if (strpos($host, $domain) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check rate limiting to prevent abuse
     * 
     * Implementation:
     * - Uses Laravel Cache (Redis preferred for high traffic)
     * - Max 100 PDF requests per hour per user
     * - Cache automatically expires after 1 hour
     * 
     * Example cache keys:
     * - pdf_access_limit_1 (user ID 1)
     * - pdf_access_limit_2 (user ID 2)
     * 
     * Each cache entry increments with every PDF access
     */
    private function checkRateLimit(Request $request): bool
    {
        $userId = Auth::id();
        $cacheKey = "pdf_access_limit_{$userId}";
        
        // Get current count from cache
        $count = Cache::get($cacheKey, 0);
        
        // Increment count
        $count++;
        
        // Store in cache with 1 hour expiry
        Cache::put($cacheKey, $count, 3600);

        // Max 100 requests per hour
        $limit = config('filesystems.disks.r2.rate_limit_per_hour', 100);
        
        return $count <= $limit;
    }

    /**
     * Verify IP is whitelisted (optional feature)
     * 
     * Usage:
     * 1. Set R2_VERIFY_IP=true in .env
     * 2. Add R2_WHITELISTED_IPS=203.0.113.1,203.0.113.2 in .env
     * 3. Only these IPs can access PDFs
     * 
     * Use cases:
     * - Restrict to office IP range
     * - Restrict to government network
     * - Restrict to specific server IPs
     */
    private function isWhitelistedIp(Request $request): bool
    {
        $whitelistedIps = config('filesystems.disks.r2.whitelisted_ips', []);
        
        // If no IPs are configured, allow all
        if (empty($whitelistedIps)) {
            return true;
        }

        // If it's a string (from env), convert to array
        if (is_string($whitelistedIps)) {
            $whitelistedIps = array_map('trim', explode(',', $whitelistedIps));
        }

        $userIp = $request->ip();

        return in_array($userIp, $whitelistedIps);
    }

    /**
     * Log all PDF access for audit trail
     * 
     * Logs to: storage/logs/pdf-access.log
     * Retention: 30 days (configured in logging.php)
     * 
     * Logged information:
     * - User ID and email
     * - Pahani/Document ID
     * - Timestamp (when accessed)
     * - User's IP address
     * - User agent (browser info)
     * - Referer (where request came from)
     * 
     * Use for:
     * - Audit trails
     * - Detecting suspicious access patterns
     * - Investigating security incidents
     * - Compliance reporting
     */
    private function logPdfAccess(Request $request)
    {
        Log::channel('pdf_access')->info('PDF Access Allowed', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'unknown',
            'user_role' => Auth::user()->role ?? 'unknown',
            'pahani_id' => $request->route('pahaniId') ?? 'N/A',
            'timestamp' => now(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'referer' => $request->header('Referer'),
            'origin' => $request->header('Origin'),
            'method' => $request->method(),
            'endpoint' => $request->path(),
        ]);
    }
}