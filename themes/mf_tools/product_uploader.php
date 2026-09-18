<?php
/**
 * Murphy Furniture — Product Upload Script (Module 1)
 * ====================================================
 * Reads a CSV of new products and creates them in Prestashop via the Webservice API.
 *
 * Usage: https://staging.murphyfurniture.ie/themes/mf_tools/product_uploader.php?token=MF_UPLOAD_2024
 *
 * CSV columns (first row = headers):
 *   product_name, sku, price, category_id, description, meta_title,
 *   meta_description, supplier, stock_quantity, image_url, active
 *
 * Rules:
 *   - price must be between €5 and €10,000 (rejected if outside)
 *   - meta_title auto-generated if blank
 *   - meta_description auto-generated if blank
 *   - active defaults to 1 if blank
 *
 * SECURITY: Delete this file after use.
 */

define('ACCESS_TOKEN', 'MF_UPLOAD_2024');
define('API_KEY',      'C7ME52N9TNU2NWHTZW26G1MSED5GW3UX');
define('API_BASE',     'https://staging.murphyfurniture.ie/api');
define('LANG_ID',      1);   // Prestashop language ID for English
define('SHOP_ID',      1);   // Default shop ID
define('MIN_PRICE',    5.00);
define('MAX_PRICE',    10000.00);

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

