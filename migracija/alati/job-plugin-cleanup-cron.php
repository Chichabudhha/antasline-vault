<?php
// Uklanja osirotele cron hook-ove (porto_send_statistics_info, mwp_update_public_keys)
// iz 'cron' opcije nakon uklanjanja porto-functionality i worker (ManageWP) plagina.
// Radi direktno preko mysqli (bez punog WP bootstrap-a, brze i pouzdanije za ovaj usko-namenski upis).

$mysqli = new mysqli('localhost', 'root', '', 'antasline_local');
if ($mysqli->connect_errno) {
    fwrite(STDERR, "DB konekcija neuspesna: {$mysqli->connect_error}\n");
    exit(1);
}

$res = $mysqli->query("SELECT option_value FROM wpgs_options WHERE option_name='cron'");
$row = $res->fetch_assoc();
$cron = unserialize($row['option_value']);

$targets = ['porto_send_statistics_info', 'mwp_update_public_keys'];
$removed = [];

foreach ($cron as $ts => $hooks) {
    if (!is_array($hooks)) continue;
    foreach ($targets as $hook) {
        if (isset($hooks[$hook])) {
            unset($cron[$ts][$hook]);
            $removed[] = $hook;
        }
    }
    if (isset($cron[$ts]) && is_array($cron[$ts]) && empty($cron[$ts])) {
        unset($cron[$ts]);
    }
}

$new_value = serialize($cron);
$stmt = $mysqli->prepare("UPDATE wpgs_options SET option_value=? WHERE option_name='cron'");
$stmt->bind_param('s', $new_value);
$stmt->execute();

echo "Uklonjeno cron hook-ova: " . count($removed) . " (" . implode(', ', $removed) . ")\n";
echo "Redova pogodjeno: " . $stmt->affected_rows . "\n";
