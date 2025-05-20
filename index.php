<?php

$page_access_token = 'EAAOYVU4pXbIBO34EdkFHnI9k4lecHrPRnZCCIZC39yn4ntZCE8P3ZAWIZAZCj4CrGepMwIGQvgBjWSdOl1GvYLsS4Yu41FSLyb62fAlXNVBkPcw60nLDF1jDWxkwGxn0z2dqQcCE2QSQWG992AZA9qG1MJVx7CtHQ2fVkgEpZCMNfp4jyDjvqyM3uJeXwCgpgaFypo1FZBLmk2gfZBFU7x0vGsT8khIrkZD';
$target_post_id = '2795596103899662'; // <-- Your target post ID

// 1. Get latest comments from the specific post
$comment_api = "https://graph.facebook.com/{$target_post_id}/comments?fields=id,message,from&access_token={$page_access_token}";
$comments_json = file_get_contents($comment_api);
$comments = json_decode($comments_json, true);

// Loop through each comment
foreach ($comments['data'] as $comment) {
    $comment_id = $comment['id'];
    $user_id = $comment['from']['id'];
    $user_name = $comment['from']['name'];
    $message = $comment['message'];

    // OPTIONAL: Store comment IDs in DB to avoid duplicate responses

    // 2. Send inbox message
    $send_message_url = "https://graph.facebook.com/v18.0/me/messages?access_token={$page_access_token}";
    $payload = [
        'recipient' => [ 'id' => $user_id ],
        'message' => [ 'text' => "Hi $user_name, thanks for your comment: \"$message\". Here's the info you asked for!" ],
        'messaging_type' => 'RESPONSE'
    ];
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => json_encode($payload)
        ]
    ];
    $context = stream_context_create($options);
    file_get_contents($send_message_url, false, $context);

    // 3. Reply to the comment
    $reply_url = "https://graph.facebook.com/{$comment_id}/comments";
    $reply_data = [
        'message' => 'Check inbox 😊',
        'access_token' => $page_access_token
    ];
    $reply_options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($reply_data)
        ]
    ];
    $reply_context = stream_context_create($reply_options);
    file_get_contents($reply_url, false, $reply_context);
}

?>
