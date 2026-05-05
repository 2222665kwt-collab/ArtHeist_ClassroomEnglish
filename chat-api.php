<?php
// chat-api.php - Backend for generating suspect responses

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['suspect']) || !isset($input['userMessage'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$suspect = $input['suspect'];
$userMessage = strtolower($input['userMessage']);

// Get response based on suspect and user message
$response = generateResponse($suspect, $userMessage);

echo json_encode(['response' => $response]);

function generateResponse($suspect, $userMessage) {
    // Try to use Ollama if available
    $ollamaResponse = tryOllama($suspect, $userMessage);
    if ($ollamaResponse) {
        return $ollamaResponse;
    }

    // Fall back to rule-based responses
    return getRuleBasedResponses($suspect, $userMessage);
}

function tryOllama($suspect, $userMessage) {
    // Check if Ollama is running locally
    $ollamaUrl = 'http://localhost:11434/api/generate';
    
    $characterPrompt = getCharacterPrompts($suspect);
    
    $payload = [
        'model' => 'mistral',
        'prompt' => "$characterPrompt\n\nStudent: $userMessage\n\nSuspect:",
        'stream' => false,
        'temperature' => 0.7
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $ollamaUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 2,
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['response'])) {
            $text = trim($result['response']);
            // Clean up the response
            $text = preg_replace('/\n+/', ' ', $text);
            $text = substr($text, 0, 300); // Limit length
            return $text;
        }
    }

    return null;
}

function getCharacterPrompts($suspect) {
    $prompts = [
        'Margot Fleischer' => "You are Margot Fleischer, a 29-year-old gallery assistant at Hartwell Gallery. You are nervous, friendly but evasive about certain topics. You have worked there for 18 months and know alarm codes. You have money problems. Use simple English, 2-4 sentences max. When uncomfortable, use phrases like 'Um... I mean...' or 'I'm not sure.' Do not confess.",
        
        'Victor Hale' => "You are Victor Hale, a 54-year-old art dealer and owner of Hale & Sons Fine Art. You are confident, formal, and serious. You do not enjoy being questioned. You tried to buy the painting 'Girl in Blue' three times but were rejected. Use simple, clear English, 2-4 sentences max. Say things like 'I don't see why that's relevant' or 'Let's keep this professional.'",
        
        'Daniela Reyes' => "You are Daniela Reyes, a 41-year-old night security guard with 12 years of experience. You are calm, honest, and cooperative. You want to help solve the case. Use simple, direct sentences. You know about a blue car and a woman near the side entrance around 8:50 PM. Keep answers to 2-4 sentences. You are warm and patient."
    ];

    return $prompts[$suspect] ?? "You are a suspect in an art theft investigation.";
}

function getRuleBasedResponses($suspect, $userMessage) {
    if ($suspect === 'Margot Fleischer') {
        return getMargotResponse($userMessage);
    } elseif ($suspect === 'Victor Hale') {
        return getVictorResponse($userMessage);
    } elseif ($suspect === 'Daniela Reyes') {
        return getDanielaResponse($userMessage);
    }

    return "I'm sorry, I don't understand. Can you ask me something else?";
}

function getMargotResponse($userMessage) {
    // Check keywords
    if (containsAny($userMessage, ['leave', 'left', 'when did you', 'time', 'went home'])) {
        return "Um... I think I left around 6 PM. I was tired and wanted to go home. Is that important?";
    }
    
    if (containsAny($userMessage, ['thursday', 'night of', 'that night', 'evening'])) {
        return "I... I was at home watching a film. Yes, that's right. I had a quiet night in.";
    }
    
    if (containsAny($userMessage, ['grand budapest', 'film', 'movie', 'watch'])) {
        return "Um... it was about... a hotel? I think. It was quite long. Can we talk about something else?";
    }
    
    if (containsAny($userMessage, ['car', 'drive', 'fiat', 'blue'])) {
        return "A car? No, I don't own a car. I take the bus everywhere. Why are you asking?";
    }
    
    if (containsAny($userMessage, ['marco', 'boyfriend', 'ex'])) {
        return "Marco? I... I haven't seen him in a long time. We're not together anymore. Please, I don't want to talk about this.";
    }
    
    if (containsAny($userMessage, ['money', 'owe', 'debt', 'problem'])) {
        return "I... well... everyone has money problems sometimes, right? It's nothing to do with the gallery.";
    }
    
    if (containsAny($userMessage, ['painting', 'girl in blue', 'theft', 'stolen'])) {
        return "I don't know anything about that painting. I just work there. I didn't see anything unusual that night.";
    }
    
    if (containsAny($userMessage, ['alarm', 'security', 'code', 'key'])) {
        return "I... we all know the codes. It's part of my job. But I didn't do anything wrong!";
    }
    
    if (containsAny($userMessage, ['guilty', 'did you', 'you stole'])) {
        return "What?! No! I didn't do anything! I don't know what you're talking about!";
    }
    
    return randomResponse([
        "Um... can you repeat that? I'm a bit nervous.",
        "I'm not sure what you mean. Sorry.",
        "Why are you asking me all these questions? Am I in trouble?",
        "I... I don't think that's important.",
        "Can we talk about something else?"
    ]);
}

