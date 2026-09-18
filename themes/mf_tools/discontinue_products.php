<?php
/**
 * Murphy Furniture — Product Discontinuation Script (Module 2)
 * =============================================================
 * Accepts a list of SKUs and unpublishes them from the website (active=0).
 * Products are NOT deleted — they remain in the system but hidden from the website.
 *
 * Usage: https://staging.murphyfurniture.ie/themes/mf_tools/discontinue_products.php?token=MF_DISC_2024
 *
 * Input: Paste SKUs one per line, or comma-separated, or upload a CSV.
 *
 * SECURITY: Delete this file after use.
 */

define('ACCESS_TOKEN', 'MF_DISC_2024');
define('API_KEY',      'C7ME52N9TNU2NWHTZW26G1MSED5GW3UX');
define('API_BASE',     'https://staging.murphyfurniture.ie/api');

// ── Auth ─────────────────────────────────────────────────────────────────────
if (!isset($_GET['token']) || $_GET['token'] !== ACCESS_TOKEN) {
    http_response_code(403);
    die('403 Forbidden — supply ?token=');
}

set_time_limit(300);
ini_set('display_errors', 0);

// ── API helpers ───────────────────────────────────────────────────────────────
function api_get(string $path): ?array {
    $sep = (strpos($path, '?') !== false) ? '&' : '?';
    $url = API_BASE . $path . $sep . 'ws_key=' . API_KEY . '&output_format=JSON';
    $ctx = stream_context_create(['http' => ['timeout' => 30, 'ignore_errors' => true]]);
    $raw = @file_get_contents($url, false, $ctx);
    return $raw ? (json_decode($raw, true) ?: null) : null;
}

function api_get_xml(string $path): ?SimpleXMLElement {
    $sep = (strpos($path, '?') !== false) ? '&' : '?';
    $url = API_BASE . $path . $sep . 'ws_key=' . API_KEY;
    $ctx = stream_context_create(['http' => ['timeout' => 30, 'ignore_errors' => true]]);
    $raw = @file_get_contents($url, false, $ctx);
    if (!$raw) return null;
    libxml_use_internal_errors(true);
    return simplexml_load_string($raw) ?: null;
}

function api_put(string $path, string $xml): array {
    $url = API_BASE . $path . '?ws_key=' . API_KEY;
    $ctx = stream_context_create(['http' => [
        'method'        => 'PUT',
        'header'        => "Content-Type: application/xml\r\nContent-Length: " . strlen($xml),
        'content'       => $xml,
        'timeout'       => 30,
        'ignore_errors' => true,
    ]]);
    $raw  = @file_get_contents($url, false, $ctx);
    $code = 0;
    if (isset($http_response_header)) {
        preg_match('/HTTP\/\S+\s+(\d+)/', $http_response_header[0], $m);
        $code = (int)($m[1] ?? 0);
    }
    return ['code' => $code, 'body' => $raw ?: ''];
}

// ── Find product by SKU ───────────────────────────────────────────────────────
function find_product_by_sku(string $sku): ?array {
    $data = api_get('/products?filter[reference]=' . urlencode($sku) . '&display=[id,reference,name,active]');
    if (!$data || empty($data['products'])) return null;
    $p    = $data['products'][0];
    $name = $p['name'] ?? '';
    if (is_array($name)) $name = $name[0]['value'] ?? '';
    return [
        'id'        => (int)$p['id'],
        'reference' => $p['reference'],
        'name'      => $name,
        'active'    => (int)($p['active'] ?? 0),
    ];
}

// ── Unpublish a product ───────────────────────────────────────────────────────
function unpublish_product(int $productId): array {
    // Fetch full product XML (needed for PUT)
    $xml = api_get_xml("/products/{$productId}");
    if (!$xml) return ['ok' => false, 'error' => 'Could not fetch product XML'];

    // Set active = 0
    $xml->product->active = 0;

    // Remove read-only fields that cause PUT to fail
    $toRemove = ['manufacturer_name', 'quantity'];
    foreach ($toRemove as $field) {
        if (isset($xml->product->$field)) {
            unset($xml->product->$field);
        }
    }

    $putXml = $xml->asXML();
    $result = api_put("/products/{$productId}", $putXml);

    return [
        'ok'    => ($result['code'] === 200),
        'code'  => $result['code'],
        'error' => $result['code'] !== 200 ? "HTTP {$result['code']}" : '',
    ];
}

// ── Parse SKU input ───────────────────────────────────────────────────────────
function parse_skus(string $input): array {
    // Supports newlines, commas, semicolons as separators
    $skus = preg_split('/[\n\r,;]+/', $input);
    $skus = array_map('trim', $skus);
    $skus = array_filter($skus);
    return array_values(array_unique($skus));
}

