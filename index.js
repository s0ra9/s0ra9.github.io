window.addEventListener('DOMContentLoaded', () => {
    /** 通知を送るボタンがクリックされたら発火 */
    const $notify_btn = document.getElementById('notify');
    $notify_btn.addEventListener('click', async function () {
        // 通知を送るphpファイルにリクエストを投げる
        const result = await fetch('./send.php')
            .then((response) => response.json())
            .then((result) => result)
            .catch(() => console.error('通知の送信に失敗しました。'))
        console.log(result.body);
    });
});
/** Service Worker の登録 */
if ('serviceWorker' in navigator) {
    // Service Workerをファイルを読み込んでブラウザに登録する
    // service-worker.js は必ずドキュメントルート直下に置くことと、scope はめちゃくちゃ重要です！
    navigator.serviceWorker.register('service-worker.js' )
    .then(() => console.log('serviceWorker を登録しました。'))
} else {
    console.log('serviceWorker に対応していません。')
}
/** Service Worker の準備が出来たら発火 */
navigator.serviceWorker.ready.then((registration) => {
    // pushサーバーに当ブラウザの存在を知らせ、認証を受ける
    registration.pushManager.getSubscription().then((subscription) => {
        // ↓後述で生成する公開鍵の文字列を入力する
        let server_public_key = "BCaGjW2Ng4iheEakSH_uSlIjoGvAfbUNGhcTogDGbOaGIIluHdqPacqXDl90rVoR1NLqYVBlmIlzsh-4mrVqa7M";
        // 呪文のような関数を用いて、公開鍵をpushサーバーが要求する認証用キーにフォーマットする
        server_public_key = urlBase64ToUint8Array(server_public_key);
        // pushサーバーに認証リクエストを送る
        registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: server_public_key
        }).then((subscription) => {
            // ブラウザに割り当てられているtokenを含んだpushサーバーのURLが取得できる
            const endpoint = subscription.endpoint;
            console.log('endpoint: ' + endpoint)
            // pushサーバーの公開鍵が取得できる
            const rawKey = subscription.getKey('p256dh');
            const push_public_key = rawKey ? btoa(String.fromCharCode.apply(null, new Uint8Array(rawKey))) : '';
            console.log('push_public_key: ' + push_public_key)
            // pushサーバーへリクエストを送る際のtokenが取得できる
            const rawAuthSecret = subscription.getKey ? subscription.getKey('auth') : '';
            const push_auth_token = rawAuthSecret ? btoa(String.fromCharCode.apply(null, new Uint8Array(rawAuthSecret))) : '';
            console.log('push_auth_token: ' + push_auth_token)
        }).catch(e => console.log('pushサーバーへの登録に失敗しました。'));
    })
})
/** サーバー公開鍵をpushサーバーが求める方式にフォーマットする */
function urlBase64ToUint8Array(server_public_key) {
    const padding = '='.repeat((4 - server_public_key.length % 4) % 4);
    const base64 = (server_public_key + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}
