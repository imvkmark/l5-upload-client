### 加载本扩展
在 `config/app.php` 的 `providers` 部分加入
```
Imvkmark\L5UploadClient\L5UploadClientServiceProvider::class
```

### 生成配置
- 配置config
如果是需要强制生成配置, 在后边加入 `--force` 选项
```
php artisan vendor:publish --tag=sour-lemon
```

- 获取的配置项目填写到 `l5-upload-client.php`
```
enable        是否启用上传
app_key       上传 public key
app_secret    上传 public secret
expires       过期时间 / 分钟
image_url     上传图片的地址
token_url     获取token的地址
version       版本号
```