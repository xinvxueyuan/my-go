# CC许可证生成器部署指南

本文档详细说明如何在Ubuntu 22.04服务器上部署CC许可证生成系统。

## 前置条件

确保你有以下权限和信息：
- 可以访问Ubuntu 22.04服务器的root权限或sudo权限
- 域名（可选，如果需要配置SSL证书）

## 安装步骤

### 1. 更新系统包

```bash
sudo apt update
sudo apt upgrade -y
```

### 2. 安装必要的软件包

```bash
sudo apt install -y nginx php8.1-fpm php8.1-sqlite3 php8.1-xml php8.1-mbstring composer git unzip
```

### 3. 克隆项目

```bash
cd /var/www
git clone [项目地址] cc-license
cd cc-license
chown -R www-data:www-data .
chmod -R 755 .
```

### 4. 安装PHP依赖

```bash
composer install --no-dev --optimize-autoloader
```

### 5. 创建必要的目录并设置权限

```bash
mkdir -p public/pdf
chown -R www-data:www-data public/pdf
chmod -R 755 public/pdf
```

### 6. 配置Nginx

创建Nginx配置文件：

```bash
sudo nano /etc/nginx/sites-available/cc-license
```

添加以下配置：

```nginx
server {
    listen 80;
    server_name your-domain.com;  # 替换为你的域名
    root /var/www/cc-license/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

启用站点配置：

```bash
sudo ln -s /etc/nginx/sites-available/cc-license /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 7. 配置SSL证书（可选）

如果需要HTTPS，可以使用Let's Encrypt：

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 8. 测试部署

访问你的域名或服务器IP，系统应该已经可以正常运行。

## 维护说明

### 更新系统

```bash
cd /var/www/cc-license
git pull
composer install --no-dev --optimize-autoloader
chown -R www-data:www-data .
```

### 日志查看

- Nginx错误日志：`/var/log/nginx/error.log`
- PHP-FPM错误日志：`/var/log/php8.1-fpm.log`

### 重启服务

```bash
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

## 故障排查

1. 检查Nginx状态：
```bash
sudo systemctl status nginx
```

2. 检查PHP-FPM状态：
```bash
sudo systemctl status php8.1-fpm
```

3. 检查文件权限：
```bash
ls -la /var/www/cc-license
```

4. 检查错误日志：
```bash
tail -f /var/log/nginx/error.log
```