function api_post(string $path, string $xml): array {
    $url = API_BASE . $path . '?ws_key=' . API_KEY;
    $ctx = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/xml\r\nContent-Length: " . strlen($xml),
        'content' => $xml,
        'timeout' => 30,
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

// ── Get blank product schema from API ────────────────────────────────────────
function get_blank_product(): ?SimpleXMLElement {
    $url = API_BASE . '/products?ws_key=' . API_KEY . '&schema=blank';
    $ctx = stream_context_create(['http' => ['timeout' => 30, 'ignore_errors' => true]]);
    $raw = @file_get_contents($url, false, $ctx);
    if (!$raw) return null;
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($raw);
    return $xml ?: null;
}

// ── Parse CSV ─────────────────────────────────────────────────────────────────
function parse_csv(string $filePath): array {
    $rows = [];
    if (($fh = fopen($filePath, 'r')) === false) return $rows;
    $headers = null;
    while (($row = fgetcsv($fh, 4096, ',')) !== false) {
        if ($headers === null) {
            $headers = array_map('trim', $row);
            continue;
        }
        if (count($row) !== count($headers)) continue;
        $rows[] = array_combine($headers, array_map('trim', $row));
    }
    fclose($fh);
    return $rows;
}

// ── Validate a row ────────────────────────────────────────────────────────────
function validate_row(array $row): array {
    $errors = [];
    if (empty($row['product_name'])) $errors[] = 'Missing product_name';
    if (empty($row['sku']))          $errors[] = 'Missing sku';
    if (!is_numeric($row['price'] ?? '')) {
        $errors[] = 'Invalid price';
    } else {
        $price = (float)$row['price'];
        if ($price < MIN_PRICE)  $errors[] = "Price €{$price} below minimum €" . MIN_PRICE;
        if ($price > MAX_PRICE)  $errors[] = "Price €{$price} above maximum €" . MAX_PRICE;
    }
    return $errors;
}

// ── Build product XML ─────────────────────────────────────────────────────────
function build_product_xml(array $row): string {
    $name    = htmlspecialchars($row['product_name'], ENT_XML1);
    $sku     = htmlspecialchars($row['sku'],          ENT_XML1);
    $price   = number_format((float)$row['price'], 6, '.', '');
    $active  = ($row['active'] ?? '1') === '0' ? 0 : 1;
    $catId   = (int)($row['category_id'] ?? 2);  // 2 = default Prestashop root category
    $desc    = htmlspecialchars($row['description'] ?? '', ENT_XML1);

    $metaTitle = $row['meta_title'] ?? '';
    if (empty($metaTitle)) {
        $metaTitle = $name . ' | Murphy Furniture Ireland';
    }
    $metaTitle = htmlspecialchars($metaTitle, ENT_XML1);

    $metaDesc = $row['meta_description'] ?? '';
    if (empty($metaDesc)) {
        $metaDesc = "Shop the {$name} at Murphy Furniture Ireland. Free delivery. 5 stores across Dublin, Naas, Carlow, Gorey &amp; Wexford.";
    }
    $metaDesc = htmlspecialchars($metaDesc, ENT_XML1);

    $langId = LANG_ID;

    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
  <product>
    <active>{$active}</active>
    <reference>{$sku}</reference>
    <price>{$price}</price>
    <id_category_default>{$catId}</id_category_default>
    <associations>
      <categories>
        <category><id>{$catId}</id></category>
      </categories>
    </associations>
    <name>
      <language id="{$langId}">{$name}</language>
    </name>
    <description>
      <language id="{$langId}"><![CDATA[{$desc}]]></language>
    </description>
    <description_short>
      <language id="{$langId}"><![CDATA[{$desc}]]></language>
    </description_short>
    <meta_title>
      <language id="{$langId}">{$metaTitle}</language>
    </meta_title>
    <meta_description>
      <language id="{$langId}">{$metaDesc}</language>
    </meta_description>
    <link_rewrite>
      <language id="{$langId}">{$sku}</language>
    </link_rewrite>
  </product>
</prestashop>
XML;
}

// ── Update stock for a newly created product ──────────────────────────────────
function set_stock(int $productId, int $qty): bool {
    // Find the stock_available record for this product
    $data = api_get("/stock_availables?filter[id_product]={$productId}&display=full");
    if (!$data || empty($data['stock_availables'])) return false;
    $stockId = (int)$data['stock_availables'][0]['id'];

    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
  <stock_available>
    <id>{$stockId}</id>
    <id_product>{$productId}</id_product>
    <quantity>{$qty}</quantity>
  </stock_available>
</prestashop>
XML;
    $url = API_BASE . "/stock_availables/{$stockId}?ws_key=" . API_KEY;
    $ctx = stream_context_create(['http' => [
        'method'  => 'PUT',
        'header'  => "Content-Type: application/xml\r\nContent-Length: " . strlen($xml),
        'content' => $xml,
        'timeout' => 30,
        'ignore_errors' => true,
    ]]);
    $raw = @file_get_contents($url, false, $ctx);
    return $raw !== false;
}

// ── Process uploaded CSV ──────────────────────────────────────────────────────
$results  = ['created' => [], 'failed' => [], 'skipped' => []];
$uploaded = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv'])) {
    $uploaded  = true;
    $tmpFile   = $_FILES['csv']['tmp_name'];
    $rows      = parse_csv($tmpFile);

    foreach ($rows as $i => $row) {
        $rowNum = $i + 2; // +2 for header + 1-based

        // Validate
        $errors = validate_row($row);
        if (!empty($errors)) {
            $results['skipped'][] = [
                'row'    => $rowNum,
                'name'   => $row['product_name'] ?? '(blank)',
                'sku'    => $row['sku'] ?? '(blank)',
                'reason' => implode('; ', $errors),
            ];
            continue;
        }

        // Check for duplicate SKU
        $existing = api_get("/products?filter[reference]=" . urlencode($row['sku']) . "&display=[id,reference]");
        if ($existing && !empty($existing['products'])) {
            $results['skipped'][] = [
                'row'    => $rowNum,
                'name'   => $row['product_name'],
                'sku'    => $row['sku'],
                'reason' => 'SKU already exists (product ID ' . $existing['products'][0]['id'] . ')',
            ];
            continue;
        }

        // Create product
        $xml      = build_product_xml($row);
        $response = api_post('/products', $xml);

        if ($response['code'] === 201) {
            // Extract new product ID from response
            libxml_use_internal_errors(true);
            $resXml    = simplexml_load_string($response['body']);
            $newId     = (int)($resXml->product->id ?? 0);

            // Set stock if qty provided
            $qty = (int)($row['stock_quantity'] ?? 0);
            if ($newId > 0 && $qty > 0) {
                set_stock($newId, $qty);
            }

            $results['created'][] = [
                'row'  => $rowNum,
                'name' => $row['product_name'],
                'sku'  => $row['sku'],
                'id'   => $newId,
            ];
        } else {
            $results['failed'][] = [
                'row'    => $rowNum,
                'name'   => $row['product_name'],
                'sku'    => $row['sku'],
                'reason' => "API returned HTTP {$response['code']}",
                'detail' => substr(strip_tags($response['body']), 0, 200),
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Product Uploader — Murphy Furniture</title>
<style>
  body      { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 24px; color: #333; max-width: 1100px; }
  h1        { color: #2c3e50; }
  .card     { background: white; border-radius: 8px; padding: 24px; box-shadow: 0 2px 6px rgba(0,0,0,.08); margin-bottom: 24px; }
  .upload   { border: 2px dashed #bdc3c7; border-radius: 8px; padding: 32px; text-align: center; }
  input[type=file]   { margin: 12px 0; }
  input[type=submit] { background: #27ae60; color: white; border: none; padding: 12px 28px; border-radius: 6px; font-size: 1em; cursor: pointer; }
  input[type=submit]:hover { background: #219a52; }
  .cards    { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
  .stat     { background: white; border-radius: 8px; padding: 16px 20px; box-shadow: 0 2px 6px rgba(0,0,0,.08); min-width: 130px; }
  .stat .n  { font-size: 2em; font-weight: bold; }
  .green    { color: #27ae60; }
  .red      { color: #c0392b; }
  .orange   { color: #e67e22; }
  table     { width: 100%; border-collapse: collapse; }
  th        { text-align: left; padding: 10px; border-bottom: 2px solid #eee; font-size: .9em; }
  td        { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; font-size: .9em; }
  .created  { background: #d5f5e3; }
  .failed   { background: #fdecea; }
  .skipped  { background: #fef9e7; }
  .note     { background: #eaf4fd; border-left: 4px solid #2980b9; padding: 12px 16px; border-radius: 0 6px 6px 0; font-size: .9em; }
  a         { color: #2980b9; }
</style>
</head>
<body>

<h1>📦 Murphy Furniture — Product Uploader</h1>

<?php if (!$uploaded): ?>

<div class="card">
  <h2>Upload Product CSV</h2>
  <div class="note" style="margin-bottom:20px">
    <strong>Required CSV columns (header row must match exactly):</strong><br>
    <code>product_name, sku, price, category_id, description, meta_title, meta_description, supplier, stock_quantity, image_url, active</code><br><br>
    <strong>Rules:</strong> Price must be €<?= MIN_PRICE ?>–€<?= number_format(MAX_PRICE, 0) ?>.
    Leave <code>meta_title</code> and <code>meta_description</code> blank to auto-generate.
    <code>active</code> defaults to 1 (visible) if blank.<br><br>
    <a href="product_uploader_template.csv" download>⬇ Download CSV Template</a>
  </div>
  <form method="post" enctype="multipart/form-data">
    <div class="upload">
      <div>📄 Select your CSV file</div>
      <input type="file" name="csv" accept=".csv" required>
      <br>
      <input type="submit" value="Upload &amp; Create Products">
    </div>
  </form>
</div>

<?php else: ?>

<div class="cards">
  <div class="stat"><div class="n green"><?= count($results['created']) ?></div><div>Created</div></div>
  <div class="stat"><div class="n red"><?= count($results['failed']) ?></div><div>Failed</div></div>
  <div class="stat"><div class="n orange"><?= count($results['skipped']) ?></div><div>Skipped / Invalid</div></div>
</div>

<?php if (!empty($results['created'])): ?>
<div class="card">
  <h3 class="green">✅ Created (<?= count($results['created']) ?>)</h3>
  <table>
    <tr><th>Row</th><th>Product Name</th><th>SKU</th><th>New ID</th><th>Admin Link</th></tr>
    <?php foreach ($results['created'] as $r): ?>
    <tr class="created">
      <td><?= $r['row'] ?></td>
      <td><?= htmlspecialchars($r['name']) ?></td>
      <td><?= htmlspecialchars($r['sku']) ?></td>
      <td><?= $r['id'] ?></td>
      <td><?php if ($r['id']): ?><a href="/admin298bpaddt/index.php?controller=AdminProducts&id_product=<?= $r['id'] ?>&updateproduct" target="_blank">Edit in admin</a><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>

<?php if (!empty($results['failed'])): ?>
<div class="card">
  <h3 class="red">❌ Failed (<?= count($results['failed']) ?>)</h3>
  <table>
    <tr><th>Row</th><th>Product Name</th><th>SKU</th><th>Reason</th><th>Detail</th></tr>
    <?php foreach ($results['failed'] as $r): ?>
    <tr class="failed">
      <td><?= $r['row'] ?></td>
      <td><?= htmlspecialchars($r['name']) ?></td>
      <td><?= htmlspecialchars($r['sku']) ?></td>
      <td><?= htmlspecialchars($r['reason']) ?></td>
      <td><small><?= htmlspecialchars($r['detail']) ?></small></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>

<?php if (!empty($results['skipped'])): ?>
<div class="card">
  <h3 class="orange">⏭ Skipped / Invalid (<?= count($results['skipped']) ?>)</h3>
  <table>
    <tr><th>Row</th><th>Product Name</th><th>SKU</th><th>Reason</th></tr>
    <?php foreach ($results['skipped'] as $r): ?>
    <tr class="skipped">
      <td><?= $r['row'] ?></td>
      <td><?= htmlspecialchars($r['name']) ?></td>
      <td><?= htmlspecialchars($r['sku']) ?></td>
      <td><?= htmlspecialchars($r['reason']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>

<p><a href="?token=<?= ACCESS_TOKEN ?>">← Upload another file</a></p>

<?php endif; ?>

<p style="color:#aaa;font-size:.8em;margin-top:20px">Murphy Furniture Product Management System — product_uploader.php &nbsp;|&nbsp; ⚠️ Delete after use.</p>
</body>
</html>