function getVictorResponse($userMessage) {
    if (containsAny($userMessage, ['thursday', 'night', 'where were you', 'alibi'])) {
        return "I was at my private club, The Northgate, on Thursday evening. That's all you need to know.";
    }
    
    if (containsAny($userMessage, ['northgate', 'club', 'verify', 'confirm'])) {
        return "I don't see why that's relevant. It's a private matter. Let's keep this professional.";
    }
    
    if (containsAny($userMessage, ['gallery', 'visit', 'april', 'near'])) {
        return "I was walking past the gallery. No reason to stop. I do business in this area regularly.";
    }
    
    if (containsAny($userMessage, ['painting', 'girl in blue', 'try to buy', 'offer'])) {
        return "Yes, I tried to purchase that painting. Three times. They refused. But that has nothing to do with the theft.";
    }
    
    if (containsAny($userMessage, ['phone call', '9:45', 'call that evening'])) {
        return "I received a business call. That's all. It was not related to this matter.";
    }
    
    if (containsAny($userMessage, ['meeting', 'secret', 'hotel', 'rival'])) {
        return "I've already answered your questions. I don't see how this helps. I did not steal the painting.";
    }
    
    if (containsAny($userMessage, ['photo', 'picture', 'april 10'])) {
        return "I took some photos at the private event. It was a professional interest. That's normal for a dealer.";
    }
    
    if (containsAny($userMessage, ['guilty', 'you did', 'stole'])) {
        return "That's ridiculous. I did not steal that painting. I have a legitimate business to run.";
    }
    
    return randomResponse([
        "I've already answered that. Please, let's keep this professional.",
        "I don't see why that's relevant.",
        "I've already answered your questions.",
        "That's not important. Is there anything else?",
        "I'm not sure what you're implying, but I assure you I did nothing wrong."
    ]);
}

function getDanielaResponse($userMessage) {
    if (containsAny($userMessage, ['thursday', 'night', 'where', 'during'])) {
        return "I was here at the gallery. I'm the night security guard, so I work Thursday evenings. I was on duty that night.";
    }
    
    if (containsAny($userMessage, ['leave', 'left', 'why did you', 'when'])) {
        return "At 9:08 PM, I got an alarm alert for the storage unit on Brent Road. I had to check it. I followed correct procedure.";
    }
    
    if (containsAny($userMessage, ['storage', 'brent road', 'alarm'])) {
        return "The alarm went off at the unit. I drove there and arrived at 9:25 PM. I stayed until 10:50 PM. There was nothing wrong.";
    }
    
    if (containsAny($userMessage, ['false alarm', 'fake', 'trap'])) {
        return "I know how it looks. But I was doing my job. I didn't know the alarm was fake until later.";
    }
    
    if (containsAny($userMessage, ['computer', 'system', 'inside'])) {
        return "Yes, I discovered the alarm came from the gallery's own computer system. Not from the storage unit. Someone sent it intentionally.";
    }
    
    if (containsAny($userMessage, ['see', 'notice', 'unusual', 'before'])) {
        return "Just before I left, I saw a blue car in the side street. And a young woman near the side entrance. She waved at me. I thought she was a staff member.";
    }
    
    if (containsAny($userMessage, ['woman', 'hair', 'description'])) {
        return "She had short dark hair. She looked young, maybe in her twenties? She seemed friendly. I didn't think anything was wrong at the time.";
    }
    
    if (containsAny($userMessage, ['car', 'blue', 'fiat'])) {
        return "It was a small blue car. Maybe a Fiat? I'm not certain of the exact model. It was parked nearby.";
    }
    
    if (containsAny($userMessage, ['guilty', 'you did', 'you're the thief'])) {
        return "I didn't steal anything! I was tricked. I know it looks bad, but I was just doing my job correctly.";
    }
    
    if (containsAny($userMessage, ['pay', 'money', 'upset', 'happy'])) {
        return "I'm not happy with my pay, but that's not relevant. I would never steal. I'm a professional.";
    }
    
    return randomResponse([
        "I'm trying to help you. Please, ask me specific questions.",
        "I know this looks suspicious, but I'm telling you the truth.",
        "What do you want to know? I'll help however I can.",
        "I was doing my job. I didn't know what was happening.",
        "Ask me something specific and I'll answer honestly."
    ]);
}

function containsAny($text, $keywords) {
    foreach ($keywords as $keyword) {
        if (strpos($text, strtolower($keyword)) !== false) {
            return true;
        }
    }
    return false;
}

function randomResponse($responses) {
    return $responses[array_rand($responses)];
}
?>