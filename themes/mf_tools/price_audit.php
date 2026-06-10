<?php
/**
 * Murphy Furniture — Price Audit Script (Module 3)
 * ================================================
 * Scans all active Prestashop products and flags:
 *   - Any product priced below €5 (critical — likely data error)
 *   - Any product with >30% price change vs stored baseline
 *
 * Usage: https://staging.murphyfurniture.ie/themes/mf_tools/price_audit.php?token=MF_AUDIT_2024
 *
 * On a clean run (no critical flags), the price baseline is updated automatically.
 * SECURITY: Delete this file after use.
 */

define('ACCESS_TOKEN', 'MF_AUDIT_2024');
define('API_KEY',      'C7ME52N9TNU2NWHTZW26G1MSED5GW3UX');
define('API_BASE',     'https://staging.murphyfurniture.ie/api');
define('BASELINE_FILE', __DIR__ . '/price_baseline.json');
define('MIN_PRICE',    5.00);
define('MAX_CHANGE',   30);   // percent
define('BATCH',        250);

// ── Auth ────────────────────────────────────────────────────────────────────
if (!isset($_GET['token']) || $_GET['token'] !== ACCESS_TOKEN) {
    http_response_code(403);
    die('403 Forbidden — supply ?token=');
}

set_time_limit(300);
ini_set('display_errors', 0);
header('Content-Type: text/html; charset=utf-8');

// ── API helper ───────────────────────────────────────────────────────────────
function api_get(string $path): ?array {
    $sep = (strpos($path, '?') !== false) ? '&' : '?';
    $url = API_BASE . $path . $sep . 'ws_key=' . API_KEY . '&output_format=JSON';
    $ctx = stream_context_create(['http' => ['timeout' => 30, 'ignore_errors' => true]]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) return null;
    return json_decode($raw, true) ?: null;
}

// ── Fetch all active products ────────────────────────────────────────────────
function fetch_all_products(): array {
    $products = [];
    $start    = 0;

    while (true) {
        $data = api_get("/products?limit={$start}," . BATCH
            . '&filter[active]=1'
            . '&display=[id,reference,name,price,active]');

        if (!$data || empty($data['products'])) break;

        foreach ($data['products'] as $p) {
            $name = $p['name'] ?? '';
            if (is_array($name)) {
                $name = $name[0]['value'] ?? '';
            }
            $products[] = [
                'id'        => (int) $p['id'],
                'reference' => trim((string)($p['reference'] ?? '')),
                'name'      => $name,
                'price'     => (float)($p['price'] ?? 0),
            ];
        }

        if (count($data['products']) < BATCH) break;
        $start += BATCH;
    }

    return $products;
}

// ── Load baseline ────────────────────────────────────────────────────────────
$baseline = [];
if (file_exists(BASELINE_FILE)) {
    $raw = @file_get_contents(BASELINE_FILE);
    if ($raw) $baseline = json_decode($raw, true) ?: [];
}
$baselineExists = !empty($baseline);

// ── Scan products ────────────────────────────────────────────────────────────
$products = fetch_all_products();
$total    = count($products);

if ($total === 0) {
    die('<h2 style="color:red">ERROR: Could not fetch products from API. Check API key and connectivity.</h2>');
}

$flags    = [];
foreach ($products as $p) {
    $reasons = [];

    // Critical: below minimum
    if ($p['price'] > 0 && $p['price'] < MIN_PRICE) {
        $reasons[] = [
            'level'   => 'critical',
            'message' => 'Price €' . number_format($p['price'], 2) . ' is BELOW minimum €' . number_format(MIN_PRICE, 2),
        ];
    }

    // Warning: large change from baseline
    if (isset($baseline[$p['id']])) {
        $base   = (float)$baseline[$p['id']]['price'];
        if ($base > 0 && $p['price'] > 0) {
            $pct = abs($p['price'] - $base) / $base * 100;
            if ($pct > MAX_CHANGE) {
                $dir = $p['price'] > $base ? '↑ increase' : '↓ decrease';
                $reasons[] = [
                    'level'   => 'warning',
                    'message' => number_format($pct, 1) . '% price ' . $dir
                               . ' — was €' . number_format($base, 2)
                               . ', now €' . number_format($p['price'], 2),
                ];
            }
        }
    }

    if (!empty($reasons)) {
        $flags[] = ['product' => $p, 'reasons' => $reasons];
    }
}

// ── Update baseline if no critical flags ─────────────────────────────────────
$hasCritical = false;
foreach ($flags as $f) {
    foreach ($f['reasons'] as $r) {
        if ($r['level'] === 'critical') { $hasCritical = true; break 2; }
    }
}

$baselineUpdated = false;
if (!$hasCritical) {
    $newBaseline = [];
    foreach ($products as $p) {
        $newBaseline[$p['id']] = [
            'price'     => $p['price'],
            'reference' => $p['reference'],
            'name'      => $p['name'],
            'updated'   => date('Y-m-d H:i:s'),
        ];
    }
    if (@file_put_contents(BASELINE_FILE, json_encode($newBaseline, JSON_PRETTY_PRINT))) {
        $baselineUpdated = true;
    }
}

