<?php
// get-chatlogs.php - Returns list of all saved chatlogs

header('Content-Type: application/json');

$chatlogsDir = __DIR__ . '/chatlogs';

// Create directory if it doesn't exist
if (!is_dir($chatlogsDir)) {
    mkdir($chatlogsDir, 0755, true);
    echo json_encode([]);
    exit;
}

$chatlogs = [];

// Scan directory for chatlog files
$files = scandir($chatlogsDir);
foreach ($files as $file) {
    if ($file === '.' || $file === '..' || $file === '.gitkeep') continue;
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') continue;

    $filePath = $chatlogsDir . '/' . $file;
    $content = json_decode(file_get_contents($filePath), true);

    if (!$content || empty($content['messages'])) continue;

    $conversationId = pathinfo($file, PATHINFO_FILENAME);
    $messages = $content['messages'];
    
    // Count student messages only (odd-indexed are student messages)
    $messageCount = count(array_filter($messages, function($msg, $idx) {
        return $idx % 2 === 0; // Student messages
    }, ARRAY_FILTER_USE_BOTH));

    $chatlogs[] = [
        'conversationId' => $conversationId,
        'suspect' => $content['suspect'] ?? 'Unknown',
        'messageCount' => $messageCount,
        'firstMessage' => $messages[0]['timestamp'] ?? null,
        'lastMessage' => end($messages)['timestamp'] ?? null,
        'file' => $file
    ];
}

// Sort by most recent first
usort($chatlogs, function($a, $b) {
    $timeA = strtotime($a['lastMessage'] ?? '2000-01-01');
    $timeB = strtotime($b['lastMessage'] ?? '2000-01-01');
    return $timeB - $timeA;
});

echo json_encode($chatlogs);
?>