<?php
// autoload.php を読み込んだら、vendor下のクラスをインスタンス化できるようにする
require_once './vendor/autoload.php';
// json形式でpostされた値を取得して配列化する
$r_post = json_decode(file_get_contents('php://input'), true);
$webPush = new \Minishlink\WebPush\WebPush([
    'VAPID' => [
        'subject' => 'https://s0ra9.github.io/',
        // ↓生成した公開鍵文字列を入れる
        'publicKey' => 'BCaGjW2Ng4iheEakSH_uSlIjoGvAfbUNGhcTogDGbOaGIIluHdqPacqXDl90rVoR1NLqYVBlmIlzsh-4mrVqa7M',
        // ↓生成した秘密鍵文字列を入れる
        'privateKey' => '0DrXYmnbW3vO_2FDv92QHlOyKnYyV5EOLXuVnqMX0Fo',
        // ↑２つのようにもちろんこういったハードコーディングはよくないが、そこらへんはよしなに。。。
    ]
]);
// push通知認証用のデータ
$subscription = \Minishlink\WebPush\Subscription::create([
    // ↓検証ツール > console に表示された endpoint URL を入力
    'endpoint' => 'https://fcm.googleapis.com/fcm/send/cIMRBkVqxEU:APA91bG1sC6o_4lkqx0vQUH3Xv5FFTu7hgxiQtC6PcPOkBYDeTteECNrQKULR3_rB3kRJMAxBqdgCfchBmDklyuYJ6ErJ17BBU96BFx71l7KbipIRQi8vW83hviFz242h8aJWx4XR1Yw',
    // ↓検証ツール > console に表示された push_public_key を入力
    'publicKey' => 'BJB+LiTqUJvseFW0EOh+BLUVnwQloZQn6nxOcaHfwGhJ/ddIflTELOULG8cjG3NjrYAFOcGknsFMFfPzjAuXyPc=',
    // ↓検証ツール > console に表示された push_auth_token を入力
    'authToken' => 'ZVp3EehDgwed7eDlyknlnA==',
]);
// pushサーバーに通知リクエストをjson形式で送る
$report = $webPush->sendOneNotification(
    $subscription,
    json_encode([
        'title' => 'こんにちは',
        'body' => 'PUSH通知のテストです',
        'url' => 'https://s0ra9.github.io/',
    ])
);
$r_response = [
    'status' => 200,
    'body' => '送信' . (($report->isSuccess()) ? '成功' : '失敗')
];
echo json_encode($r_response);
