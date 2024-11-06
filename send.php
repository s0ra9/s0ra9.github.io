<?php
// autoload.php を読み込んだら、vendor下のクラスをインスタンス化できるようにする
require_once './vendor/autoload.php';
// json形式でpostされた値を取得して配列化する
$r_post = json_decode(file_get_contents('php://input'), true);
$webPush = new \Minishlink\WebPush\WebPush([
    'VAPID' => [
        'subject' => 'http://localhost/',
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
    'endpoint' => 'https://fcm.googleapis.com/fcm/send/eBB46DgWeck:APA91bEkk0R0gf0V_g40_E_w9ja32n7T4-fU_X6S55JI8LSXT7DKRVCt4FXxDCugzmBUUu_yaMbxr1sgYBGf282k4MvpfLC66o3Q3k7hY-5OHS_uHF5aQayTM3ZP4EEW7k8JNCzva_qY',
    // ↓検証ツール > console に表示された push_public_key を入力
    'publicKey' => 'BK1JRytbQOJ4yTif5Ie/J9muCll5I3jwc7y+lCceKcQbR3KKrf+RZB3CBpNYGpYK9ry/k+RTHlH1oqSSBWhoGfc=',
    // ↓検証ツール > console に表示された push_auth_token を入力
    'authToken' => 'gjTFFT1rGjHGscQBRqpiCQ==',
]);
// pushサーバーに通知リクエストをjson形式で送る
$report = $webPush->sendOneNotification(
    $subscription,
    json_encode([
        'title' => 'タイトル',
        'body' => 'PUSH通知のテストです',
        'url' => 'http://localhost/',
    ])
);
$r_response = [
    'status' => 200,
    'body' => '送信' . (($report->isSuccess()) ? '成功' : '失敗')
];
echo json_encode($r_response);