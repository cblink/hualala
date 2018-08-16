# hualala-sdk

## 实例化

```php
use Cblink\Hualala\Hualala;
use Cblink\Hualala\HualalaOptions;

return new Hualala([
    HualalaOptions::DEBUG => true,
    HualalaOptions::DEVELOPER_ID => 123,
    HualalaOptions::DEVELOPER_PASSWORD => 'xxx',
    HualalaOptions::MERCHANT_ID => 456,
    HualalaOptions::MERCHANT_PASSWORD => 'xxx',
    HualalaOptions::GROUP_ID => 789,
    HualalaOptions::AES_KEY => 'xxxxxxxxxxxxxxxx',
]);
```
