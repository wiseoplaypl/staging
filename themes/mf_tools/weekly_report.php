<?php
/**
 * Murphy Furniture — Weekly Report Dashboard (Module 4)
 * ======================================================
 * Single-page overview of the Prestashop product catalogue health.
 * Pulls live data from the Prestashop Webservice API.
 *
 * Usage: https://staging.murphyfurniture.ie/themes/mf_tools/weekly_report.php?token=MF_REPORT_2024
 *
 * Shows:
 *   - Total products (active vs inactive)
 *   - Products missing meta titles
 *   - Products missing images
 *   - Products priced below €5 (critical flags)
 *   - Products with no description
 *   - Low stock items (quantity = 0)
 *
 * SECURITY: Delete this file after use.
 */

define('ACCESS_TOKEN', 'MF_REPORT_2024');
define('API_KEY',      'C7ME52N9TNU2NWHTZW26G1MSED5GW3UX');
define('API_BASE',     'https://staging.murphyfurniture.ie/api');
define('MIN_PRICE',    5.00);
define('BATCH',        250);
define('LANG_ID',      1);

// ── Auth ─────────────────────────────────────────────────────────────────────
if (!isset($_GET['token']) || $_GET['token'] !== ACCESS_TOKEN) {
    http_response_code(403);
    die('403 Forbidden — supply ?token=');
}

set_time_limit(300);
ini_set('display_errors', 0);
header('Content-Type: text/html; charset=utf-8');

// ── API helper ────────────────────────────────────────────────────────────────
function api_get(string $path): ?array {
    $sep = (strpos($path, '?') !== false) ? '&' : '?';
    $url = API_BASE . $path . $sep . 'ws_key=' . API_KEY . '&output_format=JSON';
    $ctx = stream_context_create(['http' => ['timeout' => 30, 'ignore_errors' => true]]);
    $raw = @file_get_contents($url, false, $ctx);
    return $raw ? (json_decode($raw, true) ?: null) : null;
}

// ── Fetch all products (paginated) ────────────────────────────────────────────
function fetch_all_products(): array {
    $all   = [];
    $start = 0;

    while (true) {
        $data = api_get(
            '/products?limit=' . $start . ',' . BATCH
            . '&display=[id,reference,name,description_short,price,active,id_default_image]'
        );

        if (!$data || empty($data['products'])) break;

        foreach ($data['products'] as $p) {
            $name = $p['name'] ?? '';
            if (is_array($name)) $name = $name[0]['value'] ?? '';

            $desc = $p['description_short'] ?? '';
            if (is_array($desc)) $desc = $desc[0]['value'] ?? '';

            $all[] = [
                'id'        => (int)($p['id'] ?? 0),
                'reference' => trim((string)($p['reference'] ?? '')),
                'name'      => $name,
                'desc'      => strip_tags($desc),
                'price'     => (float)($p['price'] ?? 0),
                'active'    => (int)($p['active'] ?? 0),
                'image_id'  => (int)($p['id_default_image'] ?? 0),
            ];
        }

        if (count($data['products']) < BATCH) break;
        $start += BATCH;
    }

    return $all;
}

// ── Fetch meta titles for all products ───────────────────────────────────────
function fetch_meta_data(): array {
    // Fetch products with meta info — done in separate calls due to API field limits
    $meta  = [];
    $start = 0;

    while (true) {
        $data = api_get(
            '/products?limit=' . $start . ',' . BATCH
            . '&display=[id,meta_title]'
        );
        if (!$data || empty($data['products'])) break;
        foreach ($data['products'] as $p) {
            $title = $p['meta_title'] ?? '';
            if (is_array($title)) $title = $title[0]['value'] ?? '';
            $meta[(int)$p['id']] = trim($title);
        }
        if (count($data['products']) < BATCH) break;
        $start += BATCH;
    }

    return $meta;
}