$criticalCount = 0;
$warningCount  = 0;
foreach ($flags as $f) {
    foreach ($f['reasons'] as $r) {
        if ($r['level'] === 'critical') $criticalCount++;
        else $warningCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Price Audit — Murphy Furniture</title>
<style>
  body      { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 24px; color: #333; }
  h1        { color: #c0392b; margin-bottom: 4px; }
  .meta     { color: #888; font-size: .9em; margin-bottom: 24px; }
  .cards    { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
  .card     { background: white; border-radius: 8px; padding: 20px 24px; box-shadow: 0 2px 6px rgba(0,0,0,.08); min-width: 160px; }
  .card .num{ font-size: 2em; font-weight: bold; }
  .card .lbl{ color: #888; font-size: .85em; margin-top: 4px; }
  .green    { color: #27ae60; }
  .red      { color: #c0392b; }
  .orange   { color: #e67e22; }
  .blue     { color: #2980b9; }
  .ok-msg   { background: #d5f5e3; border: 1px solid #27ae60; border-radius: 8px; padding: 20px 24px; font-size: 1.1em; color: #1e8449; }
  table     { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
  th        { background: #c0392b; color: white; padding: 12px 14px; text-align: left; font-size: .9em; }
  td        { padding: 10px 14px; border-bottom: 1px solid #f0f2f5; font-size: .9em; vertical-align: top; }
  tr.critical td { background: #fdecea; }
  tr.warning  td { background: #fef9e7; }
  .badge    { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: .8em; font-weight: bold; color: white; }
  .badge.critical { background: #c0392b; }
  .badge.warning  { background: #e67e22; }
  .baseline { font-size: .85em; background: white; border-radius: 8px; padding: 14px 18px; margin-top: 24px; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
</style>
</head>
<body>

<h1>🔍 Murphy Furniture — Price Audit Report</h1>
<p class="meta">Run: <?= date('d M Y H:i:s') ?> &nbsp;|&nbsp; Prestashop Staging</p>

<div class="cards">
  <div class="card"><div class="num blue"><?= number_format($total) ?></div><div class="lbl">Products scanned</div></div>
  <div class="card"><div class="num <?= $criticalCount > 0 ? 'red' : 'green' ?>"><?= $criticalCount ?></div><div class="lbl">Critical flags<br>(price below €<?= MIN_PRICE ?>)</div></div>
  <div class="card"><div class="num <?= $warningCount > 0 ? 'orange' : 'green' ?>"><?= $warningCount ?></div><div class="lbl">Warnings<br>(>30% change)</div></div>
  <div class="card"><div class="num <?= $baselineExists ? 'blue' : 'orange' ?>"><?= $baselineExists ? number_format(count($baseline)) : '—' ?></div><div class="lbl">Baseline records</div></div>
</div>

<?php if (empty($flags)): ?>
  <div class="ok-msg">✅ All clear — no price issues detected. Baseline updated with <?= number_format($total) ?> products.</div>

<?php else: ?>
  <?php if ($criticalCount > 0): ?>
    <p style="color:#c0392b;font-weight:bold;font-size:1.1em">
      🚨 CRITICAL: <?= $criticalCount ?> product(s) priced below €<?= MIN_PRICE ?>.
      Baseline NOT updated until critical issues are resolved.
    </p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>SKU / Reference</th>
        <th>Product Name</th>
        <th>Current Price</th>
        <th>Issue</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($flags as $f):
        $rowClass = 'warning';
        foreach ($f['reasons'] as $r) { if ($r['level'] === 'critical') { $rowClass = 'critical'; break; } }
      ?>
      <tr class="<?= $rowClass ?>">
        <td><?= $f['product']['id'] ?></td>
        <td><?= htmlspecialchars($f['product']['reference']) ?></td>
        <td><?= htmlspecialchars($f['product']['name']) ?></td>
        <td><strong>€<?= number_format($f['product']['price'], 2) ?></strong></td>
        <td>
          <?php foreach ($f['reasons'] as $r): ?>
            <div><span class="badge <?= $r['level'] ?>"><?= strtoupper($r['level']) ?></span> <?= htmlspecialchars($r['message']) ?></div>
          <?php endforeach; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<div class="baseline">
  <strong>Baseline status:</strong>
  <?php if ($baselineUpdated): ?>
    ✅ Updated today — <?= number_format($total) ?> products stored as new baseline.
  <?php elseif ($hasCritical): ?>
    ⚠️ <span style="color:#c0392b">Not updated</span> — critical price flags must be fixed first.
  <?php elseif (!$baselineExists): ?>
    ℹ️ No baseline exists yet. Run again after confirming all prices are correct to create baseline.
  <?php else: ?>
    ℹ️ Baseline unchanged.
  <?php endif; ?>
  <br><small>Baseline file: <?= BASELINE_FILE ?></small>
</div>

<p style="color:#aaa;font-size:.8em;margin-top:20px">
  Murphy Furniture Product Management System — price_audit.php<br>
  ⚠️ Delete this file after use.
</p>

</body>
</html>