// ── Process form ──────────────────────────────────────────────────────────────
$results    = [];
$processed  = false;
$skuInput   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $processed = true;

    // Get SKUs from textarea or uploaded CSV
    if (!empty($_FILES['csv']['tmp_name'])) {
        $raw = file_get_contents($_FILES['csv']['tmp_name']);
        $skuInput = $raw;
    } else {
        $skuInput = $_POST['skus'] ?? '';
    }

    $skus = parse_skus($skuInput);

    foreach ($skus as $sku) {
        $product = find_product_by_sku($sku);

        if (!$product) {
            $results[] = [
                'sku'    => $sku,
                'name'   => '—',
                'id'     => '—',
                'status' => 'not_found',
                'detail' => 'SKU not found in Prestashop',
            ];
            continue;
        }

        if ($product['active'] === 0) {
            $results[] = [
                'sku'    => $sku,
                'name'   => $product['name'],
                'id'     => $product['id'],
                'status' => 'already_inactive',
                'detail' => 'Already unpublished',
            ];
            continue;
        }

        // Unpublish it
        $res = unpublish_product($product['id']);

        $results[] = [
            'sku'    => $sku,
            'name'   => $product['name'],
            'id'     => $product['id'],
            'status' => $res['ok'] ? 'unpublished' : 'failed',
            'detail' => $res['ok'] ? 'Successfully unpublished' : $res['error'],
        ];
    }
}

// ── Count results ─────────────────────────────────────────────────────────────
$counts = ['unpublished' => 0, 'already_inactive' => 0, 'not_found' => 0, 'failed' => 0];
foreach ($results as $r) { $counts[$r['status']] = ($counts[$r['status']] ?? 0) + 1; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Discontinue Products — Murphy Furniture</title>
<style>
  body      { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 24px; color: #333; max-width: 900px; }
  h1        { color: #2c3e50; }
  .card     { background: white; border-radius: 8px; padding: 24px; box-shadow: 0 2px 6px rgba(0,0,0,.08); margin-bottom: 24px; }
  textarea  { width: 100%; box-sizing: border-box; height: 180px; font-family: monospace; font-size: .9em; border: 1px solid #ccc; border-radius: 6px; padding: 10px; }
  .btn      { background: #c0392b; color: white; border: none; padding: 12px 28px; border-radius: 6px; font-size: 1em; cursor: pointer; }
  .btn:hover{ background: #a93226; }
  .cards    { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
  .stat     { background: white; border-radius: 8px; padding: 16px 20px; box-shadow: 0 2px 6px rgba(0,0,0,.08); min-width: 120px; }
  .stat .n  { font-size: 2em; font-weight: bold; }
  .green    { color: #27ae60; }
  .red      { color: #c0392b; }
  .orange   { color: #e67e22; }
  .grey     { color: #95a5a6; }
  table     { width: 100%; border-collapse: collapse; }
  th        { text-align: left; padding: 10px; border-bottom: 2px solid #eee; font-size: .9em; background: #f9f9f9; }
  td        { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; font-size: .9em; }
  .unpublished    td { background: #d5f5e3; }
  .already_inactive td { background: #f0f3f4; }
  .not_found td   { background: #fef9e7; }
  .failed td      { background: #fdecea; }
  .note     { background: #fef9e7; border-left: 4px solid #f39c12; padding: 12px 16px; border-radius: 0 6px 6px 0; font-size: .9em; margin-bottom: 16px; }
  a         { color: #2980b9; }
</style>
</head>
<body>

<h1>🚫 Murphy Furniture — Discontinue Products</h1>
<p>Products are <strong>unpublished</strong> (hidden from website) — not deleted.</p>

<?php if (!$processed): ?>

<div class="card">
  <div class="note">
    ⚠️ This will immediately hide products from the website. Double-check your SKUs before submitting.
  </div>
  <h2>Enter SKUs to Discontinue</h2>
  <form method="post" enctype="multipart/form-data">
    <p>Paste SKUs — one per line or comma-separated:</p>
    <textarea name="skus" placeholder="e.g.&#10;100-101-020&#10;100-101-021&#10;WSB-001"></textarea>
    <p style="color:#888;font-size:.9em">Or upload a CSV file (one SKU per row, no header needed):</p>
    <input type="file" name="csv" accept=".csv,.txt">
    <br><br>
    <button type="submit" class="btn">🚫 Unpublish Products</button>
  </form>
</div>

<?php else: ?>

<div class="cards">
  <div class="stat"><div class="n green"><?= $counts['unpublished'] ?></div><div>Unpublished</div></div>
  <div class="stat"><div class="n grey"><?= $counts['already_inactive'] ?></div><div>Already inactive</div></div>
  <div class="stat"><div class="n orange"><?= $counts['not_found'] ?></div><div>SKU not found</div></div>
  <div class="stat"><div class="n red"><?= $counts['failed'] ?></div><div>Errors</div></div>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>SKU</th><th>Product Name</th><th>ID</th><th>Result</th></tr>
    </thead>
    <tbody>
    <?php foreach ($results as $r): ?>
      <tr class="<?= $r['status'] ?>">
        <td><strong><?= htmlspecialchars($r['sku']) ?></strong></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?php if (is_numeric($r['id'])): ?>
          <a href="/admin298bpaddt/index.php?controller=AdminProducts&id_product=<?= $r['id'] ?>&updateproduct" target="_blank"><?= $r['id'] ?></a>
        <?php else: ?>—<?php endif; ?></td>
        <td>
          <?php
            $icons = ['unpublished' => '✅', 'already_inactive' => '⚫', 'not_found' => '⚠️', 'failed' => '❌'];
            echo ($icons[$r['status']] ?? '') . ' ' . htmlspecialchars($r['detail']);
          ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<p><a href="?token=<?= ACCESS_TOKEN ?>">← Discontinue more products</a></p>

<?php endif; ?>

<p style="color:#aaa;font-size:.8em;margin-top:20px">Murphy Furniture Product Management System — discontinue_products.php &nbsp;|&nbsp; ⚠️ Delete after use.</p>
</body>
</html>