// ── Fetch stock levels ────────────────────────────────────────────────────────
function fetch_stock(): array {
    $stock = [];
    $start = 0;

    while (true) {
        $data = api_get(
            '/stock_availables?limit=' . $start . ',500'
            . '&display=[id_product,quantity]'
        );
        if (!$data || empty($data['stock_availables'])) break;
        foreach ($data['stock_availables'] as $s) {
            $pid = (int)$s['id_product'];
            $qty = (int)$s['quantity'];
            // Sum across warehouses
            $stock[$pid] = ($stock[$pid] ?? 0) + $qty;
        }
        if (count($data['stock_availables']) < 500) break;
        $start += 500;
    }

    return $stock;
}

// ── Run all queries ───────────────────────────────────────────────────────────
$startTime = microtime(true);

$products = fetch_all_products();
$metaData = fetch_meta_data();
$stockData = fetch_stock();

$elapsed = round(microtime(true) - $startTime, 1);

// ── Analyse ───────────────────────────────────────────────────────────────────
$total         = count($products);
$activeCount   = 0;
$inactiveCount = 0;
$noMeta        = [];
$noImage       = [];
$priceFlagged  = [];
$noDesc        = [];
$outOfStock    = [];

foreach ($products as $p) {
    if ($p['active']) $activeCount++; else $inactiveCount++;

    // Missing meta title
    $meta = $metaData[$p['id']] ?? '';
    if (empty($meta)) {
        $noMeta[] = $p;
    }

    // No image
    if ($p['image_id'] === 0) {
        $noImage[] = $p;
    }

    // Price flag
    if ($p['price'] > 0 && $p['price'] < MIN_PRICE) {
        $priceFlagged[] = $p;
    }

    // No description
    if (empty(trim($p['desc']))) {
        $noDesc[] = $p;
    }

    // Out of stock (active products only)
    if ($p['active'] && ($stockData[$p['id']] ?? 0) <= 0) {
        $outOfStock[] = $p;
    }
}

// Health score: 100 - deductions per issue category
$score = 100;
if ($total > 0) {
    $score -= min(30, round(count($noMeta) / $total * 100));
    $score -= min(20, round(count($noImage) / $total * 100));
    $score -= min(25, round(count($noDesc) / $total * 100));
    $score -= min(25, count($priceFlagged) > 0 ? 25 : 0);
}
$score = max(0, $score);

$scoreColor = $score >= 80 ? '#27ae60' : ($score >= 50 ? '#e67e22' : '#c0392b');

