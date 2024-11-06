/** pushサーバーから通知が来たときに発火するevent */
self.addEventListener('push', (event) => {
    const msg = event.data.json();
    const options = {
        icon: '/test.png', // ← ※必要ファイルには含めませんでしたが、push通知 に画像を設定できます。
        body: msg.body,
        data: {
            url: msg.url
        },
    };
    // デスクトップ通知を表示する
    self.registration.showNotification(msg.title, options);
});
