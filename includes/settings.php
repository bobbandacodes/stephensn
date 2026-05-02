<?php
require_once __DIR__ . '/db.php';

function settings_all(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $cache = [];
    foreach (db()->query("SELECT `key`, `value` FROM settings") as $row) {
        $cache[$row['key']] = $row['value'];
    }
    // Auto-seed if completely empty (first run)
    if (empty($cache)) {
        seed_default_settings();
        foreach (db()->query("SELECT `key`, `value` FROM settings") as $row) {
            $cache[$row['key']] = $row['value'];
        }
    }
    return $cache;
}

/**
 * Read a setting. Falls back to default_settings() if no value or empty,
 * then to the explicit $default.
 */
function setting(string $key, ?string $default = null): ?string {
    $all = settings_all();
    $val = $all[$key] ?? null;
    if ($val !== null && $val !== '') return $val;

    $defs = default_settings();
    if (isset($defs[$key]) && $defs[$key] !== '') return $defs[$key];

    return $default;
}

function setting_image_url(string $key, ?string $fallbackPath = null): ?string {
    $all = settings_all();
    $val = $all[$key] ?? '';
    if ($val) return UPLOAD_URL . '/' . rawurlencode($val);
    return $fallbackPath;
}

function setting_set(string $key, ?string $value): void {
    $stmt = db()->prepare(
        "INSERT INTO settings (`key`, `value`) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)"
    );
    $stmt->execute([$key, $value]);
}

function active_theme(): string {
    $t = setting('theme', 'light-green');
    $allowed = ['light-green', 'dark-gold', 'royal-purple'];
    return in_array($t, $allowed, true) ? $t : 'light-green';
}

function theme_choices(): array {
    return [
        'light-green'  => 'Light Green (bright)',
        'dark-gold'    => 'Dark Gold (classic)',
        'royal-purple' => 'Royal Purple (regal)',
    ];
}

function default_settings(): array {
    return [
        'theme'             => 'light-green',
        'site_name'         => 'Prophet Stephen SN',
        'site_tagline'      => 'The Paragon',
        'site_logo'         => '',
        'favicon'           => '',
        'hero_eyebrow'      => 'Apostolic · Prophetic · Teaching',
        'hero_title'        => "Prophet Stephen SN\nThe Paragon",
        'hero_subtitle'     => 'A voice for this generation — carrying the prophetic mantle, raising disciples, and pastoring Higher Life Church. Encounter God. Walk in identity. Manifest the Kingdom.',
        'hero_image'        => '',
        'about_title'       => 'The Calling',
        'about_body'        => "Prophet Stephen SN — known affectionately as The Paragon — carries an apostolic and prophetic grace for this generation. Through the preached Word, prophetic ministry, and discipleship, he calls believers into authentic encounter with God and equips them to walk in their God-given identity.\n\nHis ministry stands on three pillars: the prophetic, the apostolic, and sound teaching. He believes the Church is being raised in this hour to manifest the Kingdom with both power and order — signs and wonders rooted in Scripture, gifts exercised in love, and disciples formed in the likeness of Christ.\n\nBeyond preaching, Prophet Stephen pastors Higher Life Church, mentors leaders, and writes — a poet at heart whose words give voice to what the Spirit is saying.",
        'about_image'       => '',
        'ministry_title'    => 'Higher Life Church',
        'ministry_body'     => "Higher Life Church is a community of believers pursuing the presence of God, the preaching of the Word, and the equipping of the saints for the work of ministry. We are committed to seeing every member walk in their calling.\n\nExpect worship that ushers in the presence of God, prophetic teaching that addresses both the now and the eternal, and an atmosphere where you are welcomed, seen, and equipped to live a higher life.",
        'ministry_image'    => '',
        'church_day'        => 'Sunday',
        'church_time'       => '8:00 AM',
        'church_venue'      => 'KPF Grand Hall, Parklands',
        'give_message'      => 'We are setting up secure online giving — M-Pesa, bank transfer, and card. In the meantime, please contact us for partnership details.',
        'give_image'        => '',
        'contact_email'     => 'info@stephensn.local',
        'contact_phone'     => '',
        'facebook_url'      => 'https://web.facebook.com/stephen.poet.9',
        'youtube_url'       => '',
        'instagram_url'     => '',
        'footer_tagline'    => 'A voice. A mantle. A generation.',
    ];
}

function seed_default_settings(): void {
    foreach (default_settings() as $k => $v) {
        $stmt = db()->prepare("INSERT IGNORE INTO settings (`key`, `value`) VALUES (?, ?)");
        $stmt->execute([$k, $v]);
    }
}