// Format run date
$runDate = date('l, d F Y — H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Weekly Report — Murphy Furniture</title>
<style>
  * { box-sizing: border-box; }
  body      { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 24px; color: #333; }
  h1        { color: #2c3e50; margin-bottom: 4px; }
  .sub      { color: #888; font-size: .9em; margin-bottom: 24px; }
  .grid     { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
  .kpi      { background: white; border-radius: 10px; padding: 20px 24px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .kpi .n   { font-size: 2.4em; font-weight: bold; line-height: 1; }
  .kpi .l   { color: #888; font-size: .85em; margin-top: 6px; }
  .kpi .bar { height: 4px; border-radius: 2px; margin-top: 12px; background: #eee; }
  .kpi .fill{ height: 100%; border-radius: 2px; }
  .green    { color: #27ae60; }
  .red      { color: #c0392b; }
  .orange   { color: #e67e22; }
  .blue     { color: #2980b9; }
  .grey     { color: #95a5a6; }
  .section  { background: white; border-radius: 10px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.08); margin-bottom: 20px; }
  .section h2 { margin-top: 0; font-size: 1.1em; display: flex; align-items: center; gap: 8px; }
  table     { width: 100%; border-collapse: collapse; margin-top: 12px; }
  th        { text-align: left; padding: 8px 10px; border-bottom: 2px solid #eee; font-size: .85em; color: #555; background: #fafafa; }
  td        { padding: 7px 10px; border-bottom: 1px solid #f5f5f5; font-size: .85em; }
  .score-ring { text-align: center; padding: 20px; }
  .score-ring .big { font-size: 4em; font-weight: bold; }
  .ok-check { color: #27ae60; font-size: 1.1em; }
  .toggle   { cursor: pointer; color: #2980b9; font-size: .85em; margin-left: 8px; }
  .hidden   { display: none; }
  .tag      { display: inline-block; background: #eee; border-radius: 4px; padding: 1px 6px; font-size: .8em; color: #555; }
  .tag.red  { background: #fdecea; color: #c0392b; }
  .tag.orange { background: #fef9e7; color: #e67e22; }
  .plain-report { display: none; }
</style>
</head>
<body>

<h1>📊 Murphy Furniture — Weekly Report</h1>
<p class="sub"><?= $runDate ?> &nbsp;|&nbsp; Generated in <?= $elapsed ?>s &nbsp;|&nbsp; Staging site</p>

<!-- KPI Grid -->
<div class="grid">
  <div class="kpi">
    <div class="n blue"><?= number_format($total) ?></div>
    <div class="l">Total Products</div>
    <div class="bar"><div class="fill" style="width:100%;background:#2980b9"></div></div>
  </div>
  <div class="kpi">
    <div class="n green"><?= number_format($activeCount) ?></div>
    <div class="l">Active (visible)</div>
    <div class="bar"><div class="fill" style="width:<?= $total ? round($activeCount/$total*100) : 0 ?>%;background:#27ae60"></div></div>
  </div>
  <div class="kpi">
    <div class="n grey"><?= number_format($inactiveCount) ?></div>
    <div class="l">Inactive / Hidden</div>
    <div class="bar"><div class="fill" style="width:<?= $total ? round($inactiveCount/$total*100) : 0 ?>%;background:#95a5a6"></div></div>
  </div>
  <div class="kpi score-ring">
    <div class="big" style="color:<?= $scoreColor ?>"><?= $score ?></div>
    <div class="l">Catalogue health score</div>
  </div>
</div>

<!-- Issue Summary -->
<div class="grid">
  <div class="kpi">
    <div class="n <?= count($priceFlagged) > 0 ? 'red' : 'green' ?>"><?= count($priceFlagged) ?></div>
    <div class="l">🚨 Price below €<?= MIN_PRICE ?></div>
  </div>
  <div class="kpi">
    <div class="n <?= count($noMeta) > 0 ? 'orange' : 'green' ?>"><?= count($noMeta) ?></div>
    <div class="l">Missing meta titles</div>
  </div>
  <div class="kpi">
    <div class="n <?= count($noImage) > 0 ? 'orange' : 'green' ?>"><?= count($noImage) ?></div>
    <div class="l">Missing images</div>
  </div>
  <div class="kpi">
    <div class="n <?= count($noDesc) > 0 ? 'orange' : 'green' ?>"><?= count($noDesc) ?></div>
    <div class="l">No description</div>
  </div>
  <div class="kpi">
    <div class="n <?= count($outOfStock) > 0 ? 'orange' : 'green' ?>"><?= count($outOfStock) ?></div>
    <div class="l">Active + out of stock</div>
  </div>
</div>

<?php if (!empty($priceFlagged)): ?>
<!-- Critical: Price Flags -->
<div class="section">
  <h2>🚨 <span class="red">Critical — Products priced below €<?= MIN_PRICE ?></span>
    <span class="tag red"><?= count($priceFlagged) ?> items</span>
  </h2>
  <p style="color:#c0392b;font-size:.9em">These products may have corrupt pricing data. Check immediately.</p>
  <table>
    <tr><th>ID</th><th>SKU</th><th>Name</th><th>Price</th><th>Action</th></tr>
    <?php foreach ($priceFlagged as $p): ?>
    <tr>
      <td><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['reference']) ?></td>
      <td><?= htmlspecialchars($p['name']) ?></td>
      <td style="color:#c0392b;font-weight:bold">€<?= number_format($p['price'], 2) ?></td>
      <td><a href="/admin298bpaddt/index.php?controller=AdminProducts&id_product=<?= $p['id'] ?>&updateproduct" target="_blank">Fix in admin</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php else: ?>
<div class="section">
  <p class="ok-check">✅ No products with price below €<?= MIN_PRICE ?>. All good.</p>
</div>
<?php endif; ?>

<?php
// Helper function to render a collapsible table section
function render_section(string $icon, string $title, string $color, array $items, int $previewCount = 10): void {
    $total = count($items);
    if ($total === 0) {
        echo '<div class="section"><p class="ok-check">✅ ' . htmlspecialchars($title) . ' — none found. All good.</p></div>';
        return;
    }
    $id = 'sec_' . md5($title);
    echo '<div class="section">';
    echo '<h2>' . $icon . ' <span class="' . $color . '">' . htmlspecialchars($title) . '</span>'
       . ' <span class="tag ' . $color . '">' . $total . ' items</span>'
       . ($total > $previewCount ? ' <span class="toggle" onclick="document.getElementById(\'' . $id . '\').classList.toggle(\'hidden\')">Show/hide all</span>' : '')
       . '</h2>';
    echo '<table><tr><th>ID</th><th>SKU</th><th>Name</th><th>Admin</th></tr>';
    $shown = 0;
    echo '<tbody>';
    foreach ($items as $p) {
        $hidden = ($shown >= $previewCount) ? '' : '';
        $rowId  = ($shown >= $previewCount) ? ' id="' . $id . '" class="hidden"' : '';
        if ($shown === $previewCount) echo '<tbody' . $rowId . '>';
        echo '<tr>'
           . '<td>' . $p['id'] . '</td>'
           . '<td>' . htmlspecialchars($p['reference']) . '</td>'
           . '<td>' . htmlspecialchars(mb_substr($p['name'], 0, 60)) . '</td>'
           . '<td><a href="/admin298bpaddt/index.php?controller=AdminProducts&id_product=' . $p['id'] . '&updateproduct" target="_blank">Edit</a></td>'
           . '</tr>';
        $shown++;
    }
    echo '</tbody></table></div>';
}
?>

<?php render_section('📝', 'Products missing meta titles', 'orange', $noMeta); ?>
<?php render_section('🖼️', 'Products missing images', 'orange', $noImage); ?>
<?php render_section('📄', 'Products with no description', 'orange', $noDesc); ?>
<?php render_section('📦', 'Active products out of stock', 'orange', $outOfStock); ?>

<!-- Plain text for Slack copy/paste -->
<div class="section">
  <h2>📋 Slack / Plain Text Summary</h2>
  <p style="font-size:.85em;color:#888">Copy and paste into Slack:</p>
  <textarea style="width:100%;height:180px;font-family:monospace;font-size:.85em;border:1px solid #ddd;border-radius:6px;padding:10px" readonly>
📊 *Murphy Furniture — Weekly Product Report*
📅 <?= date('d M Y') ?>

*Catalogue Overview*
• Total products: <?= number_format($total) ?>
• Active: <?= number_format($activeCount) ?>  |  Inactive: <?= number_format($inactiveCount) ?>
• Catalogue health score: <?= $score ?>/100

*Issues to fix*
<?= count($priceFlagged) > 0 ? '🚨 ' . count($priceFlagged) . ' products priced below €' . MIN_PRICE . ' — URGENT' : '✅ No price flags' ?>

<?= count($noMeta)   > 0 ? '⚠️ ' . count($noMeta)   . ' products missing meta titles' : '✅ All meta titles present' ?>

<?= count($noImage)  > 0 ? '⚠️ ' . count($noImage)  . ' products missing images' : '✅ All products have images' ?>

<?= count($noDesc)   > 0 ? '⚠️ ' . count($noDesc)   . ' products with no description' : '✅ All products have descriptions' ?>

<?= count($outOfStock) > 0 ? '⚠️ ' . count($outOfStock) . ' active products out of stock' : '✅ All active products have stock' ?>

_Generated: <?= date('d M Y H:i') ?> — staging.murphyfurniture.ie_
  </textarea>
</div>

<p style="color:#aaa;font-size:.8em;margin-top:20px">Murphy Furniture Product Management System — weekly_report.php &nbsp;|&nbsp; ⚠️ Delete after use.</p>

</body>
</html>
