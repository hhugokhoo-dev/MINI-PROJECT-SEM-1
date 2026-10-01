<?php
declare(strict_types=1);
session_start();

/* ---------- Config (replace with DB lookups in a real app) ---------- */
$car = [
    'name'  => 'Toyota Camry 2024',
    'daily' => 388,
    'tags'  => ['Automatic', '5 seats', 'Hybrid', 'Unlimited mileage'],
];
$serviceFee = 29;
$deposit    = 3000;
$locations  = ['Hongqiao Airport T2', 'Pudong Airport T1', 'People\'s Square Branch'];
$addons = [
    'insurance' => ['label' => 'Full coverage insurance', 'note' => 'Zero excess on damage and third-party liability', 'price' => 60, 'per_day' => true],
    'gps'       => ['label' => 'GPS navigation',          'note' => '',                                                'price' => 20, 'per_day' => true],
    'child'     => ['label' => 'Child safety seat',       'note' => '',                                                'price' => 15, 'per_day' => true],
    'driver'    => ['label' => 'Additional driver',       'note' => 'One-time fee',                                    'price' => 80, 'per_day' => false],
];

/* ---------- Helpers ---------- */
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function money(int $n): string { return '$' . number_format($n); }
function rentalDays(string $a, string $b): int {
    $d1 = DateTime::createFromFormat('Y-m-d', $a);
    $d2 = DateTime::createFromFormat('Y-m-d', $b);
    return ($d1 && $d2) ? max(0, (int)$d1->diff($d2)->format('%r%a')) : 0;
}
function quote(array $car, array $addons, array $picked, int $days, int $fee): array {
    $rent = $car['daily'] * $days;
    $extra = 0;
    foreach ($picked as $k) {
        if (isset($addons[$k])) {
            $extra += $addons[$k]['price'] * ($addons[$k]['per_day'] ? $days : 1);
        }
    }
    return ['rent' => $rent, 'extra' => $extra, 'fee' => $days ? $fee : 0, 'total' => $days ? $rent + $extra + $fee : 0];
}

if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }

/* ---------- Form state ---------- */
$tomorrow = (new DateTime('+1 day'))->format('Y-m-d');
$in4      = (new DateTime('+4 days'))->format('Y-m-d');
$in = [
    'pickup_loc' => $locations[0], 'return_loc' => $locations[0],
    'pickup_date' => $tomorrow, 'pickup_time' => '10:00',
    'return_date' => $in4,      'return_time' => '10:00',
    'name' => '', 'phone' => '', 'license' => '', 'addons' => [],
];
$errors = [];
$booking = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Invalid request.');
    }
    foreach (['pickup_loc','return_loc','pickup_date','pickup_time','return_date','return_time','name','phone','license'] as $f) {
        $in[$f] = trim((string)($_POST[$f] ?? ''));
    }
    $in['addons'] = array_values(array_intersect(array_keys($addons), (array)($_POST['addons'] ?? [])));

    $days = rentalDays($in['pickup_date'], $in['return_date']);
    if (!in_array($in['pickup_loc'], $locations, true) || !in_array($in['return_loc'], $locations, true)) $errors[] = 'Please choose valid locations.';
    if ($in['pickup_date'] < date('Y-m-d')) $errors[] = 'Pick-up date cannot be in the past.';
    if ($days < 1)                          $errors[] = 'Return date must be after the pick-up date.';
    if ($in['name'] === '')                 $errors[] = 'Driver name is required.';
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $in['phone'])) $errors[] = 'Enter a valid phone number.';
    if (strlen($in['license']) < 5)         $errors[] = 'Enter your driver\'s license number.';

    if (!$errors) {
        $q = quote($car, $addons, $in['addons'], $days, $serviceFee);
        $booking = ['ref' => 'RN-' . strtoupper(bin2hex(random_bytes(3))), 'days' => $days] + $q;
        // TODO: persist booking + redirect to payment gateway here.
    }
}

$days = rentalDays($in['pickup_date'], $in['return_date']);
$q = quote($car, $addons, $in['addons'], $days, $serviceFee);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Confirm your rental</title>
<style>
:root{--bg:#f4f5f7;--card:#fff;--tx:#14171f;--mu:#6b7280;--ln:#e5e7eb;--ac:#e8590c;--ok:#12805c;--er:#c92a2a}
@media (prefers-color-scheme:dark){:root{--bg:#0f1115;--card:#1a1d24;--tx:#f1f3f7;--mu:#9aa3b2;--ln:#2a2f3a;--ok:#3ecf9b;--er:#ff8787}}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--tx);font:15px/1.5 -apple-system,"Segoe UI",Roboto,system-ui,sans-serif}
.wrap{max-width:560px;margin:0 auto;padding:12px 16px 150px}
h1{font-size:18px;margin:8px 0 12px}
.steps{display:flex;gap:6px;margin-bottom:14px;font-size:12px;color:var(--mu)}
.steps div{flex:1;text-align:center;padding-top:8px;border-top:3px solid var(--ln)}
.steps .on{border-color:var(--ac);color:var(--tx);font-weight:600}
.steps .done{border-color:var(--ok);color:var(--ok)}
.card{background:var(--card);border:1px solid var(--ln);border-radius:14px;padding:14px;margin-bottom:12px}
.card h2{font-size:13px;margin:0 0 10px;color:var(--mu);text-transform:uppercase;letter-spacing:.5px}
.car{display:flex;gap:12px;align-items:center}
.car svg{width:96px;flex:none}
.tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:6px}
.tags span{font-size:12px;padding:2px 8px;border-radius:99px;background:var(--bg);color:var(--mu)}
label.f{display:block;font-size:12px;color:var(--mu);margin-bottom:4px}
input[type=text],input[type=tel],input[type=date],input[type=time],select{width:100%;padding:10px;border:1px solid var(--ln);border-radius:10px;background:var(--bg);color:var(--tx);font:inherit}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:10px}.g1{margin-bottom:10px}
.opt{display:flex;gap:10px;align-items:center;padding:10px 0;cursor:pointer}
.opt+.opt{border-top:1px solid var(--ln)}
.opt input{width:20px;height:20px;accent-color:var(--ac);flex:none}
.opt .t{flex:1}.opt small{display:block;color:var(--mu);font-size:12px}.opt .p{font-weight:600;white-space:nowrap}
.sum .row{display:flex;justify-content:space-between;padding:5px 0;color:var(--mu)}
.sum .total{border-top:1px dashed var(--ln);margin-top:6px;padding-top:10px;color:var(--tx);font-size:16px;font-weight:700}
.note{font-size:12px;color:var(--mu);margin-top:6px}
.err{background:color-mix(in srgb,var(--er) 12%,transparent);border:1px solid var(--er);color:var(--er);border-radius:12px;padding:10px 14px;margin-bottom:12px}
.err ul{margin:0;padding-left:18px}
.bar{position:fixed;left:0;right:0;bottom:0;background:var(--card);border-top:1px solid var(--ln);padding:12px 16px calc(12px + env(safe-area-inset-bottom,0px))}
.bar div{max-width:560px;margin:0 auto;display:flex;align-items:center;gap:14px}
.bar small{display:block;color:var(--mu);font-size:12px}.bar b{font-size:20px}
.bar button{flex:1;padding:14px;border:0;border-radius:12px;background:var(--ac);color:#fff;font-size:16px;font-weight:700;cursor:pointer}
.done-box{text-align:center;padding:40px 10px}
.done-box .c{width:64px;height:64px;border-radius:50%;background:var(--ok);color:#fff;font-size:34px;line-height:64px;margin:0 auto 14px}
.done-box p{color:var(--mu)}
</style>
</head>
<body>
<div class="wrap">
<?php if ($booking): ?>
  <div class="done-box">
    <div class="c">✓</div>
    <h1>Booking confirmed</h1>
    <p>Reference <b><?= e($booking['ref']) ?></b><br>
       <?= e($car['name']) ?> · <?= $booking['days'] ?> day<?= $booking['days'] > 1 ? 's' : '' ?> · <?= money($booking['total']) ?><br>
       Please bring your driver's license and ID at pick-up.</p>
  </div>
<?php else: ?>
  <h1>Confirm your rental</h1>
  <div class="steps"><div class="done">① Select car</div><div class="on">② Details</div><div>③ Payment</div><div>④ Done</div></div>

  <?php if ($errors): ?>
    <div class="err"><ul><?php foreach ($errors as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <form method="post" id="f" novalidate>
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

    <div class="card">
      <h2>Selected vehicle</h2>
      <div class="car">
        <svg viewBox="0 0 96 48" fill="none"><path d="M6 32c0-4 2-6 6-7l10-11c2-2 4-3 7-3h24c3 0 5 1 7 3l9 10c6 1 11 3 11 8v3H6z" fill="var(--ac)"/><path d="M26 15h14v10H20zM44 15h12l8 10H44z" fill="var(--card)" opacity=".85"/><circle cx="26" cy="36" r="7" fill="var(--tx)"/><circle cx="72" cy="36" r="7" fill="var(--tx)"/><circle cx="26" cy="36" r="3" fill="var(--card)"/><circle cx="72" cy="36" r="3" fill="var(--card)"/></svg>
        <div>
          <b><?= e($car['name']) ?></b>
          <div style="color:var(--mu);font-size:13px"><?= money($car['daily']) ?> / day</div>
          <div class="tags"><?php foreach ($car['tags'] as $t): ?><span><?= e($t) ?></span><?php endforeach; ?></div>
        </div>
      </div>
    </div>

    <div class="card">
      <h2>Pick-up &amp; return</h2>
      <div class="g1"><label class="f">Pick-up location</label>
        <select name="pickup_loc"><?php foreach ($locations as $l): ?><option <?= $in['pickup_loc'] === $l ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
      <div class="g1"><label class="f">Return location</label>
        <select name="return_loc"><?php foreach ($locations as $l): ?><option <?= $in['return_loc'] === $l ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
      <div class="g2">
        <div><label class="f">Pick-up date</label><input type="date" name="pickup_date" value="<?= e($in['pickup_date']) ?>" min="<?= date('Y-m-d') ?>"></div>
        <div><label class="f">Pick-up time</label><input type="time" name="pickup_time" value="<?= e($in['pickup_time']) ?>"></div>
        <div><label class="f">Return date</label><input type="date" name="return_date" value="<?= e($in['return_date']) ?>" min="<?= date('Y-m-d') ?>"></div>
        <div><label class="f">Return time</label><input type="time" name="return_time" value="<?= e($in['return_time']) ?>"></div>
      </div>
      <div class="note" id="dur"></div>
    </div>

    <div class="card">
      <h2>Driver details</h2>
      <div class="g1"><label class="f">Full name (as on license)</label><input type="text" name="name" value="<?= e($in['name']) ?>" autocomplete="name"></div>
      <div class="g2">
        <div><label class="f">Phone</label><input type="tel" name="phone" value="<?= e($in['phone']) ?>" autocomplete="tel"></div>
        <div><label class="f">License number</label><input type="text" name="license" value="<?= e($in['license']) ?>"></div>
      </div>
    </div>

    <div class="card">
      <h2>Extras</h2>
      <?php foreach ($addons as $k => $a): ?>
        <label class="opt">
          <input type="checkbox" name="addons[]" value="<?= e($k) ?>" <?= in_array($k, $in['addons'], true) ? 'checked' : '' ?>>
          <span class="t"><?= e($a['label']) ?><?php if ($a['note']): ?><small><?= e($a['note']) ?></small><?php endif; ?></span>
          <span class="p"><?= money($a['price']) ?><?= $a['per_day'] ? '/day' : '' ?></span>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="card sum">
      <h2>Price summary</h2>
      <div class="row"><span id="l1"></span><span id="v1"></span></div>
      <div class="row"><span>Extras</span><span id="v2"></span></div>
      <div class="row"><span>Service fee</span><span id="v3"></span></div>
      <div class="row total"><span>Total</span><span id="v4"></span></div>
      <div class="note">A refundable <?= money($deposit) ?> deposit is pre-authorized at pick-up and released within 7 business days of return. It is not included in the total.</div>
    </div>

    <div class="bar"><div>
      <div><small>Total</small><b id="bt"><?= money($q['total']) ?></b></div>
      <button type="submit">CONFIRM BOOKING</button>
    </div></div>
  </form>

  <script>
  // Live preview only — the server recalculates the price on submit.
  const P = <?= json_encode(['daily' => $car['daily'], 'fee' => $serviceFee, 'addons' => $addons], JSON_HEX_TAG | JSON_HEX_AMP) ?>;
  const f = document.getElementById('f'), $ = id => document.getElementById(id);
  const m = n => '$' + n.toLocaleString('en-US');
  function calc() {
    const n = Math.max(0, Math.round((new Date(f.return_date.value) - new Date(f.pickup_date.value)) / 864e5)) || 0;
    let extra = 0;
    f.querySelectorAll('input[name="addons[]"]:checked').forEach(c => {
      const a = P.addons[c.value]; extra += a.price * (a.per_day ? n : 1);
    });
    const rent = P.daily * n, fee = n ? P.fee : 0, tot = n ? rent + extra + fee : 0;
    $('dur').textContent = n ? `${n} day${n > 1 ? 's' : ''} rental` : 'Return date must be after pick-up date';
    $('l1').textContent = `Rental (${m(P.daily)} × ${n} day${n === 1 ? '' : 's'})`;
    $('v1').textContent = m(rent); $('v2').textContent = m(extra); $('v3').textContent = m(fee);
    $('v4').textContent = $('bt').textContent = m(tot);
  }
  f.addEventListener('input', calc); f.addEventListener('change', calc); calc();
  </script>
<?php endif; ?>
</div>
</body>
</html>
